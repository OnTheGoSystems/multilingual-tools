<?php

function wpml_ctt_st_contexts(){
    return icl_st_get_contexts(false);
}


function wpml_ctt_active_languages_output( ){
    global $sitepress;
							
    $active_langs = $sitepress->get_active_languages();
    $default_lang = $sitepress->get_default_language();
    $theme_lang_inputs = '';
    
    unset($active_langs[$default_lang]);
							
    foreach( $active_langs as $lang => $v ){			
        $theme_lang_inputs .= ' <input type="checkbox" name="active_languages[]" value="'. $lang .'" /> ' . $active_langs[$lang]['english_name']; 

    }
    
    return $theme_lang_inputs;
							    
}

