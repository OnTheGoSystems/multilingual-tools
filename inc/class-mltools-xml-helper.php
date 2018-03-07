<?php

class MLTools_XML_Helper {

	public function get_dom_shortcodes( array $shortcodes ) {

		$xml                     = new DOMDocument( "1.0", "UTF-8" );
		$xml->preserveWhiteSpace = false;
		$xml->formatOutput       = true;
		$xml_shortcodes          = $xml->createElement( 'shortcodes' );
		foreach ( $shortcodes as $tag => $config ) {
			$xml_shortcodes->appendChild( $this->append_shortcode( $config, $xml ) );
		}
		$xml->appendChild( $xml_shortcodes );

		return $xml->saveXML();
	}

	public function get_dom_single_shortcode( array $config ) {

		$xml                     = new DOMDocument( "1.0", "UTF-8" );
		$xml->preserveWhiteSpace = false;
		$xml->formatOutput       = true;
		$xml->appendChild( $this->append_shortcode( $config, $xml ) );

		return $xml->saveXML();
	}

	private function append_shortcode( array $config, DOMDocument $xml ) {

		$xml_shortcode = $xml->createElement( 'shortcode' );
		$xml_tag       = $xml->createElement( 'tag', $config['tag'] );
		$xml_shortcode->appendChild( $xml_tag );
		$attributes = array_keys( $config['attributes'] );

		if ( ! empty( $attributes ) ) {
			$xml_attributes = $xml->createElement( 'attributes' );
			foreach ( $attributes as $attr_name ) {
				$xml_attribute = $xml->createElement( 'attribute', $attr_name );
				$xml_attributes->appendChild( $xml_attribute );
			}
			$xml_shortcode->appendChild( $xml_attributes );
		}

		return $xml_shortcode;
	}
}