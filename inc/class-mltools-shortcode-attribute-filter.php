<?php

class MLTools_Shortcode_Attribute_Filter {

	CONST OPTION_NAME = '_mltools_shortcode_helper_captured_tags';

	private $captured_tags = array();
	private $ignored_tags = array();

	function __construct( array $ignored_tags ) {
		$this->captured_tags = get_option( self::OPTION_NAME, array() );
		$this->ignored_tags  = $ignored_tags;
	}

	public function add_hooks() {
		if ( ! is_admin() ) {
			add_action( 'wp_head', array( $this, 'add_shortcode_filters' ) );
			add_filter( 'do_shortcode_tag', array( $this, 'do_shortcode_tag_filter' ), 10, 3 );
			add_action( 'shutdown', array( $this, 'save_tags' ) );
		}
	}

	public function add_shortcode_filters() {

		global $shortcode_tags;

		foreach ( $shortcode_tags as $tag => $callback ) {
			if ( ! in_array( $tag, $this->ignored_tags ) ) {
				add_filter( "shortcode_atts_{$tag}", array( $this, 'shortcode_atts_filter' ), 10, 4 );
			}
		}
	}

	/**
	 * Shortcode attribute filter.
	 *
	 * Notice: shortcode_atts() must be called with 3rd parameter $shortcode,
	 * otherwise this filter will be not be applied.
	 *
	 * Example:
	 * extract( shortcode_atts( $default_atts, $atts, 'My_Widget' ) )
	 *
	 * @param string $out Output.
	 * @param array $pairs Default attributes.
	 * @param array $atts Shortcode attributes.
	 * @param string $shortcode Shortcode tag.
	 *
	 * @return mixed
	 */
	public function shortcode_atts_filter( $out, $pairs, $atts, $shortcode ) {

		$all_attributes = array_merge( $pairs, $atts );

		$props = array();
		if ( isset( $this->captured_tags[ $shortcode ] ) ) {
			$props = $this->captured_tags[ $shortcode ];
		}

		$config = new MLTools_Shortcode_Config( $shortcode, $props );
		foreach ( $all_attributes as $attr_name => $attr_value ) {
			$config->add_attribute( $attr_name );
		}

		$this->captured_tags[ $shortcode ] = $config->get_props();

		return $out;
	}

	public function do_shortcode_tag_filter( $output, $tag, $attr ) {

		if ( ! in_array( $tag, $this->ignored_tags ) ) {

			$props = array();
			if ( isset( $this->captured_tags[ $tag ] ) ) {
				$props = $this->captured_tags[ $tag ];
			}

			$config = new MLTools_Shortcode_Config( $tag, $props );
			if ( is_array( $attr ) ) {
				foreach ( $attr as $attr_name => $attr_value ) {
					$config->add_attribute( $attr_name );
				}
				$this->captured_tags[ $tag ] = $config->get_props();
			}
		}

		return $output;
	}

	public function save_tags() {
		update_option( self::OPTION_NAME, $this->get_tags() );
	}

	private function get_tags() {

		foreach ( $this->ignored_tags as $tag ) {
			unset( $this->captured_tags[ $tag ] );
		}

		ksort( $this->captured_tags );

		return $this->captured_tags;
	}

}