<?php

class WPML_Compatibility_Test_Tools extends WPML_Compatibility_Test_Tools_Base {

	public function __construct() {

		parent::__construct();

		add_action( 'init', array( $this, 'init' ) );

	}


	public function init(){

		//check for WPML
		if ( ! defined( 'ICL_SITEPRESS_VERSION' ) || ICL_PLUGIN_INACTIVE ) {
			if ( ! function_exists( 'is_multisite' ) || ! is_multisite() ) {
				add_action( 'admin_notices', array( $this->messages, 'no_wpml_notice' ) );
			}
			return false;
		}

		//check for Translation Management
		if( ! defined( 'WPML_TM_VERSION' ) ){
			add_action( 'admin_notices', array( $this->messages, 'no_tm_notice' ) );
			return false;
		}

		//check for String Translation
		if( ! defined( 'WPML_ST_VERSION' ) ){
			add_action( 'admin_notices', array( $this->messages, 'no_st_notice' ) );
			return false;
		}

		//WPML setup has to be finished
		global $sitepress;
		if( !isset( $sitepress ) ){
			add_action( 'admin_notices', array( $this->messages, 'no_wpml_notice' ) );
			return false;
		}

		if( method_exists( $sitepress, 'get_setting' ) && !$sitepress->get_setting( 'setup_complete' ) ) {
			add_action( 'admin_notices', array( $this->messages, 'not_finished_wpml_setup' ) );
			return false;
		}

		self::install();

		add_action( 'admin_menu', array( $this, 'register_administration_page' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'add_scripts' ) );

		//handle admin settings page
		$this->process_request();
		//change WPML behaviour based on selected settings
		$this->modify_wpml_behaviour();

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

			if ( empty( $template ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_template_notice' ) );
				$error = true;
			}

			if ( $error ) {
				return false;
			}

			self::update_option('string_auto_translate_context', $context);
			self::update_option('string_auto_translate_languages', $languages);
			self::update_option('string_auto_translate_template', $template);

			add_action( 'admin_notices', array( $this->messages, 'settings_updated_notice' ) );

			if ( isset( $_POST['strings_auto_translate_action_translate'] ) ) {

				$context   = self::get_option('string_auto_translate_context');
				$languages = self::get_option('string_auto_translate_languages');
				$template  = self::get_option('string_auto_translate_template');


				if ( empty( $languages ) ) {
					add_action( 'admin_notices', array( $this->messages, 'no_selected_language_notice' ) );
					$error = true;
				}

				if ( empty( $context ) ) {
					add_action( 'admin_notices', array( $this->messages, 'no_context_notice' ) );
					$error = true;
				}

				if ( empty( $template ) ) {
					add_action( 'admin_notices', array( $this->messages, 'no_template_notice' ) );
					$error = true;
				}

				if ( $error ) {
					return false;
				}

				$this->translate_strings( $context, $languages, $template );
				add_action( 'admin_notices', array( $this->messages, 'strings_translated_notice' ) );

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
	 * Process action save_duplicate_strings_to_translate
	 */
	private function process_save_duplicate_strings_to_translate(){

		if ( isset( $_POST['save_duplicate_strings_to_translate'] ) ) {

			$error = false;

			$strings = ( isset( $_POST['duplicate_strings_to_translate'] ) ) ? $_POST['duplicate_strings_to_translate'] : array();
			$template = ( isset( $_POST['duplicate_strings_template'] ) ) ? $_POST['duplicate_strings_template'] : '';

			if ( empty( $template ) ) {
				add_action( 'admin_notices', array( $this->messages, 'no_template_notice' ) );
				$error = true;
			}

			if ( $error ) {
				return false;
			}

			self::update_option('duplicate_strings', $strings);
			self::update_option('duplicate_strings_template', $template);

			if ( !empty( $strings ) ){
				add_action( 'admin_notices', array( $this->messages, 'duplicate_strings_available' ) );
			}
			else{
				add_action( 'admin_notices', array( $this->messages, 'settings_updated_notice' ) );
			}

		}

		return true;

	}

	/**
	 * Modify WPML behaviour based on selected settings
	 */
	public function modify_wpml_behaviour() {

		//Enable adding language information for duplicated posts
		$duplicate_strings = self::get_option('duplicate_strings');
		$duplicate_strings_template = self::get_option('duplicate_strings_template');
		if ( ! empty( $duplicate_strings ) && ! empty( $duplicate_strings_template ) ) {
			new Modify_Duplicate_Strings( $duplicate_strings, $duplicate_strings_template);

			//add information about the plugin settings to Translation Dashboard
			if ( isset( $_GET['page'] ) && ( in_array( $_GET['page'], array( basename(WPML_TM_PATH).'/menu/main.php' ) ) ) ) {
				add_action( 'admin_notices', array( $this->messages, 'wctt_in_action_notice' ) );
			}
		}

	}


	/**
	 * Register settings page
	 */
	public function register_administration_page() {
		add_menu_page( __( 'Settings', 'wpml-compatibility-test-tools' ), __( 'WPML CTT', 'wpml-compatibility-test-tools' ), 'manage_options', WPML_CTT_MENU_SETTINGS_SLUG , null, ICL_PLUGIN_URL . '/res/img/icon16.png' );
		add_submenu_page( WPML_CTT_MENU_SETTINGS_SLUG, __( 'Settings', 'wpml-compatibility-test-tools' ), __( 'Settings', 'wpml-compatibility-test-tools' ), 'manage_options', WPML_CTT_MENU_SETTINGS_SLUG );
		add_submenu_page( WPML_CTT_MENU_SETTINGS_SLUG, __( 'Custom objects', 'wpml-compatibility-test-tools' ), __( 'Custom objects', 'wpml-compatibility-test-tools' ), 'manage_options', WPML_CTT_FOLDER . '/menus/settings/info.php' );


	}


	/**
	 * Add scripts only for plugin's settings page
	 */
	public function add_scripts(){

		$screen = get_current_screen();

		if ( in_array( $screen->id, array( WPML_CTT_FOLDER . '/menus/settings/settings') ) )
		{
			wp_enqueue_script( 'wctt-scripts', WPML_CTT_PLUGIN_URL . '/res/js/scripts.js', array( 'jquery' ), WPML_CTT_VERSION );
		}

	}


}