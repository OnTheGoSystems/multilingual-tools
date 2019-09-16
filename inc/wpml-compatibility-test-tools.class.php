<?php

class WPML_Compatibility_Test_Tools extends WPML_Compatibility_Test_Tools_Base {

	public function __construct() {
		parent::__construct();

		add_action( 'init', array( $this, 'init' ) );
	}

	public function init() {

		// Generate XML
		if ( isset( $_POST['wctt-generator-submit'] ) && check_admin_referer( 'wctt-generate', '_wctt_mighty_nonce' ) ) {
			add_action( 'wp_loaded', array( $this, 'generate_xml' ) );
		}

		// Check for WPML
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				add_action( 'admin_notices', array( $this->messages, 'no_wpml_notice' ) );
			}

			return false;
		}

		// Check for Translation Management
		if ( ! defined( 'WPML_TM_VERSION' ) ) {
			add_action( 'admin_notices', array( $this->messages, 'no_tm_notice' ) );

			return false;
		}

		// Check for String Translation
		if ( ! defined( 'WPML_ST_VERSION' ) ) {
			add_action( 'admin_notices', array( $this->messages, 'no_st_notice' ) );

			return false;
		}

		// WPML setup has to be finished
		global $sitepress;
		if ( ! isset( $sitepress ) ) {
			add_action( 'admin_notices', array( $this->messages, 'no_wpml_notice' ) );

			return false;
		}

		if ( method_exists( $sitepress, 'get_setting' ) && ! $sitepress->get_setting( 'setup_complete' ) ) {
			add_action( 'admin_notices', array( $this->messages, 'not_finished_wpml_setup' ) );

			return false;
		}

		self::install();

		add_action( 'admin_menu', array( $this, 'register_administration_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_styles' ) );
		add_action( 'wp_ajax_generate_strings_translations_action', array( $this, 'generate_strings_translations' ) );

		// Handle admin settings page
		$this->process_request();

		// Change WPML behaviour based on selected settings
		$this->modify_wpml_behaviour();

		do_action( 'mltools_loaded' );

		return true;
	}

	/**
	 * Process admin settings page requests
	 *
	 * @return bool
	 */
	public function process_request() {
		$this->process_strings_auto_translate_action_translate();
		$this->process_save_duplicate_strings_to_translate();
		$this->process_save_shortcode_helper_settings();

		return true;
	}

	/**
	 * Process action strings_auto_translate_action_translate
	 *
	 * @return bool
	 */
	private function process_strings_auto_translate_action_translate() {
		if ( isset( $_POST['strings_auto_translate_action_save'] ) || isset( $_POST['strings_auto_translate_action_translate'] ) ) {

			$error = false;

			$contexts  = ( isset( $_POST['strings_auto_translate_context'] ) ) ? $_POST['strings_auto_translate_context'] : '';
			$languages = ( isset( $_POST['active_languages'] ) ) ? $_POST['active_languages'] : array();
			$template  = ( isset( $_POST['strings_auto_translate_template'] ) ) ? $_POST['strings_auto_translate_template'] : '';

			if ( empty( $template ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_template_notice' ) );
				$error = true;
			}

			if ( $error ) {
				return false;
			}

			self::update_option( 'string_auto_translate_context', $contexts );
			self::update_option( 'string_auto_translate_languages', $languages );
			self::update_option( 'string_auto_translate_template', $template );

			add_action( 'admin_notices', array( $this->messages, 'settings_updated_notice' ) );

			$contexts  = self::get_option( 'string_auto_translate_context' );
			$languages = self::get_option( 'string_auto_translate_languages' );
			$template  = self::get_option( 'string_auto_translate_template' );

			if ( empty( $languages ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_selected_language_notice' ) );
				$error = true;
			}

			if ( empty( $template ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_template_notice' ) );
				$error = true;
			}

			if ( empty( $contexts ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_context_notice' ) );
				$error = true;
			}

			if ( $error ) {
				return false;
			}
		}

		return true;
	}

	/**
	 * Auto translate strings with given context
	 *
	 * @param $context
	 * @param $languages
	 * @param $template
	 */
	private function translate_strings( $strings, $languages, $template ) {
		// For each string add information
		foreach ( $strings as $v ) {
			foreach ( $languages as $lang ) {
				icl_add_string_translation( $v->id, $lang, wpml_ctt_prepare_string( $template, $v->value, $lang ), ICL_STRING_TRANSLATION_COMPLETE );
				icl_update_string_status( $v->id );
			}
		}
	}

	public function generate_strings_translations() {
		check_ajax_referer( 'mt_generate_strings_translations', '_mt_mighty_nonce' );

		$contexts  = isset( $_POST['contexts'] ) ? (array) $_POST['contexts'] : false;
		$languages = isset( $_POST['languages'] ) ? $_POST['languages'] : false;
		$template  = isset( $_POST['template'] ) ? $_POST['template'] : false;
		$count     = isset( $_POST['count'] ) ? $_POST['count'] : false;
		$offset    = isset( $_POST['offset'] ) ? $_POST['offset'] : 0;

		// Check in case JS fail.
		if ( ! $contexts || ! $languages || ! $template ) {
			wp_send_json( 0 );
		}

		// Strings batch threshold
		$limit = 100;

		global $wpdb;

		$esc_contexts = array_map( function ( $context ) {
			return "'" . esc_sql( $context ) . "'";
		}, $contexts );
		$esc_contexts = implode( ",", $esc_contexts );

		// Skip count if process started.
		if ( $count === false ) {
			$count = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}icl_strings WHERE context IN ({$esc_contexts})" );

			// Update settings only on first run.
			self::update_option( 'string_auto_translate_context', $contexts );
			self::update_option( 'string_auto_translate_languages', $languages );
			self::update_option( 'string_auto_translate_template', $template );
		}

		if ( $offset <= $count ) {
			$strings = $wpdb->get_results( "SELECT id, language, context, value FROM {$wpdb->prefix}icl_strings WHERE context IN ({$esc_contexts}) LIMIT {$offset}, {$limit}" );
			$this->translate_strings( $strings, $languages, $template );

			// Update offset.
			$offset += $limit;

			// Calculate progress percentage.
			$strings_left_count = max( $count - $offset, 0 );
			$progress           = floor( 100 - $strings_left_count * 100 / $count );

			wp_send_json( array(
				'offset'   => $offset,
				'count'    => $count,
				'progress' => $progress
			) );
		} else {
			wp_send_json( 1 );
		}
	}

	/**
	 * Process action save_duplicate_strings_to_translate
	 */
	private function process_save_duplicate_strings_to_translate() {
		if ( isset( $_POST['save_duplicate_strings_to_translate'] ) ) {

			$error = false;

			$strings  = ( isset( $_POST['duplicate_strings_to_translate'] ) ) ? $_POST['duplicate_strings_to_translate'] : array();
			$template = ( isset( $_POST['duplicate_strings_template'] ) ) ? $_POST['duplicate_strings_template'] : '';

			if ( empty( $template ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_template_notice' ) );
				$error = true;
			}

			if ( $error ) {
				return false;
			}

			self::update_option( 'duplicate_strings', $strings );
			self::update_option( 'duplicate_strings_template', $template );

			if ( ! empty( $strings ) ) {
				add_action( 'admin_notices', array( $this->messages, 'duplicate_strings_available' ) );
			} else {
				add_action( 'admin_notices', array( $this->messages, 'settings_updated_notice' ) );
			}
		}

		return true;
	}

	private function process_save_shortcode_helper_settings() {

		if ( isset( $_POST['_mltools_shortcode_helper_nonce'] )
		     && wp_verify_nonce( $_POST['_mltools_shortcode_helper_nonce'], 'mltools_shortcode_helper_settings_save' ) ) {

			if ( isset( $_POST['shortcode_debug_action_save'] ) ) {

				$enable = isset( $_POST['shortcode_enable_debug'] );
				self::update_option( 'shortcode_enable_debug', $enable );

				$enable_debug_value = isset( $_POST['shortcode_enable_debug_value'] );
				self::update_option( 'shortcode_enable_debug_value', $enable_debug_value );

				add_action( 'admin_notices', array( $this->messages, 'settings_updated_notice' ) );
			}
			if ( isset( $_POST['shortcode_debug_action_reset'] )
			     && class_exists( 'MLTools_Shortcode_Attribute_Filter' ) ) {

				delete_option( MLTools_Shortcode_Attribute_Filter::OPTION_NAME );
				delete_option( MLTools_Shortcode_Attribute_Filter::OPTION_NAME_VALUES );

				add_action( 'admin_notices', array( $this->messages, 'shortcode_debug_action_reset' ) );
			}
			if ( isset( $_POST['shortcode_ignored_tags'] ) ) {
				self::update_option( 'shortcode_ignored_tags', sanitize_text_field( $_POST['shortcode_ignored_tags'] ) );
			}

			if ( isset( $_POST['_wp_http_referer'] ) ) {
				wp_redirect( $_POST['_wp_http_referer'] );
				die();
			}
		}
	}

	/**
	 * Modify WPML behaviour based on selected settings
	 */
	public function modify_wpml_behaviour() {

		// Enable adding language information for duplicated posts.
		$duplicate_strings          = self::get_option( 'duplicate_strings' );
		$duplicate_strings_template = self::get_option( 'duplicate_strings_template' );

		if ( ! empty( $duplicate_strings ) && ! empty( $duplicate_strings_template ) ) {
			new Modify_Duplicate_Strings( $duplicate_strings, $duplicate_strings_template );

			// Add information about the plugin settings to Translation Dashboard.
			if ( isset( $_GET['page'] ) && ( in_array( $_GET['page'], array( basename( WPML_TM_PATH ) . '/menu/main.php' ) ) ) ) {
				add_action( 'admin_notices', array( $this->messages, 'wctt_in_action_notice' ) );
			}
		}
	}


	/**
	 * Register settings page
	 */
	public function register_administration_page() {
		add_menu_page( __( 'Dashboard', 'wpml-compatibility-test-tools' ), __( 'Multilingual Tools', 'wpml-compatibility-test-tools' ), 'manage_options', 'mt', array(
			$this,
			'load_template'
		), WPML_CTT_PLUGIN_URL . '/res/img/wctt-icon.png' );
		add_submenu_page( 'mt', __( 'Overview', 'wpml-compatibility-test-tools' ), __( 'Overview', 'wpml-compatibility-test-tools' ), 'manage_options', 'mt', array(
			$this,
			'load_template'
		) );
		add_submenu_page( 'mt', __( 'Settings', 'wpml-compatibility-test-tools' ), __( 'Settings', 'wpml-compatibility-test-tools' ), 'manage_options', 'mt-settings', array(
			$this,
			'load_template'
		) );
		add_submenu_page( 'mt', __( 'Configuration Generator', 'wpml-compatibility-test-tools' ), __( 'Configuration Generator', 'wpml-compatibility-test-tools' ), 'manage_options', 'mt-generator', array(
			$this,
			'load_template'
		) );
	}

	/**
	 * Load page template
	 */
	public function load_template() {
		$screen = get_current_screen();

		switch ( $screen->id ) {
			case 'toplevel_page_mt' :
				add_filter( 'wpml_config_array', array( $this, 'save_configuration_for_debug' ) );
				add_filter( 'wpml_parse_config_file', array( $this, 'display_configuration_for_debug' ) );

				require WPML_CTT_ABS_PATH . 'menus/settings/overview.php';
				break;

			case 'multilingual-tools_page_mt-settings' :
				require WPML_CTT_ABS_PATH . 'menus/settings/settings.php';
				break;

			case 'multilingual-tools_page_mt-generator' :
				require WPML_CTT_ABS_PATH . 'menus/settings/generator.php';
				break;
		}
	}

	public function js_labels() {
		return array(
			'question'                    => __( "All existing strings translations will be replaced with new values.\n Are you sure you want to do this?", 'multilingual-tools' ),
			'no_context_notice'           => __( "* Please select the context.", 'multilingual-tools' ),
			'no_selected_language_notice' => __( "* At least one language should be selected in order to translate strings.", 'multilingual-tools' ),
			'no_template_notice'          => __( "* Template is required.", 'multilingual-tools' )
		);
	}

	/**
	 * Add scripts only for plugin pages
	 */
	public function add_scripts( $hook ) {
		if ( in_array( $hook, array(
			'toplevel_page_mt',
			'multilingual-tools_page_mt-settings',
			'multilingual-tools_page_mt-generator'
		) ) ) {
			wp_enqueue_script( 'mt-scripts', WPML_CTT_PLUGIN_URL . '/res/js/mt-script.js', array( 'jquery' ), WPML_CTT_VERSION );
			wp_localize_script( 'mt-scripts', 'mt_data', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'labels'   => $this->js_labels()
			) );

		}
	}

	/**
	 * Add styles only for plugin pages
	 */
	public function add_styles( $hook ) {
		if ( in_array( $hook, array(
			'toplevel_page_mt',
			'multilingual-tools_page_mt-settings',
			'multilingual-tools_page_mt-generator'
		) ) ) {
			wp_register_style( 'wctt-generator-style', WPML_CTT_PLUGIN_URL . '/res/css/wctt-style.css', WPML_CTT_VERSION );
			wp_enqueue_style( 'wctt-generator-style' );
		}
	}

	/**
	 * Generate XML file
	 *
	 * Generation wpml-config.xml file.
	 * Used as configuration file for WPML plugin.
	 *
	 * @url https://wpml.org/documentation/support/language-configuration-files/
	 */
	public function generate_xml() {
		$dom                     = new DOMDocument();
		$dom->preserveWhiteSpace = false;
		$dom->formatOutput       = true;

		$root = $dom->createElement( 'wpml-config' );
		$root = $dom->appendChild( $root );

		$args = array(
			'_builtin' => false
		);

		$post_types   = get_post_types( $args, 'names' );
		$checkbox_cpt = isset( $_POST['_cpt'] ) && is_array( $_POST['_cpt'] ) ? $_POST['_cpt'] : null;
		$radio_cpt    = isset( $_POST['cpt'] ) && is_array( $_POST['cpt'] ) ? $_POST['cpt'] : null;

		$taxonomies   = get_taxonomies( $args );
		$checkbox_tax = isset( $_POST['_tax'] ) && is_array( $_POST['_tax'] ) ? $_POST['_tax'] : null;
		$radio_tax    = isset( $_POST['tax'] ) && is_array( $_POST['tax'] ) ? $_POST['tax'] : null;

		$custom_fields = wpml_get_custom_fields();
		$checkbox_cf   = isset( $_POST['_cf'] ) && is_array( $_POST['_cf'] ) ? $_POST['_cf'] : null;
		$radio_cf      = isset( $_POST['cf'] ) && is_array( $_POST['cf'] ) ? $_POST['cf'] : null;

		if ( $checkbox_cpt ) {
			$this->generate_basic_content_types(
				$dom,
				$root,
				$post_types,
				$checkbox_cpt,
				$radio_cpt,
				'custom-types',
				'custom-type',
				'translate'
			);
		}

		if ( $checkbox_tax ) {
			$this->generate_basic_content_types(
				$dom,
				$root,
				$taxonomies,
				$checkbox_tax,
				$radio_tax,
				'taxonomies',
				'taxonomy',
				'translate'
			);
		}

		if ( $checkbox_cf ) {
			$this->generate_basic_content_types(
				$dom,
				$root,
				$custom_fields,
				$checkbox_cf,
				$radio_cf,
				'custom-fields',
				'custom-field',
				'action'
			);
		}

		if ( isset( $_POST['at'] ) ) {
			$this->generate_admin_texts( $dom, $root );
		}

		if ( isset( $_POST['shc'] ) && is_array( $_POST['shc'] ) ) {
			$this->generate_shortcodes( $dom, $root );
		}

		$xml = $dom->saveXML( $root );

		// Save options
		switch ( wpml_ctt_validate_radio( $_POST['save'] ) ) {
			case 'file' :
				header( "Content-Description: File Transfer" );
				header( 'Content-Disposition: attachment; filename="wpml-config.xml"' );
				header( "Content-Type: application/xml" );
				echo $xml;
				die();
				break;

			case 'dir' :
				if ( file_put_contents( get_template_directory() . '/wpml-config.xml', $xml ) ) {
					add_action( 'admin_notices', array( $this->messages, 'file_save_success' ) );
				}
				break;
		}
	}

	/**
	 * Generate XML from option array
	 *
	 * @param $options
	 * @param $node
	 * @param $dom
	 *
	 * @since 1.3.0
	 *
	 */
	public function option2xml( $options, $node, $dom ) {
		if ( is_array( $options ) ) {

			foreach ( $options as $option => $value ) {

				// Only if parent option is selected, both parent and child will be generated
				if ( isset( $_POST['at'][ $option ] ) ) {
					$at           = $node->appendChild( $dom->createElement( 'key' ) );
					$atatr        = $dom->createAttribute( 'name' );
					$atatr->value = $option;
					$at->appendChild( $atatr );

					if ( is_array( $value ) ) {
						$this->option2xml( $value, $at, $dom );
					}
				}
			}
		}
	}

	/**
	 * Generate DOM nodes for basic content types.
	 *
	 * Basic content types in this case are: custom post types, taxonomies, custom fields.
	 *
	 * @param $dom
	 * @param $root
	 * @param $content
	 * @param $checkbox
	 * @param $radio
	 * @param $parent
	 * @param $child
	 * @param $attribute
	 *
	 * @since 1.3.0
	 *
	 */
	public function generate_basic_content_types( $dom, $root, $content, $checkbox, $radio, $parent, $child, $attribute ) {
		$parent_node = $dom->createElement( $parent );
		$parent_node = $root->appendChild( $parent_node );

		foreach ( $content as $c ) {

			if ( $parent === 'custom-fields' ) {
				$c = $c->meta_key;
			}

			if ( isset( $checkbox[ $c ] ) ) {
				$child_node             = $dom->createElement( $child, sanitize_key( $c ) );
				$child_node             = $parent_node->appendChild( $child_node );
				$child_node_attr        = $dom->createAttribute( $attribute );
				$child_node_attr->value = wpml_ctt_validate_radio( $radio[ $c ] );
				$child_node->appendChild( $child_node_attr );

				// When set to display as translated.
				if ( $radio[ $c ] === '2' ) {
					$child_node_attr        = $dom->createAttribute( 'display-as-translated' );
					$child_node_attr->value = '1';
					$child_node->appendChild( $child_node_attr );
				}
			}
		}
	}

	/**
	 * Generate DOM nodes for admin texts
	 *
	 * @param $dom
	 * @param $root
	 *
	 * @since 1.3.0
	 *
	 */
	public function generate_admin_texts( $dom, $root ) {
		$ats = $dom->createElement( 'admin-texts' );
		$ats = $root->appendChild( $ats );

		$options = wpml_ctt_options_list();

		foreach ( $options as $name => $value ) {
			$options[ $name ] = maybe_unserialize( maybe_unserialize( $value ) );
		}

		$this->option2xml( $options, $ats, $dom );
	}

	/**
	 * Generate DOM nodes for shortcodes
	 *
	 * @param $dom
	 * @param $root
	 *
	 * @since 1.3.0
	 *
	 */
	public function generate_shortcodes( $dom, $root ) {
		$shortcodes     = array_unique( $_POST['shc'] );
		$shortcode_attr = isset( $_POST['shc-attr'] ) && is_array( $_POST['shc-attr'] ) ? (array) $_POST['shc-attr'] : null;

		//	Create xml node <shortcodes>
		$shortcodes_node = $dom->createElement( 'shortcodes' );
		$shortcodes_node = $root->appendChild( $shortcodes_node );

		foreach ( $shortcodes as $shortcode ) {

			$shortcode_index = array_search( $shortcode, $shortcodes, true );
			$shortcode       = str_replace( ' ', '', sanitize_html_class( $shortcode, "Invalid_shortcode" ) );

			$shortcode_node = $dom->createElement( 'shortcode' );
			$shortcode_node = $shortcodes_node->appendChild( $shortcode_node );

			$tag_node = $dom->createElement( 'tag', $shortcode );
			$shortcode_node->appendChild( $tag_node );

			if ( ! is_null( $shortcode_attr ) && $shortcode_attr[ $shortcode_index ] !== "" ) {

				$attribute        = str_replace( ' ', '', $shortcode_attr[ $shortcode_index ] );
				$attributes_array = explode( ",", $attribute );

				// Dealing with shortcode attribute if available.
				$attributes_node = $dom->createElement( 'attributes' );
				$attributes_node = $shortcode_node->appendChild( $attributes_node );

				if ( ! empty( $attributes_array ) ) {

					foreach ( $attributes_array as $a ) {
						$attribute_node = $dom->createElement( 'attribute', sanitize_html_class( $a, "Invalid_attribute" ) );
						$attributes_node->appendChild( $attribute_node );
					}

				} else {
					$attribute_node = $dom->createElement( 'attribute', sanitize_html_class( $attribute, "Invalid_attribute" ) );
					$attributes_node->appendChild( $attribute_node );
				}
			}
		}
	}

	/**
	 * Save current configuration in a global variable to display later.
	 *
	 * @param array $config
	 *
	 * @return array
	 * @global array $wpml_config_debug
	 */
	function save_configuration_for_debug( $config ) {
		global $wpml_config_debug;

		// Check which sections have content and assign a title for each section.
		$wpml_config_debug = array();
		if ( ! empty( $config['wpml-config']['custom-types']['custom-type'] ) ) {
			$wpml_config_debug['Custom posts'] = $config['wpml-config']['custom-types']['custom-type'];
		}
		if ( ! empty( $config['wpml-config']['taxonomies']['taxonomy'] ) ) {
			$wpml_config_debug['Custom taxonomies'] = $config['wpml-config']['taxonomies']['taxonomy'];
		}
		if ( ! empty( $config['wpml-config']['custom-fields']['custom-field'] ) ) {
			$wpml_config_debug['Custom fields translation'] = $config['wpml-config']['custom-fields']['custom-field'];
		}
		if ( ! empty( $config['wpml-config']['custom-term-fields']['custom-term-field'] ) ) {
			$wpml_config_debug['Custom Term Meta Translation'] = $config['wpml-config']['custom-term-fields']['custom-term-field'];
		}
		if ( ! empty( $config['wpml-config']['shortcodes']['shortcode'] ) ) {
			$wpml_config_debug['Shortcodes'] = $config['wpml-config']['shortcodes']['shortcode'];
		}
		if ( ! empty( $config['wpml-config']['admin-texts']['key'] ) ) {
			$wpml_config_debug['Admin Strings to Translate'] = $config['wpml-config']['admin-texts']['key'];
		}
		if ( ! empty( $config['wpml-config']['language-switcher-settings']['key'] ) ) {
			$wpml_config_debug['Language Switcher Settings'] = $config['wpml-config']['language-switcher-settings']['key'];
		}

		return $config;
	}

	/**
	 * Intercept wpml-config.xml parsing to display loaded configuration files
	 * for debugging purposes.
	 *
	 * @param string $file
	 *
	 * @return string
	 * @global object $sitepress
	 */
	function display_configuration_for_debug( $file ) {
		// Get url and name.
		if ( is_object( $file ) ) {
			$url   = ICL_REMOTE_WPML_CONFIG_FILES_INDEX . 'wpml-config/' . $file->admin_text_context . '/wpml-config.xml';
			$name  = $file->admin_text_context;
			$class = 'dashicons-admin-site';
		} else {
			$url   = str_replace( WP_CONTENT_DIR, WP_CONTENT_URL, $file );
			$name  = basename( dirname( $url ) );
			$class = '';
		}

		// Display link to file.
		echo '<a href="' . $url . '">' . $name . '</a>';
		if ( ! empty( $class ) ) {
			echo ' <span class="dashicons ' . $class . '"></span>';
		}
		echo '<br />';

		// Display validation errors if any found.
		if ( is_string( $file ) && file_exists( $file ) ) {
			$validate = new WPML_XML_Config_Validate( WPML_PLUGIN_PATH . '/res/xsd/wpml-config.xsd' );
			$validate->from_file( $file );
			$errors = wp_list_pluck( $validate->get_errors(), 'message' );
			if ( ! empty( $errors ) ) {
				$errors = array_unique( $errors );
				// TODO: add some style.
				echo '<p>' . implode( '<br>', $errors ) . '</p>';
			}
		}

		return $file;
	}

}