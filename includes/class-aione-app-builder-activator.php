<?php

/**
 * Fired during plugin activation
 *
 * @link       www.oxosolutions.com
 * @since      1.0.0
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/includes
 * @author     OXO Solutions <contact@oxosolutions.com>
 */
class Aione_App_Builder_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		$version = get_option('AIONE_VERSION');
	    if ( empty($version) ) {
	        $version = 0;
	        add_option('AIONE_VERSION', 0, null, 'no');
	    }
	    if ( version_compare($version, AIONE_VERSION) < 0 ) {
	        update_option('AIONE_VERSION', AIONE_VERSION);
	    }
	}

}
