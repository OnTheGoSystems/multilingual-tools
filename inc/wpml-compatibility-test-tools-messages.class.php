<?php

class WPML_Compatibility_Test_Tools_Messages {

	public function __call($name, $arguments)
	{
		switch($name){
			case 'no_wpml_notice' :
				$message = __( 'WPML Compatibility Test Tools plugin is enabled but not effective. It requires %s plugin in order to work.', 'wpml-compatibility-test-tools' );
				echo '<div class="message error"><p>'.sprintf($message, '<a href="http://wpml.org/">WPML</a>').'</p></div>';
				break;

			case 'not_finished_wpml_setup' :
				$message = __( 'WPML Compatibility Test Tools is enabled but not effective. You have to finish WPML setup.', 'wpml-compatibility-test-tools' );
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

			case 'wctt_in_action_notice' :
				//get current settings for string duplication
				$duplicate_strings = WPML_Compatibility_Test_Tools::get_option('duplicate_strings');
				//prepare a message
				$message =
					__("WPML Compatibility Tester plugin is running and will automatically add language information to all new duplicates for your site. Right now, it will add language information for the following post fields:", 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['post']['title'])?'[&#10004;] ':'[ ] ').__('Post title', 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['post']['content'])?'[&#10004;] ':'[ ] ').__('Post content', 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['post']['image-tags'])?'[&#10004;] ':'[ ] ').__('Image alt and title', 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['post']['excerpt'])?'[&#10004;] ':'[ ] ').__('Post excerpt', 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['custom_fields']['value'])?'[&#10004;] ':'[ ] ').__('Custom fields', 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['taxonomy']['all'])?'[&#10004;] ':'[ ] ').__('Term name ', 'wpml-compatibility-test-tools' )."<br/>".
					(isset($duplicate_strings['taxonomy_slug']['all'])?'[&#10004;] ':'[ ] ').__('Term slug', 'wpml-compatibility-test-tools' )."<br/>".
					sprintf("<a href=\"%s\">".__("Click here to change fields to duplicate", 'wpml-compatibility-test-tools')."</a><br/>", admin_url('admin.php?page='.WPML_CTT_MENU_SETTINGS_SLUG))."<br/>".
					__("To proceed, select all the site's content, scroll down and select <strong>Duplicate all</strong> and then click on <strong>Send documents</strong>.", 'wpml-compatibility-test-tools')."<br/>".
					"<div style=\"color: #ff0000\">".__("Please note that any existing translations for selected posts will be overwritten!", 'wpml-compatibility-test-tools')."</div>";
				echo '<div class="updated message fade"><p>' . $message . '</p></div>';
				break;


		}

	}


}
