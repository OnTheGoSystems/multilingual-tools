<?php

/**
 * Prepare string based on template
 *
 * @param $template
 * @param $string
 * @param $lang
 *
 * @return mixed
 */
function wpml_ctt_prepare_string( $template, $string, $lang ) {

	global $sitepress;

	$template = str_replace( '%original_string%', $string, $template );

	$language_details = $sitepress->get_language_details( $lang );

	if ( isset( $language_details['english_name'] ) ) {
		$template = str_replace( '%language_name%', $language_details['english_name'], $template );
	}

	if ( isset( $language_details['code'] ) ) {
		$template = str_replace( '%language_code%', $language_details['code'], $template );
	}

	if ( isset( $language_details['display_name'] ) ) {
		$template = str_replace( '%language_native_name%', $language_details['display_name'], $template );
	}


	return $template;

}


/**
 *
 * Return list of contexts for string translation
 *
 * @return mixed
 */
function wpml_ctt_st_contexts(){
    return icl_st_get_contexts(false);
}

/**
 *
 * Generate language checkboxes
 *
 * @param array $selected_languages - arrach of languages (code) that should be checked
 *
 * @return string
 */
function wpml_ctt_active_languages_output( $selected_languages = array() ){

//	if(!is_array($selected_languages)) return false;

    global $sitepress;
							
    $active_langs = $sitepress->get_active_languages();
    $default_lang = $sitepress->get_default_language();
    $theme_lang_inputs = '';

	//remove default language from list
    unset($active_langs[$default_lang]);
							
    foreach( $active_langs as $lang => $v ){
		$checked = in_array($lang, $selected_languages  ) ? 'checked' : '';
        $theme_lang_inputs .= ' <input type="checkbox" '.$checked.' name="active_languages[]" value="'. $lang .'" /> ' . $active_langs[$lang]['english_name'];

    }
    
    return $theme_lang_inputs;
							    
}


/**
 *
 * Return names of all custom fields
 *
 * @return mixed
 */
function wpml_get_custom_fields(  ){
	global $wpdb;


	return $wpdb->get_results( "SELECT DISTINCT(meta_key) FROM $wpdb->postmeta" );

}