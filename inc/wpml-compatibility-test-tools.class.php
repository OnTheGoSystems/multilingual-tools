<?php

class WPML_Compatibility_Test_Tools {

	public function __construct() {
		$this->admin_notices();
		$this->install();

		add_action( 'admin_menu', array( $this, 'register_administration_page' ) );
		add_action( 'init', array( $this, 'process_request' ) );
		add_action( 'init', array( $this, 'apply_settings' ) );

	}

	/**
	 * Apply various settings based on saved configuration
	 */
	public function apply_settings() {

		$wpml_ctt_auto_duplicate = get_option( 'wpml_ctt_auto_duplicate' );

		if ( ! empty( $wpml_ctt_auto_duplicate ) ) {
			new Modify_Duplicate_Strings( $wpml_ctt_auto_duplicate );
		}

	}

	/**
	 * Process administration page request
	 *
	 * @return bool
	 */
	public function process_request() {

		if ( isset( $_POST['strings_auto_translate_action_translate'] ) ) {
			$context   = ( isset( $_POST['strings_auto_translate_context'] ) ) ? $_POST['strings_auto_translate_context'] : null;
			$languages = ( isset( $_POST['active_languages'] ) ) ? $_POST['active_languages'] : null;
			$prefix    = ( isset( $_POST['strings_auto_translate_prefix'] ) ) ? $_POST['strings_auto_translate_prefix'] : null;

			if ( ! $languages ) {
				add_action( 'admin_notices', array( $this, 'no_selected_language_notice' ) );
			}

			if ( ! $prefix ) {
				add_action( 'admin_notices', array( $this, 'no_prefix_notice' ) );
			}

			if ( ! $languages || ! $prefix ) {
				return false;
			}

			update_option( 'wpml_ctt_auto_translate_prefix', $prefix );

			$this->translate_strings( $context, $languages, $prefix );
			add_action( 'admin_notices', array( $this, 'strings_translated_notice' ) );
		}

		if ( isset( $_POST['save_duplicate_strings_to_translate'] ) ) {
			$settings = ( isset( $_POST['duplicate_strings_to_translate'] ) ) ? $_POST['duplicate_strings_to_translate'] : array();

			update_option( 'wpml_ctt_auto_duplicate', $settings );
			add_action( 'admin_notices', array( $this, 'settings_updated_notice' ) );
		}


	}


	public function admin_notices() {
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				add_action( 'admin_notices', array( $this, 'no_wpml_notice' ) );
			}

			return false;
		}
	}

	public function install() {
		if ( get_option( 'wpml_ctt_plugin ' ) === false ) {
			add_option( 'wpml_ctt_plugin', '1' );

			if ( get_option( 'wpml_ctt_auto_translate_prefix ' ) === false ) {
				add_option( 'wpml_ctt_auto_translate_prefix', '[%language_name] ' );
			}
		}
	}

	public function no_wpml_notice() {
	?>
	<div class="message error"><p><?php printf( __( 'WPML Compatibility Test Tools is enabled but not effective. It requires <a href="%s">WPML</a> in order to work.', 'wpml-string-translation' ), 'https://wpml.org/' ); ?></p></div>
	<?php
	}

	public function no_selected_language_notice() {
		echo '<div class="message error"><p>' . __( 'At least one language should be selected in order to translate strings.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function no_selected_language_for_pages_notice() {
		echo '<div class="message error"><p>' . __( 'At least one language should be selected in order to create pages with dummy content.', 'wpml-compatibility-test-tools' ) . '</p></div>';
	}

	public function no_prefix_notice() {
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
		include( WPML_CTT_PATH . '/menus/wpml-compatibility-test-tools.menu.settings.php' );
	}

	/**
	 * Auto translate strings with given context
	 *
	 * @param $context
	 * @param $languages
	 * @param $prefix
	 */
	private function translate_strings( $context, $languages, $prefix ) {
		global $wpdb;

		//add information about language to prefix
		$prefixes = array();
		foreach ( $languages as $lang ) {
			$prefixes[$lang] = $this->parse_prefix( $prefix, $lang );
		}

		//get all not translated strings (status <> 1)
		if ( 0 === strcmp( $context, 'all_contexts' ) ) {
			$strings = $wpdb->get_results( "SELECT id, language, context, value FROM {$wpdb->prefix}icl_strings" );
		} else {
			$strings = $wpdb->get_results( $wpdb->prepare( "SELECT id, language, context, value FROM {$wpdb->prefix}icl_strings WHERE context=%s", $context ) );
		}

		//for each string add information
		foreach ( $strings as $v ) {
			foreach ( $languages as $lang ) {
				icl_add_string_translation( $v->id, $lang, $prefixes[$lang] . $v->value, TRUE );
				icl_update_string_status( $v->id );
			}

		}

	}

	/**
	 * Parse prefix string
	 *
	 * @param $prefix
	 * @param $lang
	 *
	 * @return mixed
	 */
	private function parse_prefix( $prefix, $lang ) {

		global $sitepress;
		$language_details = $sitepress->get_language_details( $lang );

		if ( isset( $language_details['english_name'] ) ) {
			$prefix = str_replace( '%language_name', $language_details['english_name'], $prefix );
		}

		return $prefix;

	}


}