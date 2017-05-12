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
			<h2>Shortcode List</h2>
			<div class="shortcode-list">
				<table>
					<th>Name</th>
					<th>Syntax</th>
					<tbody>
					<tr>
						<td>Login Link</td>
						<td>[login-link]
						<br>
						<strong>Arguments</strong>
<pre>
'class'          => '', //CSS class for login link
'text'           => 'Login' //Default Text to be shown 
</pre>
						</td>
					</tr>
					<tr>
						<td>Register Link</td>
						<td>[register-link]
						<br>
					<strong>Arguments</strong>
<pre>
'class'          => '', //CSS class for Register link
'text'           => 'Sign up' //Default Text to be shown 
</pre>	
						</td>
					</tr>
					<tr>
						<td>logout Link</td>
						<td>[logout-link]
						<br>
						<strong>Arguments</strong>
<pre>
'class'          => '', //CSS class for Register link
'text'           => 'Logout' //Default Text to be shown 
</pre>	

						</td>
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
						<td>[access][/access]
						<br>
                          <strong>Arguments</strong>
<pre>
'capability'     => '', //Defines the capabilities of user
'role'           => '', //Defines the role to be given to user
</pre>	

						</td>
					</tr>
					<tr>
						<td>Login Form</td>
						<td>
						[login]
						<br>
						<strong>Arguments</strong>
<pre>
'echo'           => false, // true/false
'redirect'       => Page set in set pages,  //Link to redirect after login
'form_id'        => 'loginform', //CSS Id for login form
'label_username' => __( 'Username' ),//Label for username input
'label_password' => __( 'Password' ), //Label for password input
'label_remember' => __( 'Remember Me' ), //Label for remember me input
'label_log_in'   => __( 'Login' ),   //Label for Login Button
'id_username'    => 'user_login',  //CSS Id for username input
'id_password'    => 'user_pass',  //CSS Id for password input
'id_remember'    => 'rememberme',  //CSS Id for Remember me input
'id_submit'      => 'wp-submit',  //CSS Id for login Button
'social_login'   => 'no', //Whether social login or not
</pre>


						</td>
					</tr>
					<tr>
						<td>Account</td>
						<td>
						[account]
						<br>
						<strong>Arguments</strong>
<pre>
'pages'          => 'yes', // yes/no
'sub_pages'      => 'no', // yes/no
'layout_menu'    => 'vertical',// horizontal/vertical
'layout_content' => 'box',// box/table/list
</pre>
						</td>
					</tr>
					<tr>
						<td>Account Menu</td>
						<td>
						[account-menu]
						<br>
						<strong>Arguments</strong>
<pre>
'pages'          => 'yes', // yes/no
'sub_pages'      => 'no', // yes/no
'layout'         =>'vertical',// horizontal/vertical
</pre>
						</td>
					</tr>
										<tr>
						<td>Account Content</td>
						<td>
						[account-content]
						<br>
						<strong>Arguments</strong>
<pre>
'pages'          => 'yes', // yes/no
'sub_pages'      => 'no', // yes/no
'layout'         => 'box',// box/table/list
</pre>
						</td>
					</tr>


					<tr>
						<td>Home URL</td>
						<td>[home_url]
						<br>
<strong>Arguments</strong>
<pre>
'type'           => '',
'id'             => '0',
'path'           => '',
'title'          => '',
'action'         => '',
'class'          => '',
</pre>
						</td>
					</tr>
					<tr>
						<td>URL</td>
						<td>[url]
						<br>
                         <strong>Arguments</strong>
<pre>
'id'             => '',
</pre>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>[reset-password]</td>
					</tr>
					<tr>
						<td></td>
						<td>[list-posts]
						<br>
					<strong>Arguments</strong>
<pre>
'cat'            => '',
'cat_id'         => '',
'author'         => '',
'author_id'      => '',
'count'          => '',
'id'             => '',
'class'          => ''	
</pre>	

						</td>
					</tr>
					<tr>
						<td></td>
						<td>[list-comments]
						<br>
                    <strong>Arguments</strong>
<pre>
'count'          => '',
'id'             => '',
'class'          => ''						
</pre>	
						</td>
					</tr>
					<tr>
						<td></td>
						<td>[faq]
						<br>
 <strong>Arguments</strong>
<pre>
'category'       => '',
'id'             => '',
'class'          => '',					
</pre>	

						</td>
					</tr>
					<tr>
						<td></td>
						<td>[change-password]</td>
					</tr>
					<tr>
						<td></td>
						<td>
						[register]
						<br>
						<strong>Arguments</strong>
<pre>
'echo'           => false, // true/false
'captcha'           => true, // true/false
'form_id'        => 'aione_registration_form',  //CSS Id for Registeration form
'show_firstname' => 'yes', //Label for Username Input
'show_lastname'  => 'yes',
'label_firstname'=> __( 'First Name' ), //Label for First name
'label_lastname' => __( 'Last Name' ), //Label for Last name
'label_username' => __( 'Username' ), //Label for Username 
'label_email'    => __( 'Email Address' ), //Label for Email
'label_password' => __( 'Password' ), //Label for password
'label_password_again' => __( 'Password Again' ), //Label for Re Enter Password
'label_submit'   => __( 'Register' ), // Text on Submit Button
'placeholder_firstname' => __( 'Enter Your First Name' ), //Placeholder for Firstname
'placeholder_laststname' => __( 'Enter Your Last Name' ),  //Placeholder for Lastname
'placeholder_username' => __( 'Enter Your Username' ),  //Placeholder for Username
'placeholder_email' => __( 'Enter Your Email Address' )  //Placeholder for Email
</pre>

						</td>
					</tr>
					<tr>
						<td>List or Users</td>
						<td>
						[users]
						<br>
						<strong>Arguments</strong>
<pre>
'site'           =>  1, // ID of site in case of multisite
'style'          => 'table',
'columns'        => 'Username', //Headers for the columns
'fields'         => 'user_login', //keys of fields and custom fields to be dispayed
'role'           => '', //Get users of a perticular role
'roles'          => '', //Get users of a roles saperated by comma
</pre>

						</td>
					</tr>
					<tr>
						<td>Details about Single User</td>
						<td>
						[user]
						<br>
						<strong>Arguments</strong>
<pre>
'user_id'        => CURRENT_LOGGED_IN_USER, // ID of user
'field'          => 'user_login', //key of field and custom field to be dispayed
'field_type'     => 'field', //field/meta
</pre>

						</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-post-title]
						<br>
			<strong>Arguments</strong>
<pre>
'link'           => true
</pre>
						</td>
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
						<td>[aione-post-date format='D M j']
						<br>
<strong>Arguments</strong>
<pre>
'format'         => 'D M j',
</pre>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-custom-fields]
						<br>
<strong>Arguments</strong>
<pre>
'display'        => 'all',
'label'          => true,
'value'          => true,
'seprator'       => ":",
</pre>

						</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-compare-button]
						<br>
<strong>Arguments</strong>
<pre>
'comparetext'    => 'Add To Compare',
'removetext'     => 'Remove From Compare List',
</pre>

						</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-search-filter]
						<br>
<strong>Arguments</strong>
<pre>
'customfields'   => '',
'category'       => true,
</pre>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-create-post]
						<br>
<strong>Arguments</strong>
<pre>
'post_type'      => '',
'groups'         => '',

</pre>
						</td>
					</tr>
					<tr>
						<td></td>
						<td>[aione-embed]
						<br>
<strong>Arguments</strong>
<pre>
'link'           => '',
</pre>
						</td>
					</tr>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}	


}
