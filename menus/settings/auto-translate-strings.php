<table class="widefat general_options_table" style="margin-top: 20px;">
	<thead>
	<tr>
		<th><?php _e('Auto generate strings translations', 'wpml-compatibility-test-tools') ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td>
			<p>
				<?php printf( __('Before you start make sure that your theme/plugin is already <a href="%s">scanned for strings</a>.', 'wpml-compatibility-test-tools'), admin_url( 'admin.php?page=sitepress-multilingual-cms/menu/theme-localization.php' ) )  ; ?>
			</p>
		</td>
	</tr>

	<tr>
		<td>

			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<?php
				$stt_context = wpml_ctt_st_contexts();

				if( !empty( $stt_context ) ){ ?>
					<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Select strings within context to translate:', 'wpml-compatibility-test-tools'); ?></label>
					<select name="strings_auto_translate_context">
						<option value=""><?php _e('Select context', 'wpml-compatibility-test-tools'); ?></option>
						<option <?php selected('all_contexts', WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_context')) ?> value="all_contexts" ><?php _e('All contexts', 'wpml-compatibility-test-tools'); ?></option>

						<?php foreach( $stt_context as $v ){ ?>
							<option <?php selected(htmlspecialchars($v->context), WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_context')) ?> value="<?php echo htmlspecialchars( $v->context ); ?>"><?php echo $v->context . ' ('. $v->c .')'; ?></option>
						<?php } ?>
					</select>
				<?php } ?>
				<br /><br />

				<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Translate languages:', 'wpml-compatibility-test-tools'); ?></label>
				<?php echo wpml_ctt_active_languages_output( WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_languages', array()) ); ?>
				<a style="margin-left: 20px;" id="translate_strings_active_languages_toggle_all" href="#">Toggle all</a>
				<br /><br />

				<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Template:', 'wpml-compatibility-test-tools'); ?></label>
				<input style="width:400px" type="text" id="strings_auto_translate_template" name="strings_auto_translate_template" value="<?php echo WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_template'); ?>" />
				<select id="string_auto_translate_predefined_templates" name="string_auto_translate_predefined_templates">
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

				<input type="submit" id="strings_auto_translate_action_translate" name="strings_auto_translate_action_translate" value="<?php _e('Generate strings translations', 'wpml-compatibility-test-tools'); ?>" class="button-primary" />
				<input type="submit" id="strings_auto_translate_action_save" name="strings_auto_translate_action_save" value="<?php _e('Save settings', 'wpml-compatibility-test-tools'); ?>" class="button-secondary" />
			</form>

		</td>
	</tr>
	</tbody>
</table>