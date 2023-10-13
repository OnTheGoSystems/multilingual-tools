<?php
require_once WPML_CTT_PATH . '/inc/class-mltools-custom-fields-translation.php';
$mt_custom_fields_translation = new MLTools_Custom_Fields_Translation();
$custom_fields                = $mt_custom_fields_translation->get_custom_fields();
$translation_preferences      = $mt_custom_fields_translation->determine_translation_preference();
?>
<div class="wrap">

	<h2><?php _e( 'Custom Field Settings Helper', 'wpml-compatibility-test-tools' ); ?></h2>

	<div class="description">
		<p><?php echo sprintf( __( 'The following table shows the custom fields with no translation preferences and suggests a translation preference for each field. You can review them and generate the XML. Once the XML is generated, you can copy it and paste it in the <a href="%s">Custom XML Configuration</a> tab in WPML.', 'wpml-compatibility-test-tools' ), esc_url( admin_url( 'admin.php?page=tm%2Fmenu%2Fsettings&sm=custom-xml-config' ) ) ); ?></p>
	</div>

	<form id="wpml-cf-form">
		<table class="widefat">
			<?php if ( $custom_fields ): ?>
			<thead>
			<tr>
				<th><?php esc_html_e( 'Custom fields', 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Don't translate", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Copy", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Copy once", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Translate", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Sample", 'wpml-compatibility-test-tools' ); ?></th>
			</tr>
			</thead>
			<tbody class="wctt">
			<?php foreach ( $translation_preferences as $custom_field => $translation_preference ): ?>
				<tr>
					<td>
						<label>
							<?php echo esc_html( $custom_field ); ?>
						</label>
					</td>
					<td title="<?php esc_attr_e( "Don't translate", 'wpml-compatibility-test-tools' ); ?>">
						<input id="cf_0_<?php echo esc_attr( $custom_field ); ?>" type="radio"
						       name="cf[<?php echo esc_attr( $custom_field ); ?>]" value="ignore"/>
					</td>
					<td title="<?php esc_attr_e( "Copy", 'wpml-compatibility-test-tools' ); ?>">
						<input id="cf_1_<?php echo esc_attr( $custom_field ); ?>" type="radio"
						       name="cf[<?php echo esc_attr( $custom_field ); ?>]"
						       value="copy" <?php checked( $translation_preference, 'copy' ); ?>/>
					</td>
					<td title="<?php esc_attr_e( "Copy once", 'wpml-compatibility-test-tools' ); ?>">
						<input id="cf_2_<?php echo esc_attr( $custom_field ); ?>" type="radio"
						       name="cf[<?php echo esc_attr( $custom_field ); ?>]" value="copy-once"/>
					</td>
					<td title="<?php esc_attr_e( "Translate", 'wpml-compatibility-test-tools' ); ?>">
						<input id="cf_3_<?php echo esc_attr( $custom_field ); ?>" type="radio"
						       name="cf[<?php echo esc_attr( $custom_field ); ?>]"
						       value="translate" <?php checked( $translation_preference, 'translate' ); ?>/>
					</td>
					<td>
						<?php
						global $wpdb;

						$value = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM $wpdb->postmeta WHERE meta_key = %s LIMIT 1", $custom_field ) );

						// Define the maximum number of characters to display
						$maxLength = 50;

						// If the value length is greater than the defined maximum length,
						// cut it down and append '...' to indicate that it's truncated
						if ( $value && strlen( $value ) > $maxLength ) {
							$value = substr( $value, 0, $maxLength ) . "...";
						} elseif ( empty( $value ) ) {
							$value = 'N/A';
						}

						?><code><?php echo $value; ?></code>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>
			<tfoot>
			<tr>
				<th><?php esc_html_e( 'Custom fields', 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Don't translate", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Copy", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Copy once", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Translate", 'wpml-compatibility-test-tools' ); ?></th>
				<th><?php esc_html_e( "Sample", 'wpml-compatibility-test-tools' ); ?></th>
			</tr>
			</tfoot>
		</table>
		<?php wp_nonce_field( 'wpml_cf_nonce', 'wpml_cf_nonce' ); ?>
		<input type="hidden" name="action" value="wpml_cf_generate_xml"/>
		<br>
		<button class="button-primary"
		        type="submit"><?php esc_html_e( 'Generate XML', 'wpml-compatibility-test-tools' ); ?></button>
		<button class="button-primary" id="copy-xml" type="button"
		        disabled><?php esc_html_e( 'Copy XML', 'wpml-compatibility-test-tools' ); ?></button>
	</form>
	<br>
	<textarea id="xml-output" readonly style="width: 90%; min-height: 300px; background-color: #fff"></textarea>
	<?php else: ?>
		<thead>
		<tr>
			<th colspan="5"><?php esc_html_e( 'Custom Fields', 'wpml-compatibility-test-tools' ); ?></th>
		</tr>
		</thead>
		<tbody class="wctt">
		<tr>
			<td colspan="5"><?php esc_html_e( 'It looks like all custom fields have their translation preferences.', 'wpml-compatibility-test-tools' ); ?></td>
		</tr>
		</tbody>
	<?php endif; ?>
	</table>


</div>
