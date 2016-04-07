<?php
/*
Plugin Name: Multilingual Tools
Plugin URI: https://wpml.org/download/multilingual-tools/
Description: Set of tools to test themes and plugins multilingual compatibility.
Author: WPML Compatibility Team
Author URI: http://wpml.org
Version: 1.2
*/

define( 'WPML_CTT_VERSION'	 , '1.2' );
define( 'WPML_CTT_PATH'		 , dirname( __FILE__ ) );
define( 'WPML_CTT_ABS_PATH'	 , plugin_dir_path( __FILE__ ) );
define( 'WPML_CTT_FOLDER'	 , basename( WPML_CTT_PATH ) );
define( 'WPML_CTT_PLUGIN_URL', plugins_url( basename( dirname( __FILE__ ) ) ) );

require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools-messages.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.functions.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools-base.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.class.php';
require WPML_CTT_PATH . '/inc/wpml-modify-duplicate-strings.class.php';

// Disable informations about ICanLocalize.
if ( !defined( 'ICL_DONT_PROMOTE' ) ) {
	define( 'ICL_DONT_PROMOTE', true );
}

$WPML_Compatibility_Test_Tools = new WPML_Compatibility_Test_Tools();