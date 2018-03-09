<?php

class MLTools_Shortcode_WPML_Config_Parser {

	private static $config;

	public static function add_hooks() {
		add_filter( 'wpml_config_array', array(
			'MLTools_Shortcode_WPML_Config_Parser',
			'wpml_config_array_filter'
		), 100 );
	}

	public static function wpml_config_array_filter( $config ) {

		self::$config = self::parse_config( $config );

		return $config;
	}

	private static function parse_config( $wpml_xml_config ) {

		$wpml_shortcodes = array();

		if ( isset( $wpml_xml_config['wpml-config']['shortcodes']['shortcode'] ) ) {

			foreach ( $wpml_xml_config['wpml-config']['shortcodes']['shortcode'] as $shortcode ) {

				$tag    = $shortcode['tag']['value'];
				$config = new MLTools_Shortcode_Config( $tag );

				if ( isset( $shortcode['tag']['attr']['encoding'] ) ) {
					$config->set( 'encoding', $shortcode['tag']['attr']['encoding'] );
				}

				if ( isset( $shortcode['tag']['attr']['type'] ) ) {
					$config->set( 'type', $shortcode['tag']['attr']['type'] );
				}

				if ( isset( $shortcode['attributes']['attribute'] ) && is_array( $shortcode['attributes']['attribute'] ) ) {

					if ( isset( $shortcode['attributes']['attribute']['value'] ) ) {

						$attr_name = $shortcode['attributes']['attribute']['value'];
						$config->add_attribute( $attr_name );

						if ( isset( $shortcode['attributes']['attribute']['attr']['encoding'] ) ) {
							$config->set_attribute_property( $attr_name, 'encoding', $shortcode['attributes']['attribute']['attr']['encoding'] );
						}

						if ( isset( $shortcode['attributes']['attribute']['attr']['type'] ) ) {
							$config->set_attribute_property( $attr_name, 'type', $shortcode['attributes']['attribute']['attr']['type'] );
						}
					} else {
						foreach ( $shortcode['attributes']['attribute'] as $attr ) {

							$attr_name = $attr['value'];
							$config->add_attribute( $attr_name );

							if ( isset( $attr['attr']['encoding'] ) ) {
								$config->set_attribute_property( $attr_name, 'encoding', $attr['attr']['encoding'] );
							}

							if ( isset( $attr['attr']['type'] ) ) {
								$config->set_attribute_property( $attr_name, 'type', $attr['attr']['type'] );
							}
						}
					}
				}
				$wpml_shortcodes[ $tag ] = $config->get_props();
			}
		}

		return $wpml_shortcodes;
	}

	public static function get_config() {

		if ( self::$config === null ) {

			// @todo Fix dependencies
			$array_utility_file = WPML_PLUGIN_PATH . '/inc/utilities/xml2array.php';

			if ( file_exists( $array_utility_file ) ) {
				require_once $array_utility_file;
				WPML_Config::load_config_run();
			} else {
				return false;
			}
		}

		return self::$config;
	}

}