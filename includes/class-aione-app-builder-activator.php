<?php

/**
 * Fired during plugin activation
 *
 * @link       www.sgssandhu.com
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
 * @author     SGS Sandhu <contact@oxosolutions.com>
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
		if ( is_network_admin() ) {
			return;
		}

		// Add the transient to redirect.
		set_transient( 'aab_activation_redirect', true, 30 );

		/*require_once AIONE_DIR_PATH. 'admin/class-aione-app-builder-admin-aione-custom-post-type.php';
		$options = Aione_App_Builder_Admin_Aione_Custom_Post_Type::aione_get_builtin_in_post_types();
		update_option( 'aione_custom_post_types', $options );*/
	}

}
