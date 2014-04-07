<?php

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

