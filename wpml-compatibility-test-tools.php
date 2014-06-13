<?php
/*
Plugin Name: WPML Compatibility Test Tools
Plugin URI: http://wpml.org
Description: Tools to test theme and plugin compatibility.
Author: WPML Development Team
Author URI: http://wpml.org
Version: 1.0.1
*/

define('WPML_CTT_VERSION', '1.0.1');
define('WPML_CTT_PATH', dirname(__FILE__));
define('WPML_CTT_FOLDER', basename(WPML_CTT_PATH));
define('WPML_CTT_MENU_SETTINGS_SLUG', WPML_CTT_FOLDER . '/menus/settings/settings.php');
define('WPML_CTT_PLUGIN_URL', plugins_url( basename(dirname(__FILE__)) ) );

require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools-messages.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.functions.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools-base.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.class.php';
require WPML_CTT_PATH . '/inc/wpml-modify-duplicate-strings.class.php';

//disable informations about ICanLocalize
if ( !defined( 'ICL_DONT_PROMOTE' ) ){
	define('ICL_DONT_PROMOTE', true );
}

$WPML_Compatibility_Test_Tools = new WPML_Compatibility_Test_Tools();
