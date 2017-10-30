<div class="wrap">

	<div id="icon-wpml" class="icon32"><br /></div>
	<h2><?php _e( 'WPML Configuration Debug', 'wpml-compatibility-test-tools' ); ?></h2>

	<h3><?php _e( 'Configuration files loaded', 'wpml-compatibility-test-tools' ); ?></h3>
	<?php WPML_Config::load_config_run(); ?>

	<?php
	global $wpml_config_debug;

	// TODO: Administration texts and language switcher settings.
	$data = array();
	if ( ! empty( $wpml_config_debug['wpml-config']['custom-types']['custom-type'] ) ) {
		$data['Custom posts'] = $wpml_config_debug['wpml-config']['custom-types']['custom-type'];
	}
	if ( ! empty( $wpml_config_debug['wpml-config']['taxonomies']['taxonomy'] ) ) {
		$data['Custom taxonomies'] = $wpml_config_debug['wpml-config']['taxonomies']['taxonomy'];
	}
	if ( ! empty( $wpml_config_debug['wpml-config']['custom-fields']['custom-field'] ) ) {
		$data['Custom fields translation'] = $wpml_config_debug['wpml-config']['custom-fields']['custom-field'];
	}
	if ( ! empty( $wpml_config_debug['wpml-config']['custom-types']['custom-term-fields']['custom-term-field'] ) ) {
		$data['Custom Term Meta Translation'] = $wpml_config_debug['wpml-config']['custom-term-fields']['custom-term-field'];
	}
	if ( ! empty( $wpml_config_debug['wpml-config']['shortcodes']['shortcode'] ) ) {
		$data['Shortcodes'] = $wpml_config_debug['wpml-config']['shortcodes']['shortcode'];
	}
	?>
	<?php foreach ( $data as $type => $config ) : ?>
		<?php if ( ! empty( $config ) ) : ?>
			<h3><?php _e( $type, 'sitepress' ) ?></h3>
			<?php foreach( $config as $entry ) : ?>
				<?php wpml_ctt_parse_entry( $entry ); ?>
			<?php endforeach; ?>
		<?php endif; ?>
	<?php endforeach; ?>

</div>
