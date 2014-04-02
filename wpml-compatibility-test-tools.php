<?php
/*
Plugin Name: WPML Compatibility Test Tools
Plugin URI: http://www.wpml.org
Description: Tools to test theme and plugin compatibility.
Author: ICanLocalize
Author URI: http://wpml.org
Version: 0.1.0
*/

define('WPML_CTT_VERSION', '0.0.1');
define('WPML_CTT_PATH', dirname(__FILE__));

require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.functions.php';
require WPML_CTT_PATH . '/inc/wpml-modify-duplicate-strings.class.php';


$WPML_Compatibility_Test_Tools = new WPML_Compatibility_Test_Tools();
