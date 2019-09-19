<table id="wctt-settings" class="widefat general_options_table">
    <thead>
    <tr>
        <th><h3><?php _e( 'Auto generate strings translations', 'wpml-compatibility-test-tools' ) ?></h3></th>
    </tr>
    </thead>
    <tbody>
    <tr>
        <td>
            <p><?php printf( __( 'Before you start make sure that your theme/plugin is already <a href="%s">scanned for strings</a>.', 'wpml-compatibility-test-tools' ), admin_url( 'admin.php?page=sitepress-multilingual-cms/menu/theme-localization.php' ) ); ?></p>
        </td>
    </tr>
    <tr>
        <td>
            <form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>">
				<?php
				$stt_context                   = wpml_ctt_st_contexts();
				$string_auto_translate_context = (array) WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_context' );

				if ( ! empty( $stt_context ) ) : ?>
                    <label><?php _e( 'Select strings within context to translate:', 'wpml-compatibility-test-tools' ); ?></label>
                    <dl id="dropdown" class="holder">
                        <dt>
                            <span class="placeholder"><?php _e( '- Select -', 'wpml-compatibility-test-tools' ); ?></span>
                        </dt>
                        <dd>
                            <ul>
								<?php foreach ( $stt_context as $v ) : ?>
                                    <li>
                                        <input type="checkbox" id="strings_auto_translate_context"
                                               class="strings_auto_translate_context"
                                               name="strings_auto_translate_context[]"
                                               value="<?php echo htmlspecialchars( $v->context ); ?>"
											<?php echo ! empty( $string_auto_translate_context ) ? checked( is_array( $string_auto_translate_context ), in_array( htmlspecialchars( $v->context ), $string_auto_translate_context ), false ) : 'checked'; ?>>
										<?php echo $v->context . ' (' . $v->c . ')'; ?>
                                    </li>
								<?php endforeach; ?>
                            </ul>
                            <a id="strings_auto_translate_context" class="toggle" href="#">Toggle all</a>
                        </dd>
                    </dl>
				<?php endif; ?>

                <label><?php _e( 'Translate languages:', 'wpml-compatibility-test-tools' ); ?></label>
                <div class="holder">
					<?php echo wpml_ctt_active_languages_output( WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_languages', array() ) ); ?>
                </div>

                <label><?php _e( 'Template:', 'wpml-compatibility-test-tools' ); ?></label>
                <div class="holder">
                    <div class="template-left">
                        <input class="full-width" id="strings_auto_translate_template"
                               name="strings_auto_translate_template"
                               value="<?php echo WPML_Compatibility_Test_Tools::get_option( 'string_auto_translate_template' ); ?>"/>
                    </div>
                    <div class="template-right">
                        <select class="full-width" id="string_auto_translate_predefined_templates"
                                name="string_auto_translate_predefined_templates">
                            <option value="0"> -= Select predefined template =-</option>
                            <option value="1">[%language_code%] %original_string%</option>
                            <option value="2">%original_string% [%language_code%]</option>
                            <option value="3">[%language_native_name%] %original_string%</option>
                            <option value="4">%original_string% [%language_native_name%]</option>
                            <option value="5">[%language_name%] %original_string%</option>
                            <option value="6">%original_string% [%language_name%]</option>
                        </select>
                    </div>
                    <small>You can use following, special tags: %original_string%, %language_name%, %language_code%,
                        %language_native_name%</small>
                </div>

                <input id="strings_auto_translate_action_translate" name="strings_auto_translate_action_translate"
                       value="<?php _e( 'Generate strings translations', 'wpml-compatibility-test-tools' ); ?>"
                       class="button-primary button"/>
                <input type="submit" id="strings_auto_translate_action_save" name="strings_auto_translate_action_save"
                       value="<?php _e( 'Save settings', 'wpml-compatibility-test-tools' ); ?>"
                       class="button-secondary button"/>
                <span class="status"><span class="progress"></span><span
                            class="spinner"></span></span><?php wp_nonce_field( "mt_generate_strings_translations", "_mt_mighty_nonce" ); ?>
            </form>
        </td>
    </tr>
    </tbody>
</table>
