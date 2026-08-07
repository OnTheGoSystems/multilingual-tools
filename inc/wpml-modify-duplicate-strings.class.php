<?php

/**
 * Class Modify_Duplicate_Strings
 *
 * Example how to use filter wpml_duplicate_generic_string
 * to modify strings (title, content, terms, custom fields) for duplicates
 *
 */
class Modify_Duplicate_Strings {

	private $filter   = array();
	private $template = '';

	public function __construct( $filter = array(), $template = '[%language_name%] %original_string%' ) {
		$this->filter   = $filter;
		$this->template = $template;
		add_filter( 'wpml_duplicate_generic_string', array( $this, 'duplicate_generic_string' ), 10, 3 );
	}

	/**
	 *
	 * Add information about language to string based on context
	 *
	 * @param $string - string to modify
	 * @param $lang - language code
	 * @param $context - array(
	 *      'context'   => 'post' or 'custom_field' or 'taxonomy',
	 *      'attribute' => 'title' or 'content' or 'excerpt' (for a post), 'value' (for a custom field), '{taxonomy_name}' (for a taxonomy),
	 *      'key'       => '{post_id}' | '{meta_key}' | '{term_id}',
	 * );
	 *
	 * @return string
	 */
	public function duplicate_generic_string( $string, $lang, $context ) {

		// Check context
		$filter_context = isset( $context['context'] ) ? $context['context'] : '';
		$attribute      = isset( $context['attribute'] ) ? $context['attribute'] : '';

		// Check if user required to filter given string type (based on selected settings in admin panel)
		if ( isset( $this->filter[ $filter_context ] ) ) {
			// Special case for taxonomy
			if ( in_array( $filter_context, array( 'taxonomy', 'taxonomy_slug' ) ) ) {
				if ( ! isset( $this->filter[ $filter_context ]['all'] ) ) {
						return $string;
				}
			} elseif ( ! isset( $this->filter[ $filter_context ][ $attribute ] ) ) {
				return $string;
			}

		} else {
			return $string;
		}

		// Based on context
		switch ( $filter_context ) {
			case 'post':

				// Exception for empty excerpt field
				if ( ( 0 === strcmp( $attribute, 'excerpt' ) ) && ( empty( $string ) ) ) {
					break;
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

		// By default return the same value
		return $string;
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

		// If custom field is set to translate (id = 2)
		if ( 2 === $this->get_custom_field_translation_preference( $context['key'] ) ) {
			// Add language information
			return wpml_ctt_prepare_string( $this->template, $string, $lang );
		}

		return $string;
	}

	/**
	 * WPML 5.0 moves the custom-field preferences out of the icl_sitepress_settings
	 * blob into a per-key store that WPML reads through PreferenceResolver. Older
	 * WPML versions only have the blob, so both sources are checked.
	 *
	 * @param string $field_key
	 *
	 * @return int 0 when the field has no preference.
	 */
	private function get_custom_field_translation_preference( $field_key ) {
		if ( class_exists( '\WPML\TM\Settings\PreferenceResolver' ) ) {
			return (int) \WPML\TM\Settings\PreferenceResolver::mode(
				\WPML\Core\Component\CustomFieldPreferences\Domain\ElementType::POST,
				$field_key
			);
		}

		// Get settings - $this->settings is not set when creating duplicate (not updating)
		global $sitepress_settings;

		if ( isset( $sitepress_settings['translation-management']['custom_fields_translation'][ $field_key ] ) ) {
			return (int) $sitepress_settings['translation-management']['custom_fields_translation'][ $field_key ];
		}

		return 0;
	}
}