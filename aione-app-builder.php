<?php

/**
 *
 * @link              www.oxosolutions.com
 * @since             1.0.2
 * @package           Aione_App_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       Aione App Builder
 * Plugin URI:        http://oxosolutions.com/products/wordpress-plugins/aione-app-builder/
 * Description:       Easily create custom post types and taxonomy and connect everything together.
 * Version:           1.6.0.1
 * Author:            OXO Solutions®
 * Author URI:        https://oxosolutions.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       aione-app-builder
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/oxosolutions/aione-app-builder
 * GitHub Branch: master
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
if ( ! defined( 'AIONE_PLUGIN_NAME' ) ) {
	define( 'AIONE_PLUGIN_NAME', 'Aione App Builder' );
}
if ( ! defined( 'AIONE_VERSION' ) ) {
	define( 'AIONE_VERSION', '1.2.0.0' );
}

/*
 * Path Constants
 */
if ( ! defined( 'AIONE_DIR_PATH' ) ) {
	define( 'AIONE_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( ! defined( 'AIONE_DIR_URL' ) ) {
	define( 'AIONE_DIR_URL', plugin_dir_url( __FILE__ ) );
}
if ( ! defined( 'AIONE_FILE' ) ) {
	define( 'AIONE_FILE', plugin_basename( __FILE__ ) );
}
if ( ! defined( 'AIONE_ABSPATH' ) ) {
	define( 'AIONE_ABSPATH', dirname( __FILE__ ) );
}

/**
 * last edit flag
 */
if ( !defined('AIONE_EDIT_LAST' )){
    define( 'AIONE_EDIT_LAST', '_aione_edit_last');
}

/**
 * last author
 */
if ( !defined('AIONE_AUTHOR' )){
    define( 'AIONE_AUTHOR', '_aione_author_id');
}
/*
 *
 * Forced priority
 */
if ( !defined( 'AIONE_INIT_PRIORITY' ) ) {
    // Early start ( some plugins use 'init' with priority 0 ).
    define( 'AIONE_INIT_PRIORITY', -1 );
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aione-app-builder-activator.php
 */
function activate_aione_app_builder() {
	require_once AIONE_DIR_PATH. 'includes/class-aione-app-builder-activator.php';
	Aione_App_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aione-app-builder-deactivator.php
 */
function deactivate_aione_app_builder() {
	require_once AIONE_DIR_PATH . 'includes/class-aione-app-builder-deactivator.php';
	Aione_App_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aione_app_builder' );
register_deactivation_hook( __FILE__, 'deactivate_aione_app_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aione-app-builder.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_aione_app_builder() {

	$plugin = new Aione_App_Builder();
	$plugin->run();
	$GLOBALS['aione'] = new stdClass();

}
run_aione_app_builder();
