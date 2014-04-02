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
				<?php _e('Before you start make sure that your theme/plugin is already scanned for strings.', 'wpml-compatibility-test-tools'); ?><br /><br />

				<a href="<?php echo admin_url( 'admin.php?page=sitepress-multilingual-cms/menu/theme-localization.php' ); ?>" class="button-secondary"><?php _e('Scan the theme or plugin for strings', 'wpml-compatibility-test-tools'); ?></a>

			</p>
		</td>
	</tr>

	<tr>
		<td>
			<p>
			<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<?php
				$stt_context = wpml_ctt_st_contexts();

				if( !empty( $stt_context ) ){ ?>
					<label style="width: 235px; margin-top: 3px; float: left;"><?php _e('Select strings within context to translate:', 'wpml-compatibility-test-tools'); ?></label>
					<select name="strings_auto_translate_context">
						<option value="all_contexts" selected="selected"><?php _e('All strings', 'wpml-compatibility-test-tools'); ?></option>

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
				<input type="text" name="strings_auto_translate_prefix" value="<?php echo get_option( 'wpml_ctt_auto_translate_prefix '); ?>" />
				<br /><br />

				<input type="submit" name="strings_auto_translate_action_translate" value="<?php _e('Add translations', 'wpml-compatibility-test-tools'); ?>" class="button-secondary" />
			</form>
			</p>
		</td>
	</tr>
	</tbody>
</table>