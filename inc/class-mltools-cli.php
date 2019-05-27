<?php

class MLTools_CLI {

	private $sitepress, $ctt, $original_settings;

	private $settings = 'a:6:{s:30:"string_auto_translate_template";s:35:"[%language_name%] %original_string%";s:26:"duplicate_strings_template";s:35:"[%language_code%] %original_string%";s:22:"shortcode_enable_debug";b:0;s:28:"shortcode_enable_debug_value";b:0;s:22:"shortcode_ignored_tags";b:0;s:17:"duplicate_strings";a:4:{s:4:"post";a:3:{s:5:"title";s:1:"1";s:7:"content";s:1:"1";s:7:"excerpt";s:1:"1";}s:12:"custom_field";a:1:{s:5:"value";s:1:"1";}s:8:"taxonomy";a:1:{s:3:"all";s:1:"1";}s:13:"taxonomy_slug";a:1:{s:3:"all";s:1:"1";}}}';

	function __construct( SitePress $sitepress, WPML_Compatibility_Test_Tools $ctt ) {
		$this->sitepress         = $sitepress;
		$this->ctt               = $ctt;
		$this->original_settings = get_option( 'wpml_ctt_settings' );
		update_option( $this->ctt::OPTIONS_NAME, maybe_unserialize( $this->settings ) );
		// Fix missing tables
		WPML_Package_Translation_Schema::run_update();
	}

	/**
	 * Duplicates posts.
	 *
	 * * ---
	 * default: success
	 * options:
	 *   - success
	 *   - error
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp mltools duplicate
	 *
	 * @when after_wp_load
	 */
	public function duplicate() {

		$default_language = $this->sitepress->get_default_language();
		$languages        = array_keys( $this->sitepress->get_active_languages() );
		$post_type        = array_keys( $this->sitepress->get_translatable_documents() );

		$this->sitepress->switch_lang( $default_language );
		$post_ids = get_posts( array(
			'post_type'        => $post_type,
			'post_status'      => 'all',
			'fields'           => 'ids',
			'suppress_filters' => false,
			'posts_per_page'   => - 1,
		) );

		WP_CLI::log( 'Duplicating ' . implode( ', ', $post_type ) );

		$count = 0;
		foreach ( $post_ids as $post_id ) {

			$duplicates    = array_keys( $this->sitepress->get_duplicates( $post_id ) );
			$duplicated_to = array();

			foreach ( $languages as $language ) {
				if ( $language == $default_language ) {
					continue;
				}
				if ( ! in_array( $language, $duplicates ) ) {
					$this->sitepress->make_duplicate( intval( $post_id ), $language );
					$duplicated_to[] = $language;
					$count ++;
				}
				// @todo Translate page-builder strings
			}

			if ( empty( $duplicated_to ) ) {
				$duplicated_to = array( 'none' );
			}

			WP_CLI::log( "-- post_ID: {$post_id} [" . implode( ',', $duplicated_to ) . "]" );
		}

		if ( $count ) {
			WP_CLI::success( "duplicated {$count} post(s)" );
		} else {
			WP_CLI::error( 'no posts for duplication' );
		}
	}

	/**
	 * Translates strings.
	 *
	 * ## OPTIONS
	 *
	 * <type>
	 * : Type of content to translate.
	 *
	 * [--<field>=<value>]
	 * : Use context name.
	 * ---
	 * default: success
	 * options:
	 *   - success
	 *   - error
	 * ---
	 *
	 * ## EXAMPLES
	 *
	 *     wp mltools translate strings --context=all_contexts
	 *
	 * @when after_wp_load
	 */
	public function translate( $type, $assoc_args ) {

		$context  = array_key_exists( 'context', $assoc_args ) ? $assoc_args['context'] : 'all_contexts';
		$settings = maybe_unserialize( $this->settings );

		WP_CLI::log( "Context: {$context}" );

		switch ( $type ) {
			case 'string':
			case 'strings':
			default:
				$this->ctt->translate_strings_in_context(
					$context,
					array_keys( $this->sitepress->get_active_languages() ),
					$settings['string_auto_translate_template']
				);
		}

		WP_CLI::success( 'strings translated' );
	}

	function __destruct() {
		update_option( $this->ctt::OPTIONS_NAME, $this->original_settings );
	}
}