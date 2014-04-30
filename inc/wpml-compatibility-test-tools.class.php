<?php

class WPML_Compatibility_Test_Tools extends WPML_Compatibility_Test_Tools_Base {



	public function __construct() {

		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				add_action( 'admin_notices', array( $this, 'no_wpml_notice' ) );
			}

			return false;
		}

		global $sitepress;
		if(!isset($sitepress) || (method_exists($sitepress,'get_setting') && !$sitepress->get_setting( 'setup_complete' ))){
			add_action( 'admin_notices', array( $this, 'no_wpml_notice' ) );
			return false;
		}


		self::install();

		add_action( 'admin_menu', array( $this, 'register_administration_page' ) );
		add_action( 'admin_print_scripts', array( $this, 'add_scripts' ) );

		add_action( 'init', array( $this, 'process_request' ) );
		add_action( 'init', array( $this, 'modify_wpml_behaviour' ) );

	}


	/**
	 * Process request
	 *
	 * @return bool
	 */
	public function process_request() {

		$this->process_strings_auto_translate_action_translate();
		$this->process_save_duplicate_strings_to_translate();

		return true;
	}


	/**
	 * Process action save_duplicate_strings_to_translate
	 */
	private function process_save_duplicate_strings_to_translate(){

		if ( isset( $_POST['save_duplicate_strings_to_translate'] ) ) {

			$error = false;

			$strings = ( isset( $_POST['duplicate_strings_to_translate'] ) ) ? $_POST['duplicate_strings_to_translate'] : array();
			$template = ( isset( $_POST['duplicate_strings_template'] ) ) ? $_POST['duplicate_strings_template'] : '';

			if ( empty( $template ) ) {
				add_action( 'admin_notices', array( $this, 'no_template_notice' ) );
				$error = true;
			}

			if ( $error ) {
				return false;
			}

			self::update_option('duplicate_strings', $strings);
			self::update_option('duplicate_strings_template', $template);
			add_action( 'admin_notices', array( $this, 'settings_updated_notice' ) );

		}

		return true;

	}


	/**
	 * Process action strings_auto_translate_action_translate
	 *
	 * @return bool
	 */
	private function process_strings_auto_translate_action_translate(){

		if ( isset( $_POST['strings_auto_translate_action_save'] ) || isset( $_POST['strings_auto_translate_action_translate'] )  ) {

			$error = false;

			$context   = ( isset( $_POST['strings_auto_translate_context'] ) ) ? $_POST['strings_auto_translate_context'] : '';
			$languages = ( isset( $_POST['active_languages'] ) ) ? $_POST['active_languages'] : array();
			$template  = ( isset( $_POST['strings_auto_translate_template'] ) ) ? $_POST['strings_auto_translate_template'] : '';

			self::update_option('string_auto_translate_context', $context);
			self::update_option('string_auto_translate_languages', $languages);
			self::update_option('string_auto_translate_template', $template);

			add_action( 'admin_notices', array( $this, 'settings_updated_notice' ) );

			if ( isset( $_POST['strings_auto_translate_action_translate'] ) ) {

				$context   = self::get_option('string_auto_translate_context');
				$languages = self::get_option('string_auto_translate_languages');
				$template  = self::get_option('string_auto_translate_template');


				if ( empty( $languages ) ) {
					add_action( 'admin_notices', array( $this, 'no_selected_language_notice' ) );
					$error = true;
				}

				if ( empty( $context ) ) {
					add_action( 'admin_notices', array( $this, 'no_context_notice' ) );
					$error = true;
				}

				if ( empty( $template ) ) {
					add_action( 'admin_notices', array( $this, 'no_template_notice' ) );
					$error = true;
				}


				if ( $error ) {
					return false;
				}

				$this->translate_strings( $context, $languages, $template );
				add_action( 'admin_notices', array( $this, 'strings_translated_notice' ) );

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
	private function translate_strings( $context, $languages, $template ) {
		global $wpdb;

		//get all not translated strings (status <> 1)
		if ( 0 === strcmp( $context, 'all_contexts' ) ) {
			$strings = $wpdb->get_results( "SELECT id, language, context, value FROM {$wpdb->prefix}icl_strings" );
		} else {
			$strings = $wpdb->get_results( $wpdb->prepare( "SELECT id, language, context, value FROM {$wpdb->prefix}icl_strings WHERE context=%s", $context ) );
		}

		//for each string add information
		foreach ( $strings as $v ) {
			foreach ( $languages as $lang ) {
				icl_add_string_translation( $v->id, $lang, wpml_ctt_prepare_string($template, $v->value, $lang), TRUE );
				icl_update_string_status( $v->id );
			}

		}

	}


	/**
	 * Modify WPML behaviour based on selected settings
	 */
	public function modify_wpml_behaviour() {

		//Enable adding language information for duplicated posts
		$duplicate_strings = self::get_option('duplicate_strings');;
		$duplicate_strings_template = self::get_option('duplicate_strings_template');;
		if ( ! empty( $duplicate_strings ) && ! empty( $duplicate_strings_template ) ) {
			new Modify_Duplicate_Strings( $duplicate_strings, $duplicate_strings_template);
		}

	}

	public function no_wpml_notice() {
	?>
	<div class="message error"><p><?php printf( __( 'WPML Compatibility Test Tools is enabled but not effective. It requires configured <a href="%s">WPML</a> in order to work.', 'wpml-compatibility-test-tools' ), 'https://wpml.org/' ); ?></p></div>
	<?php
	}

	public function no_selected_language_notice() {
		echo '<div class="message error"><p>' . __( 'At least one language should be selected in order to translate strings.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function no_selected_language_for_pages_notice() {
		echo '<div class="message error"><p>' . __( 'At least one language should be selected in order to create pages with dummy content.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function no_context_notice() {
		echo '<div class="message error"><p>' . __( 'Please select the context.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function no_template_notice() {
		echo '<div class="message error"><p>' . __( 'Template is required.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function strings_translated_notice() {
		echo '<div class="updated message fade"><p>' . __( 'Strings translated.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function settings_updated_notice() {
		echo '<div class="updated message fade"><p>' . __( 'Settings updated.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function register_administration_page() {
		add_menu_page( __( 'WPML CTT', 'wpml-compatibility-test-tools' ), __( 'WPML CTT', 'wpml-compatibility-test-tools' ), 'manage_options', 'wpml-compatibility-test-tools', array( $this, 'administration_page' ), ICL_PLUGIN_URL . '/res/img/icon16.png' );
	}

	public function administration_page() {
		include( WPML_CTT_PATH . '/menus/settings/settings.php' );
	}

	public function add_scripts(){
		wp_enqueue_script( 'wctt-scripts', WPML_CTT_PLUGIN_URL . '/res/js/scripts.js', array( 'jquery' ), WPML_CTT_VERSION );
	}


}