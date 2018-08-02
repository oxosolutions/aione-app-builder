<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       www.oxosolutions.com
 * @since      1.0.0
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/admin/partials
 */
?>

<div class="aione">
	<h4>Aione Shortcodes</h4>
	<div class="aione-table">
		<table class="compact">
			<thead>
				<th>Name</th>
				<th>Shortcode</th>
				<th>Arguments</th>
			</thead>
			<tbody>
				<tr>
					<td>Login Link</td>
					<td>[login-link]</td>
<td><pre>
'class' => '', //CSS class for login link
'text'  => 'Login' //Default Text to be shown 
</pre></td>
				</tr>
				<tr>
					<td>Register Link</td>
					<td>[register-link]</td>
<td><pre>
'class' => '', //CSS class for Register link
'text'  => 'Sign up' //Default Text to be shown 
</pre></td>
				</tr>
				<tr>
					<td>logout Link</td>
					<td>[logout-link]</td>
<td><pre>
'class'  => '', //CSS class for Register link
'text'   => 'Logout' //Default Text to be shown 
</pre></td>
				</tr>
				<tr>
					<td>Is user logged in</td>
					<td>[is_user_logged_in][/is_user_logged_in]</td>
					<td></td>
				</tr>
				<tr>
					<td>User not logged in</td>
					<td>[user_not_logged_in][/user_not_logged_in]</td>
					<td></td>
				</tr>
				<tr>
					<td>User not logged in Error</td>
					<td>[user_not_logged_in_error][/user_not_logged_in_error]</td>
					<td></td>
				</tr>
				<tr>
					<td>Access</td>
					<td>[access][/access]</td>
<td><pre>
'capability'     => '', //Defines the capabilities of user
'role'           => '', //Defines the role to be given to user
</pre></td>
				</tr>
				<tr>
					<td>Login Form</td>
					<td>[login]</td>
<td><pre>
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
</pre></td>
				</tr>
				<tr>
					<td>Account</td>
					<td>[account]</td>
<td><pre>
'pages'          => 'yes', // yes/no
'sub_pages'      => 'no', // yes/no
'layout_menu'    => 'vertical',// horizontal/vertical
'layout_content' => 'box',// box/table/list
</pre></td>
				</tr>
				<tr>
					<td>Account Menu</td>
					<td>[account-menu]</td>
<td><pre>
'pages'          => 'yes', // yes/no
'sub_pages'      => 'no', // yes/no
'layout'         =>'vertical',// horizontal/vertical
</pre></td>
				</tr>
				<tr>
					<td>Account Content</td>
					<td>[account-content]</td>
<td><pre>
'pages'          => 'yes', // yes/no
'sub_pages'      => 'no', // yes/no
'layout'         => 'box',// box/table/list
</pre></td>
				</tr>
				<tr>
					<td>Home URL</td>
					<td>[home_url]</td>
<td><pre>
'type'           => '',
'id'             => '0',
'path'           => '',
'title'          => '',
'action'         => '',
'class'          => '',
</pre></td>
				</tr>
				<tr>
					<td>URL</td>
					<td>[url]</td>
<td><pre>
'id'             => '',
</pre></td>
				</tr>
				<tr>
					<td>Reset Password</td>
					<td>[reset-password]</td>
					<td></td>
				</tr>
				<tr>
					<td>List Posts</td>
					<td>[list-posts]</td>
<td><pre>
'cat'            => '',
'cat_id'         => '',
'author'         => '',
'author_id'      => '',
'count'          => '',
'id'             => '',
'class'          => ''	
</pre></td>
				</tr>
				<tr>
					<td>List Comments</td>
					<td>[list-comments]</td>
<td><pre>
'count'          => '',
'id'             => '',
'class'          => ''						
</pre></td>
				</tr>
				<tr>
					<td>Frequently Asked Questions</td>
					<td>[faq]</td>
<td><pre>
'category'       => '',
'id'             => '',
'class'          => '',					
</pre></td>
				</tr>
				<tr>
					<td>Change Password</td>
					<td>[change-password]</td>
					<td></td>
				</tr>
				<tr>
					<td>Register Form</td>
					<td>[register]</td>
<td><pre>
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
'role' => 'subscriber'  //Role for user
</pre></td>
				</tr>
				<tr>
					<td>List or Users</td>
					<td>[users]</td>
<td><pre>
'site'           =>  1, // ID of site in case of multisite
'style'          => 'table',
'columns'        => 'Username', //Headers for the columns
'fields'         => 'user_login', //keys of fields and custom fields to be dispayed
'role'           => '', //Get users of a perticular role
'roles'          => '', //Get users of a roles saperated by comma
</pre></td>
				</tr>
				<tr>
					<td>Details about Single User</td>
					<td>[user]</td>
<td><pre>
'user_id'        => CURRENT_LOGGED_IN_USER, // ID of user
'field'          => 'user_login', //key of field and custom field to be dispayed
'field_type'     => 'field', //field/meta
</pre></td>
				</tr>
				<tr>
					<td>Welcome User</td>
					<td>[welcome]</td>
<td><pre>
'user_id'        => CURRENT_LOGGED_IN_USER, // ID of user
'class'          => 'user_login', //CSS Class
'id'     => 'field', //CSS Id
</pre></td>
				</tr>
			</tbody>
		</table>
	</div>
</div>