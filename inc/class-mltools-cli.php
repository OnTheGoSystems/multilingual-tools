<?php

class MLTools_CLI {

	private $sitepress;

	function __construct( SitePress $sitepress ) {
		$this->sitepress = $sitepress;
	}

	/*public function __invoke( $args ) {
		WP_CLI::success( $args[0], $args );
	}*/

	private $settings = 'a:6:{s:30:"string_auto_translate_template";s:35:"[%language_name%] %original_string%";s:26:"duplicate_strings_template";s:35:"[%language_code%] %original_string%";s:22:"shortcode_enable_debug";b:0;s:28:"shortcode_enable_debug_value";b:0;s:22:"shortcode_ignored_tags";b:0;s:17:"duplicate_strings";a:4:{s:4:"post";a:3:{s:5:"title";s:1:"1";s:7:"content";s:1:"1";s:7:"excerpt";s:1:"1";}s:12:"custom_field";a:1:{s:5:"value";s:1:"1";}s:8:"taxonomy";a:1:{s:3:"all";s:1:"1";}s:13:"taxonomy_slug";a:1:{s:3:"all";s:1:"1";}}}';

	/**
	 * Duplicates posts.
	 *
	 * ## OPTIONS
	 *
	 * #<language>
	 * #: Target language.
	 *
	 * [--prefix=<string>]
	 * : Translated string prefix.
	 * ---
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
	public function duplicate( $args, $assoc_args ) {

		$settings = maybe_unserialize( $this->settings );
		// Fix missing tables
		WPML_Package_Translation_Schema::run_update();
		update_option( 'wpml_ctt_settings', $settings );

		$modify = new Modify_Duplicate_Strings(
			$settings['duplicate_strings'],
			$settings['duplicate_strings_template']
		);

		$default_language = $this->sitepress->get_default_language();
		$languages        = array_keys( $this->sitepress->get_active_languages() );
		$post_type        = array_keys( $this->sitepress->get_translatable_documents() );

		do_action( 'wpml_switch_language', $default_language );
		$post_ids = get_posts( array(
			'post_type'        => $post_type,
			'post_status'      => 'all',
			'fields'           => 'ids',
			'suppress_filters' => false,
			'posts_per_page'   => - 1,
		) );

		WP_CLI::log( 'Duplicating ' . implode( ', ', $post_type ) );
		$count = 0;

		foreach ( $languages as $language ) {
			if ( $language == $default_language ) {
				continue;
			}
			WP_CLI::log( "Language {$language}" );
			foreach ( $post_ids as $post_id ) {
				// Exits script if already duplicated
				$existing = array_keys( $this->sitepress->get_duplicates( $post_id ) );
				if ( ! in_array( $language, $existing ) ) {
					$this->sitepress->make_duplicate( intval( $post_id ), $language );
					WP_CLI::log( "--post_ID: {$post_id}" );
					$count ++;
				}
			}
		}
		WP_CLI::success( "duplicated {$count} post(s)" );
	}
}