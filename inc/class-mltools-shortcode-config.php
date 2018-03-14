<?php

class MLTools_Shortcode_Config {

	protected $props = array(
		'tag'        => false,
		'attributes' => array(),
	);

	function __construct( $tag, $props = array() ) {
		if ( ! empty( $props ) ) {
			foreach ( $props as $prop => $value ) {
				$this->props[ $prop ] = $value;
			}
		}
		$this->props['tag'] = $tag;
	}

	public function set( $name, $value ) {
		if ( isset( $this->props[ $name ] ) ) {
			$this->props[ $name ] = $value;
		}
	}

	public function get_props() {
		return $this->props;
	}

	public function __get( $name ) {
		return isset( $this->props[ $name ] ) ? $this->props[ $name ] : null;
	}

	public function __set( $name, $value ) {
		user_error( 'Use set' );
	}

	public function add_attribute( $attr_name ) {
		if ( ! isset( $this->props['attributes'][ $attr_name ] ) ) {
			$this->props['attributes'][ $attr_name ] = array();
		}
	}

	public function set_attribute_property( $attr_name, $prop, $value ) {
		if ( isset( $this->props['attributes'][ $attr_name ] ) ) {
			$this->props['attributes'][ $attr_name ][ $prop ] = $value;
		}
	}

	public function get_attribute_property( $attr_name, $prop ) {
		return isset( $this->props['attributes'][ $attr_name ][ $prop ] ) ? $this->props['attributes'][ $attr_name ][ $prop ] : null;
	}
}