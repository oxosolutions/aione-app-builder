<?php

/**
 * @link              www.sgssandhu.com
 * @since             1.0.0
 * @package           Aione_App_Builder
 *
 * @wordpress-plugin
 * Plugin Name:       Aione App Builder
 * Plugin URI:        http://oxosolutions.com/products/wordpress-plugins/aione-app-builder/
 * Description:       Aione App Builder
<<<<<<< HEAD
 * Version:           2.1.0.6
=======
 * Version:           2.1.5.2
>>>>>>> f02c80feb8f4cef417d0af892654c0c0bb249366
 * Author:            OXO Solutions
 * Author URI:        https://github.com/oxosolutions/aione-app-builder
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
 * The code that runs during plugin activation.
 * This action is documented in includes/class-aione-app-builder-activator.php
 */
function activate_aione_app_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aione-app-builder-activator.php';
	Aione_App_Builder_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-aione-app-builder-deactivator.php
 */
function deactivate_aione_app_builder() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-aione-app-builder-deactivator.php';
	Aione_App_Builder_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_aione_app_builder' );
register_deactivation_hook( __FILE__, 'deactivate_aione_app_builder' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-aione-app-builder.php';
require plugin_dir_path( __FILE__ ) . 'includes/class-aione-app-builder-shortcode.php';

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

}
run_aione_app_builder();
function run_aione_app_builder_shortcode() {

	$plugin_shortcode = new Aione_App_Builder_Shortcode();

}
run_aione_app_builder_shortcode();