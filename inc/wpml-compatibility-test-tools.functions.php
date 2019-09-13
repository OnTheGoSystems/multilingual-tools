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
function wpml_ctt_st_contexts() {
    return icl_st_get_contexts( false );
}

/**
 *
 * Generate language checkboxes
 *
 * @param array $selected_languages - arrach of languages (code) that should be checked
 *
 * @return string
 */
function wpml_ctt_active_languages_output( $selected_languages = array() ) {
    $active_langs = apply_filters( 'wpml_active_languages', NULL, 'orderby=id&order=asc' );
    $default_lang = apply_filters( 'wpml_default_language', NULL );

	// Remove default language from list.
    unset( $active_langs[$default_lang] );

    if ( empty( $active_langs ) ) {
        return sprintf( __( 'No active languages set. You can enable languages <a href="%s">here</a>.', 'wpml-compatibility-test-tools' ), admin_url( 'admin.php?page=sitepress-multilingual-cms/menu/languages.php' ) );
    }

    $theme_lang_inputs = '<ul>';

    foreach( $active_langs as $lang => $v ) {
		$checked = in_array( $lang, $selected_languages ) ? 'checked' : '';
        $icon    = '<img src="' . $active_langs[$lang]['country_flag_url']
                                . '" alt="' . $active_langs[$lang]['translated_name']
                                . '" width="18" height="12"> ';

        $theme_lang_inputs .= ' <li><input type="checkbox" ' . $checked . ' id="active_languages" class="active_languages" name="active_languages[]" value="' . $lang .'" />'
                                                         . $icon . $active_langs[$lang]['translated_name'] . '</li>';
    }

    $theme_lang_inputs .= '<a id="active_languages" class="toggle" href="#">Toggle all</a></ul>';

    return $theme_lang_inputs;
}

/**
 *
 * Return names of all custom fields
 *
 * @return mixed
 */
function wpml_get_custom_fields() {
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
    check_ajax_referer( 'wctt-generate', '_wctt_mighty_nonce' );

    $data    = array();
    $options = isset( $_POST['options'] ) ? (array) $_POST['options'] : array();

    $safe_options = wpml_ctt_options_list();

    foreach ( $options as $option ) {
        // Dealing with unwanted.
        if ( ! array_key_exists( $option, $safe_options ) ) {
            $data = ["{$option}" => 'No way Jose!'];
            break;
        }

        // Dealing with bad nested serialization.
	    if ( ! is_serialized( get_option( $option ) ) ) {
		    $data[ $option ] = get_option( $option );
	    } else {
		    $data[ $option ] = '*** WARNING: NESTED SERIALIZATION DETECTED, WILL NOT WORK WITH WPML! ***';
        }
    }

    echo json_encode( $data );
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

    $options = wpml_ctt_load_alloptions();

    foreach ( $options as $name => $value ) {
        if ( in_array( $name, $exclude_list ) || ( ! stristr( $name, '_transient' ) === false ) ) {
            unset( $options[$name] );
        }
    }

    return $options;
}

/**
 *
 * Validate radio values.
 *
 * @param $value
 *
 * @return mixed
 */
function wpml_ctt_validate_radio( $value ) {
	$allowed = array(
		'translate',
		'copy-once',
		'ignore',
		'copy',
		'file',
		'dir',
		'2',
		'1',
		'0'
	);

	if ( in_array( $value, $allowed, true ) ) {
		// When set to display as translated.
		if ( $value === '2' ) {
			return '1';
		}

		return $value;
	}

	return '';
}

/**
 * Loads and caches all options.
 *
 * @global wpdb $wpdb WordPress database abstraction object.
 *
 * @return array List of all options.
 */
function wpml_ctt_load_alloptions() {
	global $wpdb;

	if ( ! wp_installing() || ! is_multisite() ) {
		$alloptions = wp_cache_get( 'wpml_ctt_all_options', 'options' );
	} else {
		$alloptions = false;
	}

	if ( ! $alloptions ) {
		$suppress      = $wpdb->suppress_errors();
		$alloptions_db = $wpdb->get_results( "SELECT option_name, option_value FROM $wpdb->options ORDER BY option_name" );

		$wpdb->suppress_errors( $suppress );

		$alloptions = array();

		foreach ( (array) $alloptions_db as $o ) {
			$alloptions[ $o->option_name ] = $o->option_value;
		}
		if ( ! wp_installing() || ! is_multisite() ) {
			wp_cache_add( 'wpml_ctt_all_options', $alloptions, 'options' );
		}
	}

	return $alloptions;
}

/**
 * Display an entry from a wpml-config.xml file.
 * 
 * @param array $entry
 */
function wpml_ctt_parse_entry( $entry ) {
	if ( isset( $entry['tag']['value'] ) ) {
		// This is for items from the shortcodes section.
		echo '<strong>' . $entry['tag']['value'] . '</strong>';
		if ( ! empty( $entry['attributes']['attribute'] ) ) {
			if ( isset( $entry['attributes']['attribute']['value'] ) ) {
				$entry['attributes']['attribute'] = array( $entry['attributes']['attribute'] );
			}
			$attributes = wp_list_pluck($entry['attributes']['attribute'], 'value' );
			echo ': ' . implode( ', ', $attributes );
		}
		echo '<br />';
	} else if ( isset( $entry['attr']['name'] ) ) {
		// This part if for admin-texts and language-switcher-settings.
		echo '<strong>' . $entry['attr']['name'] . '</strong>: ';
		echo $entry['value'] . '<br />';
		if ( ! empty( $entry['key'] ) ) {
			echo '<blockquote style="margin: 0 1em;">';
			foreach ( $entry['key'] as $key ) {
				wpml_ctt_parse_entry( $key );
			}
			echo '</blockquote>';
		}
	} else {
		// This is for any other type of entry.
		echo '<strong>' . $entry['value'] . '</strong>: ';
		foreach ( $entry['attr'] as $key => $value ) {
			echo $key . ' =&gt; ' . $value . '<br /> ';
		}
	}
}

function mltools_shortcode_helper_add_hooks() {

	$debug_enabled = WPML_Compatibility_Test_Tools::get_option( 'shortcode_enable_debug', false );

	if ( $debug_enabled && is_user_logged_in() ) {

		$debug_values_enabled = WPML_Compatibility_Test_Tools::get_option( 'shortcode_enable_debug_value', false );
		$default_ignored_tags = mltools_shortcode_helper_get_default_ignored_tags();
		$ignored_tags         = array_merge( $default_ignored_tags, array_map( 'trim',
			explode( ',', WPML_Compatibility_Test_Tools::get_option( 'shortcode_ignored_tags', '' ) )
		) );

		$shortcode_attribute_filter = new MLTools_Shortcode_Attribute_Filter( $ignored_tags );
		$shortcode_attribute_filter->add_hooks();

		MLTools_Shortcode_WPML_Config_Parser::add_hooks();

		if ( ! is_admin() ) {
			add_action( 'shutdown', 'mltools_shortcode_helper_unregistered_print_xml', 20 );
			if ( $debug_values_enabled ) {
				add_action( 'shutdown', 'mltools_shortcode_helper_unregistered_print_captured_values', 30 );
			}
		}

	}
}

function mltools_shortcode_helper_get_default_ignored_tags() {
	$default = array(
		'vc_row',
		'vc_column',
		'vc_row_inner',
		'vc_column_inner',
		'vc_basic_grid',
		'vc_empty_space',
		'vc_icon',
		'vc_separator',
		'audio',
		'caption',
		'embed',
		'gallery',
		'playlist',
		'video',
		'wp_caption',
		'wpml-string',
		'wpml_language_form_field',
		'wpml_language_selector_footer',
		'wpml_language_selector_widget',
		'wpml_language_switcher',
	);
	sort( $default );

	return $default;
}

function mltools_shortcode_helper_unregistered_print_xml() {

	$output = mltools_shortcode_helper_unregistered_get_xml_output();

	if ( $output === false ) {

		user_error( 'MLTools shortcode helper: WPML_Config not loaded' );

	} elseif ( is_string( $output ) && ! empty( $output ) ) {

		echo '<pre style="padding:1em; background-color: #f8f8f8; color: #0a001f">'
		     . htmlentities( $output ) . '</pre>';
	}
}

function mltools_shortcode_helper_unregistered_print_captured_values(){

		$unregistered_tags = mltools_shortcode_helper_get_unregistered_tags();
		$captured_values = get_option( MLTools_Shortcode_Attribute_Filter::OPTION_NAME_VALUES, array() );

		foreach ( $captured_values as $tag => $values ) {

			if ( array_key_exists( $tag, $unregistered_tags )) {

				echo '<pre style="padding:1em; background-color: #f8f8f8; color: #0a001f">' . $tag . '<ul>';

				foreach ( $values['attributes'] as $attr_name => $attr_value ) {
					echo "<li>{$attr_name}: <span style=\"background-color: #000000; color: #FFFFFF; padding-left: 1em; padding-right: 1em;\">{$attr_value}</span></li>";
				}

				echo '</ul></pre>';
			}
		}
}

/**
 * @return bool|string
 */
function mltools_shortcode_helper_unregistered_get_xml_output() {

	$xml_helper    = new MLTools_XML_Helper();
	$captured_tags = mltools_shortcode_helper_get_unregistered_tags();

	if ( $captured_tags === false ) {
		return false;
	}

	if ( is_array( $captured_tags ) && ! empty( $captured_tags ) ) {
		return $xml_helper->get_dom_shortcodes( $captured_tags );
	}

	return '';
}

/**
 * @return bool|array
 */
function mltools_shortcode_helper_get_unregistered_tags() {

	$wpml_config = MLTools_Shortcode_WPML_Config_Parser::get_config();

	if ( $wpml_config === false ) {
		return false;
	}

	$captured_tags        = get_option( MLTools_Shortcode_Attribute_Filter::OPTION_NAME, array() );
	$default_ignored_tags = mltools_shortcode_helper_get_default_ignored_tags();
	$ignored_tags         = array_merge( $default_ignored_tags, array_map( 'trim',
		explode( ',', WPML_Compatibility_Test_Tools::get_option( 'shortcode_ignored_tags', '' ) )
	) );

	foreach ( $captured_tags as $tag => $config ) {
		if ( array_key_exists( $tag, $wpml_config ) || in_array( $tag, $ignored_tags ) ) {
			unset( $captured_tags[ $tag ] );
		}
	}

	return $captured_tags;
}