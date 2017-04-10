<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.oxosolutions.com
 * @since      1.0.0
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/admin
 * @author     AmritDeep <amritdeepkaur@gmail.com>
 */
class Aione_App_Builder_Admin_Shortcodes {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
	}

	function aione_app_builder_shortcodes_list() {
		?>
		<style>
		.shortcode-list table {
			width:98%;
			background:#fff;
			border:1px solid #d2d2d2;
			border-collapse: collapse;
		}
		.shortcode-list th,.shortcode-list tr, .shortcode-list td {
			border:1px solid #d2d2d2;
			padding:1%;
			
		}
		</style>
		<div class="wrap">
			<h2>ShortCode List</h2>
			<div class="shortcode-list">
				<table>
					<th>Name</th>
					<th>Syntax</th>
					<tbody>
					<tr>
						<td>Login Link</td>
						<td>[login-link class="" text=""]</td>
					</tr>
					<tr>
						<td>Register Link</td>
						<td>[register-link class="" text=""]</td>
					</tr>
					<tr>
						<td>logout Link</td>
						<td>[logout-link class="" text=""]</td>
					</tr>
					<tr>
						<td>Is user logged in</td>
						<td>[is_user_logged_in][/is_user_logged_in]</td>
					</tr>
					<tr>
						<td>User not logged in</td>
						<td>[user_not_logged_in][/user_not_logged_in]</td>
					</tr>
					<tr>
						<td>User not logged in Error</td>
						<td>[user_not_logged_in_error][/user_not_logged_in_error]</td>
					</tr>
					<tr>
						<td>Access</td>
						<td>[access capability="" role=""][/access]</td>
					</tr>
					<tr>
						<td>Login Form</td>
						<td>[aione-app-builder-login-form]</td>
					</tr>
					<tr>
						<td>Home URL</td>
						<td>[home_url]</td>
					</tr>
					<tr>
						<td>URL</td>
						<td>[url id=""]</td>
					</tr>
					<tr>
						<td></td>
						<td>[forget-password]</td>
					</tr>
					<tr>
						<td></td>
						<td>[list-posts]</td>
					</tr>
					<tr>
						<td></td>
						<td>[list-comments]</td>
					</tr>
					<tr>
						<td></td>
						<td>[faq]</td>
					</tr>
					<tr>
						<td></td>
						<td>[change-password]</td>
					</tr>
					<tr>
						<td></td>
						<td>[register]</td>
					</tr>
					<tr>
						<td></td>
						<td>[users]</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-post-title]</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-post-content]</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-post-author]</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-post-date format='D M j']</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-custom-fields display = 'all' label = true value = true seprator = ":"]</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-compare-button removetext="" comparetext=""]</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-search-filter customfields="field_58d36c2b25b70,field_58d4f175ca21a" category="true"]</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}	


}
