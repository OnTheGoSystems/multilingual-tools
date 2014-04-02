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

			<?php _e('Select strings to which you want to add language information when duplicating a post.', 'wpml-compatibility-test-tools'); ?><br /><br />

			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">

				<?php $wpml_ctt_auto_duplicate = get_option('wpml_ctt_auto_duplicate', array())?>

				<input type="checkbox" name="duplicate_strings_to_translate[]" value="post_title" <?php checked(in_array('post_title', $wpml_ctt_auto_duplicate))?>  /> <?php _e('Posts title', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[]" value="post_content" <?php checked(in_array('post_content', $wpml_ctt_auto_duplicate))?> /> <?php _e('Posts content', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[]" value="custom_fields" <?php checked(in_array('custom_fields', $wpml_ctt_auto_duplicate))?> /> <?php _e('Custom fields', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[]" value="terms_name" <?php checked(in_array('terms_name', $wpml_ctt_auto_duplicate))?> /> <?php _e('Terms name', 'wpml-compatibility-test-tools' );?> <br/>
				<input type="checkbox" name="duplicate_strings_to_translate[]" value="terms_slug" <?php checked(in_array('terms_slug', $wpml_ctt_auto_duplicate))?> /> <?php _e('Terms slug', 'wpml-compatibility-test-tools' );?> <br/>

				<br /><br />

				<input type="submit" name="save_duplicate_strings_to_translate" value="<?php _e('Save', 'wpml-compatibility-test-tools'); ?>" class="button-secondary" />
			</form>
			</p>
		</td>
	</tr>
	</tbody>
</table>