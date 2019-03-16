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
'class'  => '', //CSS class for Logout link
'text'   => 'Logout' //Default Text to be shown 
</pre></td>
				</tr>
				<tr>
					<td>Account Link</td>
					<td>[account-link]</td>
<td><pre>
'class'  => '', //CSS class for Account link
'text'   => 'Account' //Default Text to be shown 
</pre></td>
				</tr>
				<tr>
					<td>Dashboard Link</td>
					<td>[dashboard-link]</td>
<td><pre>
'class'  => '', //CSS class for Dashboard link
'text'   => 'Dashboard' //Default Text to be shown 
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
					<td>View Profile</td>
					<td>[profile]</td>
					<td></td>
				</tr>
				<tr>
					<td>Edit Profile</td>
					<td>[edit-profile]</td>
					<td></td>
				</tr>
				<tr>
					<td>Change Password</td>
					<td>[change-password]</td>
					<td></td>
				</tr>
				<tr>
					<td>Home URL</td>
					<td>[home_url]</td>
<td></td>
				</tr>
				<tr>
					<td>URL</td>
					<td>[url]</td>
<td><pre>
'type'           => '',
'id'             => '0',
'path'           => '',
'title'          => '',
'action'         => 'logout',
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
'post_type'      => 'post',
'status'		=> 'publish',
'cat'            => '',
'cat_id'         => '',
'author'         => '',
'author_id'      => '',
'count'          => '',
'order'			=> 'DESC',
'orderby'		=> 'date',
'template'       => '', // slug of aione template
'pagination'     => 'yes', // no
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
</pre></td>
				</tr>
				<tr>
					<td>Blog Archive</td>
					<td>[blog]</td>
<td><pre>
'post_type'           =>  "post,movie", // post types with comma seprated
'post_status'          => "publish", // publish,pending,draft
'order'        => 'DESC', // DESC or ASC
'orderby'         => 'date', //keys of fields and custom fields to be dispayed
'class'           => '', 
'id'          => '', 
'number_posts'          => '10',  // any number or -1 for all
'offset'			     => '', // Number of post to displace or pass over
'cat_slug'			     => 'slug1,slug2', // Category Slug
'exclude_cats'			 => 'slug3,slug4', // 
'show_title'			 => 'yes', //yes/no. Default=yes
'title_link'			 => 'yes', //yes/no. Default=yes
'thumbnail'			 => 'yes', //yes/no. Default=yes
'excerpt'			 => 'yes', //yes/no. Default=yes
'excerpt_length'			 => '55', //Number
'meta_author'			=> 'yes', //yes/no. Default=yes
'meta_categories'			=> 'yes', //yes/no. Default=yes
'meta_date'			=> 'yes', //yes/no. Default=yes
'meta_date_format'			=> 'F j, Y', 
'meta_tags'			=> 'yes', //yes/no. Default=yes
'read_more'			=> 'yes', //yes/no. Default=yes
'read_more_text'			=> 'Read More..', 
'pagination'	=> 'yes',
</pre></td>
				</tr>
				<tr>
					<td>Icon</td>
					<td>[icon]</td>
<td><pre>
'class' => 'fa fa-facebook', //Fontawsome class
</pre></td>
				</tr>
				<tr>
					<td>Date</td>
					<td>[date]</td>
<td><pre>
'format' => 'jS F Y H:i A', //DateTime format 
'time-zone' => 'Asia/Kolkata', // TimeZone 
</pre> <a href="http://php.net/manual/en/timezones.php" target="_blank">Supported TimeZone</a></td>
				</tr>
				<tr>
					<td>Blog Info</td>
					<td>[info]</td>
<td><pre>
'show' => 'name', 
</pre>
<a href="https://developer.wordpress.org/reference/functions/get_bloginfo/" target="_blank">Supported Parameters</a></td>
</td>
				</tr>
				<tr>
					<td>Post ID</td>
					<td>[id]</td>
<td>
</td>
</td>
				</tr>
				<tr>
					<td>Post Link</td>
					<td>[link]</td>
<td>
</td>
</td>
				</tr>
				<tr>
					<td>Post Title</td>
					<td>[title]</td>
<td><pre>
'link' => 'true', //false
'class' => ' ',
'id' => ' '
'style' => 'div' // h1,h2,h3,h4,h5,h6,span
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Post Content</td>
					<td>[content]</td>
<td><pre>
'expert' => 'false', //true
'expert-length' => '20',
'more-text' => 'Read More...'
'class' => ' ',
'id' => ' '
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Post Feature Image</td>
					<td>[featured-image]</td>
<td><pre>
'size' => 'full',
'class' => '',
'id' => ' ',
'placeholder' => 'true', //false
'placeholder_image' => '', //url of the image
'return' => 'image' // image/url/width/height/alt
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Attached Media</td>
					<td>[attached-media]</td>
<td><pre>
'width' => '',
'height' => '',
'caption' => 'true', //false
'class' => '',
'id' => ' ',
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Post Tags</td>
					<td>[tags]</td>
<td><pre>
'texonomy' => 'post_tags', //custom texonomy
'style' => 'list', //list,div
'class' => ' ',
'id' => ' '
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Post Categories</td>
					<td>[categories]</td>
<td><pre>
'texonomy' => 'post_tags', //custom texonomy
'style' => 'list', //list,div
'class' => ' ',
'id' => ' '
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Custom Field</td>
					<td>[custom-field]</td>
<td><pre>
'field' => 'field_5bc5a7afa60b0', //acf field_key
'label' => 'true', //false
'template' => '', //slug of aione template (work only if field type is Post Object)
'seperator' => ' : ',

</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Custom Field</td>
					<td>[custom-fields]</td>
<td><pre>
'field_group' => '82', 
'label' => 'true', //false
'seperator' => ' : ',
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Upcoming Tag</td>
					<td>[upcoming-tag]</td>
<td><pre>
'text' => 'Upcoming',
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Social Icons</td>
					<td>[social-icon]</td>
<td><pre>
'size' => 'small', // medium,large,xlarge
'style' => 'square', // rounded,circle
'direction' => 'horizontal', // vertical
'theme' => 'colored', // dark,dark-solid,dark-outline,light bg-black,light-solid bg-black,light-outline bg-black
'labels' => "false" // true
'facebook' => 'www.facebook.com'
'twitter' => 'www.twitter.com'
'youtube' => 'www.youtube.com'
'googleplus' => ''
'linkedin' => ''
'instagram' => ''
'flickr' => ''
'github' => ''
'pinterest' => ''
'rss' => ''
'tumblr' => ''
'vimeo' => ''
'wordpress' => ''
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Social Share</td>
					<td>[social-share]</td>
<td><pre>
'size' => 'small', // medium,large,xlarge
'style' => 'square', // rounded,circle
'direction' => 'horizontal', // vertical
'theme' => 'colored', // dark,dark-solid,dark-outline,light bg-black,light-solid bg-black,light-outline bg-black
'labels' => "false" // true
'facebook' => "false" // true
'twitter' => "false" // true
'googleplus' => "false" // true
'linkedin' => "false" // true
'pinterest' => "false" // true
'reddit' => "false" // true
'tumblr' => "false" // true
</pre>
</td>
</td>
				</tr>
				<tr>
					<td>Add New Post Form at Frontend</td>
					<td>[add-new]</td>
<td><pre>
'type' => 'post', // page, any custom pot type
'title' => 'true', // false
'content' => 'true', // false
'status' => 'publish', // draft,pending, all wordpres post status
'id' => 'add_new_form', // css id name
</pre>
</td>
</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>