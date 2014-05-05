<?php

class WPML_Compatibility_Test_Tools_Messages {

	public function __call($name, $arguments)
	{
		switch($name){
			case 'no_wpml_notice' :
				$message = __( 'WPML Compatibility Test Tools requires %s plugin to work. You will need to install the base WPML plugin, Translation Management and String Translation.', 'wpml-compatibility-test-tools' );
				echo '<div class="message error"><p>'.sprintf($message, '<a href="http://wpml.org/">WPML</a>').'</p></div>';
				break;

			case 'no_tm_notice' :
				$message = __( 'WPML Compatibility Test Tools is enabled but not effective. It requires WPML Translation Management plugin in order to work.', 'wpml-compatibility-test-tools' );
				echo '<div class="message error"><p>'.sprintf($message, '<a href="http://wpml.org/">WPML</a>').'</p></div>';
				break;

			case 'no_st_notice' :
				$message = __( 'WPML Compatibility Test Tools is enabled but not effective. It requires WPML String Translation plugin in order to work.', 'wpml-compatibility-test-tools' );
				echo '<div class="message error"><p>'.sprintf($message, '<a href="http://wpml.org/">WPML</a>').'</p></div>';
				break;

			case 'no_selected_language_notice' :
				echo '<div class="message error"><p>' . __( 'At least one language should be selected in order to translate strings.', 'wpml-compatibility-test-tools' ) . '</p></div>';
				break;

			case 'no_selected_language_for_pages_notice' :
				echo '<div class="message error"><p>' . __( 'At least one language should be selected in order to create pages with dummy content.', 'wpml-compatibility-test-tools' ) . '</p></div>';
				break;

			case 'no_context_notice' :
				echo '<div class="message error"><p>' . __( 'Please select the context.', 'wpml-compatibility-test-tools' ) . '</p></div>';
				break;

			case 'no_template_notice' :
				echo '<div class="message error"><p>' . __( 'Template is required.', 'wpml-compatibility-test-tools' ) . '</p></div>';
				break;

			case 'strings_translated_notice' :
				echo '<div class="updated message fade"><p>' . __( 'Strings translated.', 'wpml-compatibility-test-tools' ) . '</p></div>';
				break;

			case 'settings_updated_notice' :
				echo '<div class="updated message fade"><p>' . __( 'Settings updated.', 'wpml-compatibility-test-tools' ) . '</p></div>';
				break;

			case 'duplicate_strings_available' :
				$message = sprintf( __("Your settings have been updated.<br/>Now, continue to the %s screen, select all the site's content, select <strong>Duplicate all</strong> and click on <strong>Send documents</strong>. %s.", 'wpml-compatibility-test-tools'), "<a href=\"".admin_url('admin.php?page='.basename(WPML_TM_PATH). '/menu/main.php')."\">".__('Translation Dashboard','wpml-compatibility-test-tools')."</a>", "<a target=\"_blank\" href=\"#\">Help</a>" );
				echo '<div class="updated message fade"><p>' . $message . '</p></div>';
				break;

		}

	}


}