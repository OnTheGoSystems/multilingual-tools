<?php
/*
Plugin Name: Multilingual Tools
Plugin URI: https://wpml.org/download/multilingual-tools/
Description: Set of tools to test themes and plugins multilingual compatibility.
Author: OnTheGoSystems
Author URI: https://www.onthegosystems.com/
Version: 2.2.3
*/

define('WPML_CTT_VERSION', '2.2.3');
define('WPML_CTT_PATH', dirname(__FILE__));
define('WPML_CTT_ABS_PATH', plugin_dir_path(__FILE__));
define('WPML_CTT_FOLDER', basename(WPML_CTT_PATH));
define('WPML_CTT_PLUGIN_URL', plugins_url(basename(dirname(__FILE__))));

require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools-messages.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.functions.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools-base.class.php';
require WPML_CTT_PATH . '/inc/wpml-compatibility-test-tools.class.php';
require WPML_CTT_PATH . '/inc/wpml-modify-duplicate-strings.class.php';

require_once WPML_CTT_PATH . '/inc/class-mltools-shortcode-attribute-filter.php';
require_once WPML_CTT_PATH . '/inc/class-mltools-shortcode-config.php';
require_once WPML_CTT_PATH . '/inc/class-mltools-shortcode-wpml-config-parser.php';
require_once WPML_CTT_PATH . '/inc/class-mltools-xml-helper.php';

// Disable informations about ICanLocalize.
if (!defined('ICL_DONT_PROMOTE')) {
	define('ICL_DONT_PROMOTE', true);
}

$WPML_Compatibility_Test_Tools = new WPML_Compatibility_Test_Tools();

add_action('mltools_loaded', 'mltools_shortcode_helper_add_hooks');
