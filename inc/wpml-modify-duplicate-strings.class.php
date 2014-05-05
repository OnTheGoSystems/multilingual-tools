<?php

/**
 * Class Modify_Duplicate_Strings
 *
 * Example how to use filter icl_duplicate_generic_string
 * to modify strings (title, content, terms, custom fields) for duplicates
 *
 */
class Modify_Duplicate_Strings {

	private $filter = array();
	private $template = '';

	public function __construct( $filter = array(), $template = '[%language_name%] %original_string%' ) {
		$this->filter   = $filter;
		$this->template = $template;
		add_filter( 'icl_duplicate_generic_string', array( $this, 'icl_duplicate_generic_string' ), 10, 3 );
	}

	/**
	 *
	 * Add information about language to string based on context
	 *
	 * @param $string             - string to modify
	 * @param $lang               - language code
	 * @param $context            - array(
	 *                            'context'   => 'post' or 'custom_field' or 'taxonomy',
	 *                            'attribute' => 'title' or 'content' or 'excerpt' (for a post), 'value' (for a custom field), '{taxonomy_name}' (for a taxonomy),
	 *                            'key'       => '{post_id}' | '{meta_key}' | '{term_id}',
	 *                            );
	 *
	 * @return string
	 */
	public function icl_duplicate_generic_string( $string, $lang, $context ) {

		//check context
		$filter_context = isset( $context['context'] ) ? $context['context'] : '';
		$attribute      = isset( $context['attribute'] ) ? $context['attribute'] : '';

		//check if user required to filter given string type (based on selected settings in admin panel)
		if ( isset( $this->filter[$filter_context] ) ) {
			//special case for taxonomy
			if ( in_array( $filter_context, array( 'taxonomy', 'taxonomy_slug' ) ) ) {
				if ( ! isset( $this->filter[$filter_context]['all'] ) ) {
					if ( ! isset( $this->filter[$filter_context][$attribute] ) ) {
						return $string;
					}
				}
			} elseif ( ! isset( $this->filter[$filter_context][$attribute] ) ) {
				return $string;
			}

		} else {
			return $string;
		}

		//based on context
		switch ( $filter_context ) {
			case 'post':

				//exception for empty excerpt field
				if ( ( 0 === strcmp( $attribute, 'excerpt' ) ) && ( empty( $string ) ) ) {
					break;
				}

				//check if user want to add language information also for images alt and title tags in content
				if ( 0 === strcmp( $attribute, 'content' ) && isset( $this->filter[$filter_context]['image-tags'] ) ) {
					$string = $this->add_language_name_to_images( $this->template, $string, $lang );
				}

				$string = wpml_ctt_prepare_string( $this->template, $string, $lang );
				break;
			case 'taxonomy':
				$string = wpml_ctt_prepare_string( $this->template, $string, $lang );
				break;
			case 'taxonomy_slug' :
				$string = $this->add_language_name_to_slug( $string, $lang );
				break;
			case 'custom_field' :
				$string = $this->add_language_name_to_custom_field( $string, $lang, $context );
				break;
		}

		//by default return the same value
		return $string;

	}


	/**
	 * Add language name to slug
	 *
	 * @param $template
	 * @param $string
	 * @param $lang
	 *
	 * @return string
	 */
	private function add_language_name_to_images( $template, $string, $lang ) {

		$doc = new DOMDocument();
		$loaded = @$doc->loadHTML( '<div>'.$string.'</div>'); //dirty hack with <div> to avoid additional <p> for text without tags
		if ( !$loaded){
			return $string;
		}

		$images = $doc->getElementsByTagName( 'img' );
		foreach ( $images as $image ) {

			if ( $image->hasAttribute( 'alt' ) ) {
				$image->setAttribute( 'alt', wpml_ctt_prepare_string( $template, $image->getAttribute( 'alt' ), $lang ) );
			}

			if ( $image->hasAttribute( 'title' ) ) {
				$image->setAttribute( 'title', wpml_ctt_prepare_string( $template, $image->getAttribute( 'title' ), $lang ) );
			}

		}

		// removes doctype
		$doc->removeChild($doc->firstChild);

		// removes html, body and div tags
		$result = str_replace( array('<html>', '</html>', '<body><div>', '</div></body>'), array('', '', '', ''), $doc->saveHTML());
		return $result;

	}


	/**
	 * Add language name to slug
	 *
	 * @param $string
	 * @param $lang
	 *
	 * @return string
	 */
	private function add_language_name_to_slug( $string, $lang ) {

		global $sitepress;
		$language_details = $sitepress->get_language_details( $lang );
		if ( isset( $language_details['english_name'] ) ) {
			return sanitize_title_with_dashes( $language_details['english_name'] . '-' . $string, 'save' );
		}

		return $string;

	}


	/**
	 *
	 * Add language name to custom field (only if is set to translate)
	 *
	 * @param $string
	 * @param $lang
	 * @param $context
	 *
	 * @return string
	 */
	private function add_language_name_to_custom_field( $string, $lang, $context ) {

		//get settings - $this->settings is not set when creating duplicate (not updating)
		global $sitepress_settings;
		$settings =& $sitepress_settings['translation-management'];

		//check for custom fields to translate
		if ( isset( $settings['custom_fields_translation'] ) ) {
			//get information about custom fields to translate
			$custom_fields_translation = $settings['custom_fields_translation'];
			if ( isset( $custom_fields_translation[$context['key']] ) ) {
				//if custom field is set to translate (id = 2)
				if ( $custom_fields_translation[$context['key']] == 2 ) {
					//add language information
					return wpml_ctt_prepare_string( $this->template, $string, $lang );
				}
			}
		}

		return $string;

	}

}

