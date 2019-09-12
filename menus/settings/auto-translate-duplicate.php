<table id="wctt-settings" class="widefat general_options_table">
	<thead>
	<tr>
		<th><h3><?php _e( 'Add language information to post duplicate', 'wpml-compatibility-test-tools' ) ?></h3></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>
			<p><?php printf( __( "Select which elements in posts to auto-translate. Once you've saved your selection, go to the %s and duplicate the site's content.", 'wpml-compatibility-test-tools' ), "<a href=\"" . admin_url( 'admin.php?page=' . basename( WPML_TM_PATH ) . '/menu/main.php' ) . "\">" . __( 'Translation Dashboard', 'wpml-compatibility-test-tools' ) . "</a>"  ); ?></p>
		</td>
	</tr>
	<tr>
		<td>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<?php $duplicate_strings = WPML_Compatibility_Test_Tools::get_option( 'duplicate_strings', array() );?>

				<label><?php _e( 'Select options:', 'wpml-compatibility-test-tools' ); ?></label>
				<ul class="holder">
					<li><input type="checkbox" id="duplicate_strings_to_translate" name="duplicate_strings_to_translate[post][title]" value="1" <?php checked( isset( $duplicate_strings['post']['title'] ) ); ?> /><?php _e( 'Post title', 'wpml-compatibility-test-tools' ); ?></li>
					<li><input type="checkbox" id="duplicate_strings_to_translate" name="duplicate_strings_to_translate[post][content]" value="1" <?php checked( isset( $duplicate_strings['post']['content'] ) ); ?> /><?php _e( 'Post content', 'wpml-compatibility-test-tools' ); ?></li>
					<li><input type="checkbox" id="duplicate_strings_to_translate" name="duplicate_strings_to_translate[post][excerpt]" value="1" <?php checked( isset( $duplicate_strings['post']['excerpt'] ) ); ?> /><?php _e( 'Post excerpt', 'wpml-compatibility-test-tools' ); ?></li>
					<li><input type="checkbox" id="duplicate_strings_to_translate" name="duplicate_strings_to_translate[custom_field][value]" value="1" <?php checked( isset( $duplicate_strings['custom_field']['value'] ) ); ?> /><?php _e( 'Custom fields', 'wpml-compatibility-test-tools' ); ?></li>
					<li><input type="checkbox" id="duplicate_strings_to_translate" name="duplicate_strings_to_translate[taxonomy][all]" value="1" <?php checked( isset( $duplicate_strings['taxonomy']['all'] ) ); ?> /><?php _e( 'Term name', 'wpml-compatibility-test-tools' ); ?></li>
					<li><input type="checkbox" id="duplicate_strings_to_translate" name="duplicate_strings_to_translate[taxonomy_slug][all]" value="1" <?php checked( isset( $duplicate_strings['taxonomy_slug']['all'] ) ); ?> /><?php _e( 'Term slug', 'wpml-compatibility-test-tools' ); ?></li>
					<a id="duplicate_strings_to_translate" class="toggle" href="#">Toggle all</a>
				</ul>

				<label><?php _e( 'Template:', 'wpml-compatibility-test-tools' ); ?></label>
				<div class="holder">
					<div class="template-left">
						<input class="full-width" type="text" id="duplicate_strings_template" name="duplicate_strings_template" value="<?php echo WPML_Compatibility_Test_Tools::get_option( 'duplicate_strings_template' ); ?>" />
					</div>
					<div class="template-right">
						<select class="full-width" id="duplicate_strings_predefined_templates" name="duplicate_strings_predefined_templates">
							<option value="0"> -= Select predefined template =- </option>
							<option value="1">[%language_code%] %original_string%</option>
							<option value="2">%original_string% [%language_code%]</option>
							<option value="3">[%language_native_name%] %original_string%</option>
							<option value="4">%original_string% [%language_native_name%]</option>
							<option value="5">[%language_name%] %original_string%</option>
							<option value="6">%original_string% [%language_name%]</option>
						</select>
					</div>
					<small>You can use following, special tags: %original_string%, %language_name%, %language_code%, %language_native_name%</small>
				</div>

				<input type="submit" name="save_duplicate_strings_to_translate" value="<?php _e( 'Save', 'wpml-compatibility-test-tools' ); ?>" class="button-secondary button" />
			</form>
		</td>
	</tr>
	</tbody>
</table>