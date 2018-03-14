<?php

class WPML_Compatibility_Test_Tools_Base {

	const  OPTIONS_NAME = 'wpml_ctt_settings';
	static $options     = array();
	public $messages;

	public function __construct() {
		$this->messages = new WPML_Compatibility_Test_Tools_Messages();
	}

	/**
	 * Save initial configuration to database
	 */
	public static function install() {
		if ( get_option( self::OPTIONS_NAME ) === false ) {
			$options[ 'string_auto_translate_template' ] = '[%language_name%] %original_string%';
			$options[ 'duplicate_strings_template' ] 	 = '[%language_name%] %original_string%';
			$options[ 'shortcode_enable_debug' ]         = false;
			$options[ 'shortcode_enable_debug_value' ]   = false;
			$options[ 'shortcode_ignored_tags' ]         = false;

			add_option( self::OPTIONS_NAME, $options );
		}

		return true;
	}

	/**
	 * Return plugin option
	 *
	 * @param      $option_name
	 * @param null $default
	 *
	 * @return null
	 */
	public static function get_option( $option_name, $default = null ) {
		if ( empty( self::$options ) ) {
			self::$options = get_option( self::OPTIONS_NAME );
		}

		if ( isset( self::$options[$option_name] ) ) {
			return self::$options[$option_name];
		}

		return $default;
	}

	/**
	 * Update plugin option
	 *
	 * @param $option_name
	 * @param $option_value
	 * @return bool
     */
	public static function update_option($option_name, $option_value ) {
		$options 			   = get_option( self::OPTIONS_NAME );
		$options[$option_name] = $option_value;
		$result 			   = update_option( self::OPTIONS_NAME, $options );

		if ( $result ) {
			self::refresh_options();
		}

		return $result;
	}

	/**
	 * Refresh options
     */
	public static function refresh_options() {
		self::$options = get_option( self::OPTIONS_NAME );
	}
}