<?php

$debug_enabled = WPML_Compatibility_Test_Tools::get_option( 'shortcode_enable_debug', false );

if ( $debug_enabled ) {
	$debug_value_enabled = WPML_Compatibility_Test_Tools::get_option( 'shortcode_enable_debug_value', false );
	if ( $debug_value_enabled ) {
		$captured_values = get_option( MLTools_Shortcode_Attribute_Filter::OPTION_NAME_VALUES, array() );
	}
	$ignored_tags         = WPML_Compatibility_Test_Tools::get_option( 'shortcode_ignored_tags', false );
	$unregistered_tags    = mltools_shortcode_helper_get_unregistered_tags();
	$default_ignored_tags = mltools_shortcode_helper_get_default_ignored_tags();
	$xml_output           = mltools_shortcode_helper_unregistered_get_xml_output();
}

?>
<table id="wctt-settings" class="widefat general_options_table">
    <thead>
    <tr>
        <th><h3><?php _e( 'Shortcode Helper', 'wpml-compatibility-test-tools' ) ?></h3></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

                <label><?php _e( 'Enable debug output', 'wpml-compatibility-test-tools' ); ?></label>
                <ul class="holder">
                    <li>
                        <input type="checkbox" name="shortcode_enable_debug"
                               value="1" <?php checked( $debug_enabled ); ?> />
                    </li>
                </ul>

				<?php if ( $debug_enabled ) { ?>

                    <label><?php _e( 'Unregistered tags', 'wpml-compatibility-test-tools' ); ?></label>
                    <ul class="holder">
                        <li>
							<?php if ( $unregistered_tags ) { ?>
                                <ol>
                                    <li><?php echo implode( '</li><li>', array_keys( $unregistered_tags ) ); ?></li>
                                </ol>
							<?php } else { ?>
								<?php _e( 'No unregistered tags captured', 'wpml-compatibility-test-tools' ); ?>
							<?php } ?>
                        </li>
                    </ul>

					<?php if ( $unregistered_tags ) { ?>
                        <label><?php _e( 'XML output', 'wpml-compatibility-test-tools' ); ?></label>
                        <ul class="holder">
                            <li>
                            <textarea readonly="readonly"
                                      style="min-width: 450px;min-height: 150px;"><?php echo htmlentities( $xml_output ); ?></textarea>
                            </li>
                        </ul>
					<?php } ?>

                    <label><?php _e( 'Add ignored tags as CSV', 'wpml-compatibility-test-tools' ); ?></label>
                    <ul class="holder">
                        <li>
                        <textarea name="shortcode_ignored_tags"
                                  style="min-width: 450px;"><?php echo $ignored_tags ? $ignored_tags : ''; ?></textarea>
                        </li>
                    </ul>

					<?php if ( $default_ignored_tags ) { ?>
                        <label><?php _e( 'Ignored tags', 'wpml-compatibility-test-tools' ); ?></label>
                        <ul class="holder">
                            <li>
                                <ol>
                                    <li><?php echo implode( '</li><li>', $default_ignored_tags ); ?></li>
                                </ol>
                            </li>
                        </ul>
					<?php } ?>

                    <label><?php _e( 'Display captured values', 'wpml-compatibility-test-tools' ); ?></label>
                    <ul class="holder">
                        <li>
                            <input type="checkbox" name="shortcode_enable_debug_value"
                                   value="1" <?php checked( $debug_value_enabled ); ?> />
                        </li>
                    </ul>

					<?php if ( $debug_value_enabled && $captured_values ) { ?>
                        <label><?php _e( 'Captured values', 'wpml-compatibility-test-tools' ); ?></label>
                        <ul class="holder">
                            <li><?php foreach ( $captured_values

								as $tag => $values ) { ?>
								<?php if ( array_key_exists( $tag, $unregistered_tags ) ) { ?>
                                    <strong><?php echo $tag; ?></strong>
                                    <ul><?php foreach ( $values['attributes'] as $attr_name => $attr_value ) { ?>
                                            <li style="padding-left: 2em"><?php echo "{$attr_name}: <span style=\"background-color: #000000; color: #FFFFFF; padding-left: 1em; padding-right: 1em;\">{$attr_value}</span>"; ?></li><?php } ?>
                                    </ul>
								<?php } ?></li>
							<?php } ?></li>
                        </ul>
					<?php } ?>

				<?php } ?>

                <input type="submit" name="shortcode_debug_action_save"
                       value="<?php _e( 'Save settings', 'wpml-compatibility-test-tools' ); ?>"
                       class="button-secondary button"/>

				<?php if ( $debug_enabled ) { ?>
                    <input type="submit" name="shortcode_debug_action_reset"
                           value="<?php _e( 'Reset debug data', 'wpml-compatibility-test-tools' ); ?>"
                           class="button-primary button"/>
				<?php } ?>

				<?php wp_nonce_field( 'mltools_shortcode_helper_settings_save', '_mltools_shortcode_helper_nonce' ); ?>

            </form>
        </td>
    </tr>
    </tbody>
</table>