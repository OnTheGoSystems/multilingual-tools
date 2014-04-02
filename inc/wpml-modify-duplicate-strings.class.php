<?php
/**
 * Class Modify_Duplicate_Strings
 *
 * Example how to use filter icl_duplicate_generic_string
 * to modify strings (title, content, terms, custom fields) for duplicates
 *
 */
class Modify_Duplicate_Strings{

	private $settings = array();

	public function __construct($settings = array()){
		$this->settings = $settings;
		add_filter( 'icl_duplicate_generic_string', array($this, 'icl_duplicate_generic_string'), 10, 3 );
	}

	/**
	 *
	 * Add information about language to string based on context
	 *
	 * @param $string - string to modify
	 * @param $lang - language code
	 * @param $context - array (context, attribute, key)
	 *
	 * @return string
	 */
	public function icl_duplicate_generic_string( $string, $lang, $context ){

		//check context
		$filter_context = isset( $context['context'] )?$context['context']:'';

		//based on context
		switch( $filter_context ) {
			case 'post':
				$string = $this->add_language_name_to_string($string, $lang);
				break;
			case 'taxonomy':
				$string = $this->add_language_name_to_string($string, $lang);
				break;
			case 'taxonomy_slug' :
				$string = $this->add_language_name_to_slug($string, $lang);
				break;
			case 'custom_field' :
				$string = $this->add_language_name_to_custom_field($string, $lang, $context);
				break;
		}

		//by default return the same value
		return  $string;

	}


	/**
	 * Add language name to slug
	 *
	 * @param $string
	 * @param $lang
	 *
	 * @return string
	 */
	private function add_language_name_to_slug( $string, $lang ){

		global $sitepress;
		$language_details = $sitepress->get_language_details($lang);
		if ( isset( $language_details['english_name'] ) ){
			return sanitize_title_with_dashes( $language_details['english_name'].'-'.$string, 'save') ;
		}

		return  $string;

	}

	/**
	 * Add language name to string
	 *
	 * @param $string
	 * @param $lang
	 *
	 * @return string
	 */
	private function add_language_name_to_string( $string, $lang ){

		global $sitepress;
		$language_details = $sitepress->get_language_details($lang);
		if ( isset( $language_details['english_name'] ) ){
			return '['.strtoupper($language_details['english_name']).'] '.$string;
		}

		return  $string;

	}

	/**
	 *
	 * Add language name to custom field (only if set to translate)
	 *
	 * @param $string
	 * @param $lang
	 * @param $context
	 *
	 * @return string
	 */
	private function add_language_name_to_custom_field($string, $lang, $context){

		//get settings - $this->settings is not set when creating duplicate (not updating)
		global $sitepress_settings;
		$settings =& $sitepress_settings['translation-management'];

		//check for custom fields to translate
		if( isset( $settings['custom_fields_translation'] ) ){
			//get information about custom fields to translate
			$custom_fields_translation = $settings['custom_fields_translation'];
			if ( isset( $custom_fields_translation[$context['key']] ) ){
				//if custom field is set to translate (id = 2)
				if ( $custom_fields_translation[$context['key']] == 2  ){
					//add language information
					return $this->add_language_name_to_string($string, $lang);
				}
			}
		}

		return $string;

	}

}

