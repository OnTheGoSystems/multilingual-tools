<?php

class MLTools_Shortcode_Attribute_Filter {

	CONST OPTION_NAME = '_mltools_shortcode_helper_captured_tags';
	CONST OPTION_NAME_VALUES = '_mltools_shortcode_helper_captured_values';

	private $captured_tags = array();
	private $captured_values = array();
	private $ignored_tags = array();

	function __construct( array $ignored_tags ) {
		$this->captured_tags   = get_option( self::OPTION_NAME, array() );
		$this->captured_values = get_option( SELF::OPTION_NAME_VALUES, array() );
		$this->ignored_tags    = $ignored_tags;
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
	 * @param string $tag Shortcode tag.
	 *
	 * @return mixed
	 */
	public function shortcode_atts_filter( $out, $pairs, $atts, $tag ) {

		if ( is_array( $pairs ) && is_array( $atts ) ) {
			$all_attributes = array_merge( $pairs, $atts );
			$this->add_tag( $tag, $all_attributes );
		}

		return $out;
	}

	public function do_shortcode_tag_filter( $output, $tag, $attr ) {

		if ( ! in_array( $tag, $this->ignored_tags ) && is_array( $attr ) ) {
			$this->add_tag( $tag, $attr );
		}

		return $output;
	}

	private function add_tag( $tag, $attributes ) {

		$props = array();
		if ( isset( $this->captured_tags[ $tag ] ) ) {
			$props = $this->captured_tags[ $tag ];
		}

		$config = new MLTools_Shortcode_Config( $tag, $props );

		foreach ( $attributes as $attr_name => $attr_value ) {
			$config->add_attribute( $attr_name );
			$this->captured_values[ $tag ]['attributes'][ $attr_name ] = $attr_value;
		}

		ksort( $this->captured_values[ $tag ]['attributes'] );

		$props = $config->get_props();
		ksort( $props['attributes'] );

		$this->captured_tags[ $tag ] = $props;
	}

	public function save_tags() {
		update_option( self::OPTION_NAME, $this->get_tags() );
		update_option( self::OPTION_NAME_VALUES, $this->get_captured_values() );
	}

	private function get_tags() {

		foreach ( $this->ignored_tags as $tag ) {
			unset( $this->captured_tags[ $tag ] );
		}

		ksort( $this->captured_tags );

		return $this->captured_tags;
	}

	private function get_captured_values() {

		foreach ( $this->ignored_tags as $tag ) {
			unset( $this->captured_values[ $tag ] );
		}

		ksort( $this->captured_values );

		return $this->captured_values;
	}

}