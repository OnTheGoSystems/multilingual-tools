<div class="wrap">
    <div id="icon-wpml" class="icon32"><br /></div>
    <h2><?php _e('WPML Compatibility Test Tools', 'wpml-compatibility-test-tools'); ?></h2>
	
	
	<?php

	//include strings auto translate box only when String Translations plugin is available
	if ( defined( 'WPML_ST_VERSION' )  ) {
		include_once( WPML_CTT_PATH . '/menus/settings/auto-translate-strings.php' );
	}

	//include duplicate auto translate box
	include_once( WPML_CTT_PATH . '/menus/settings/auto-translate-duplicate.php' );
	?>
	

</div>