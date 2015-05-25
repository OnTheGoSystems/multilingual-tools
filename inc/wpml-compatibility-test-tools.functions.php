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
function wpml_get_custom_fields(){
	global $wpdb;

	return $wpdb->get_results( "SELECT DISTINCT(meta_key) FROM $wpdb->postmeta" );

}

/**
 *
 * Returning through AJAX selected option array as JSON.
 *
 */
add_action( 'wp_ajax_wpml_ctt_action', 'wpml_ctt_options_list_ajax' );
function wpml_ctt_options_list_ajax() {

    $option = get_option($_POST['option']);

    if (!is_array($option)) {
        $option = array($_POST['option'] => $option);
    }

    if ($_POST['option'] == 'all') {
        $option = wpml_ctt_options_list();
    }

    echo json_encode($option);
    wp_die();

}

/**
 *
 * Creating options list by filtering results from wp_options table.
 *
 * @return array
 *
 */
function wpml_ctt_options_list() {

    $exclude_list = array(

        /* WP default ones */

        'siteurl',
        'home',
        'blogname',
        'blogdescription',
        'users_can_register',
        'admin_email',
        'start_of_week',
        'use_balanceTags',
        'use_smilies',
        'require_name_email',
        'comments_notify',
        'posts_per_rss',
        'rss_use_excerpt',
        'mailserver_url',
        'mailserver_login',
        'mailserver_pass',
        'mailserver_port',
        'default_category',
        'default_comment_status',
        'default_ping_status',
        'default_pingback_flag',
        'posts_per_page',
        'date_format',
        'time_format',
        'links_updated_date_format',
        'comment_moderation',
        'moderation_notify',
        'permalink_structure',
        'gzipcompression',
        'hack_file',
        'blog_charset',
        'active_plugins',
        'category_base',
        'ping_sites',
        'advanced_edit',
        'comment_max_links',
        'gmt_offset',
        'default_email_category',
        'template',
        'stylesheet',
        'comment_whitelist',
        'comment_registration',
        'html_type',
        'use_trackback',
        'default_role',
        'db_version',
        'uploads_use_yearmonth_folders',
        'upload_path',
        'blog_public',
        'default_link_category',
        'show_on_front',
        'tag_base',
        'show_avatars',
        'avatar_rating',
        'upload_url_path',
        'thumbnail_size_w',
        'thumbnail_size_h',
        'thumbnail_crop',
        'medium_size_w',
        'medium_size_h',
        'avatar_default',
        'large_size_w',
        'large_size_h',
        'image_default_link_type',
        'image_default_size',
        'image_default_align',
        'close_comments_for_old_posts',
        'close_comments_days_old',
        'thread_comments',
        'thread_comments_depth',
        'page_comments',
        'comments_per_page',
        'default_comments_page',
        'comment_order',
        'sticky_posts',
        'widget_categories',
        'widget_text',
        'widget_rss',
        'timezone_string',
        'page_for_posts',
        'page_on_front',
        'default_post_format',
        'link_manager_enabled',
        'initial_db_version',
        'wp_user_roles',
        'widget_search',
        'widget_recent-posts',
        'widget_recent-comments',
        'widget_archives',
        'widget_meta',
        'sidebars_widgets',
        'cron',
        'rewrite_rules',
        'can_compress_scripts',
        'recently_activated',
        'blacklist_keys',
        'moderation_keys',
        'links_recently_updated_prepend',
        'links_recently_updated_append',
        'links_recently_updated_time',
        'embed_autourls',
        'embed_size_w',
        'embed_size_h',
        'secret',
        'use_linksupdate',
        'rss_language',
        'default_post_edit_rows',
        'enable_app',
        'enable_xmlrpc',
        'recently_edited',
        'auto_core_update_notified',
        'db_upgraded',

        /* WPML added ones */

        '_icl_cache',
        '_wpml_media',
        'icl_adl_settings',
        'icl_admin_messages',
        '_icl_admin_option_names',
        'icl_sitepress_settings',
        'icl_sitepress_version',
        'icl_translation_jobs_basket',
        'widget_icl_lang_sel_widget',
        'wp_icl_non_translators_cached',
        'wp_icl_translators_cached',
        'wpml_config_files_arr',
        'wpml_config_index',
        'wpml_config_index_updated',
        'wpml-package-translation-db-updates-run',
        'wpml-package-translation-refresh-required',
        'wpml-package-translation-string-packages-table-updated',
        'wpml-package-translation-string-table-updated',
        'WPML_CMS_NAV_VERSION',
        'wpml_tm_version',
        'wp_installer_settings',
        'wpml_cms_nav_settings',
        'wpml_ctt_settings' );

    $options = wp_load_alloptions();

    foreach ( $options as $name => $value ) {
        if ( in_array($name, $exclude_list) || (!stristr($name, '_transient') === false) ) {
            unset($options[$name]);
        }
    }

    return $options;
}