<table class="widefat general_options_table" style="margin-top: 20px;">
	<thead>
	<tr>
		<th><?php _e('Auto strings translation', 'wpml-compatibility-test-tools') ?></th>
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
						<option value="all_contexts" ><?php _e('All strings', 'wpml-compatibility-test-tools'); ?></option>

						<?php foreach( $stt_context as $v ){ ?>
							<option value="<?php echo htmlspecialchars( $v->context ); ?>"><?php echo $v->context . ' ('. $v->c .')'; ?></option>
						<?php } ?>
					</select>
				<?php } ?>
				<br /><br />

				<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Translate languages:', 'wpml-compatibility-test-tools'); ?></label>
				<?php echo wpml_ctt_active_languages_output( ); ?>
				<br /><br />

				<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Template:', 'wpml-compatibility-test-tools'); ?></label>
				<input type="text" name="strings_auto_translate_prefix" value="<?php echo get_option( 'wpml_ctt_auto_translate_prefix '); ?>" /><br/>
				<small>You can use %language_name to generate translation language name</small>
				<br /><br />

				<input type="submit" name="strings_auto_translate_action_translate" value="<?php _e('Add translations', 'wpml-compatibility-test-tools'); ?>" class="button-secondary" />
			</form>

		</td>
	</tr>
	</tbody>
</table>