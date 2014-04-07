<table class="widefat general_options_table" style="margin-top: 20px;">
	<thead>
	<tr>
		<th><?php _e('Add language information to post duplicate', 'wpml-compatibility-test-tools') ?></th>
	</tr>
	</thead>
	<tbody>

	<tr>
		<td>
			<p>

			<?php _e('Select strings to which you want to add language information. Language information will be applied during duplicate creation.', 'wpml-compatibility-test-tools'); ?><br /><br />

			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

				<?php $duplicate_strings = WPML_Compatibility_Test_Tools::get_option( 'duplicate_strings', array() );?>

				<input type="checkbox" name="duplicate_strings_to_translate[post][title]" value="1" <?php checked(isset($duplicate_strings['post']['title']))?>  /> <?php _e('Posts title', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[post][content]" value="1" <?php checked(isset($duplicate_strings['post']['content']))?> /> <?php _e('Posts content', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[custom_fields][value]" value="1" <?php checked(isset($duplicate_strings['custom_fields']['value']))?> /> <?php _e('Custom fields', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[taxonomy][all]" value="1" <?php checked(isset($duplicate_strings['taxonomy']['all']))?> /> <?php _e('Terms name', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[taxonomy_slug][all]" value="1" <?php checked(isset($duplicate_strings['taxonomy_slug']['all']))?> /> <?php _e('Terms slug', 'wpml-compatibility-test-tools' );?> <br/>

				<br /><br />

				<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Template:', 'wpml-compatibility-test-tools'); ?></label>
				<input style="width:400px" type="text" id="duplicate_strings_template" name="duplicate_strings_template" value="<?php echo WPML_Compatibility_Test_Tools::get_option( 'duplicate_strings_template'); ?>" />
				<select id="duplicate_strings_predefined_templates" name="duplicate_strings_predefined_templates">
					<option value="0"> -= Select predefined template =- </option>
					<option value="1">[%language_code%] %original_string%</option>
					<option value="2">%original_string% [%language_code%]</option>
					<option value="3">[%language_native_name%] %original_string%</option>
					<option value="4">%original_string% [%language_native_name%]</option>
					<option value="5">[%language_name%] %original_string%</option>
					<option value="6">%original_string% [%language_name%]</option>
				</select>
				<br/>
				<small style="margin-left: 235px; float: left;">You can use following, special tags: %original_string%, %language_name%, %language_code%, %language_native_name% </small>
				<br /><br />

				<input type="submit" name="save_duplicate_strings_to_translate" value="<?php _e('Save', 'wpml-compatibility-test-tools'); ?>" class="button-secondary" />
			</form>
			</p>
		</td>
	</tr>
	</tbody>
</table>