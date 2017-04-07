<?php
/**
 * Plugin Name:       Aione Templates
 * Plugin URI:        https://oxosolutions.com/products/wordpress-plugins/aione-templates
 * Description:       Aione Page Templates
 * Version:           1.0.1.2
 * Author:            SGS Sandhu
 * Author URI:        http://sgssandhu.com
 * Text Domain:       aione_templates
 * License:           GPL-2.0+
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/oxosolutions/aione-templates
 * GitHub Branch: master
 */


// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
} // end if

require_once( plugin_dir_path( __FILE__ ) . 'class-aione-templates.php' );
add_action( 'plugins_loaded', array( 'Page_Template_Plugin', 'get_instance' ) );

 
 