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
	<div class="aione-table font-size-14">
		<table class="compact">
			<thead>
				<th>Name</th>
				<th>Shortcode</th>
				<th>Arguments</th>
			</thead>
			<tbody>
				<tr>
					<td>Login Link</td>
					<td>[login_link]</td>
					<td>
<pre>
'class' => '', //CSS class for login link
'text'  => 'Login' //Default Text to be shown 
</pre>
				</td>
				</tr>
				<tr>
					<td>Register Link</td>
					<td>[register_link]</td>
					<td>
<pre>
'class' => '', //CSS class for Register link
'text'  => 'Sign up' //Default Text to be shown 
</pre>
				</td>
				</tr>
				<tr>
					<td>Reset Password Link</td>
					<td>[reset_password_link]</td>
					<td>
<pre>
'class' => '', //CSS class for Register link
'text'  => 'Reset Password' //Default Text to be shown 
</pre>
				</td>
				</tr>
				<tr>
					<td>logout Link</td>
					<td>[logout_link]</td>
					<td>
<pre>
'class'  => '', //CSS class for Logout link
'text'   => 'Logout' //Default Text to be shown 
</pre>
				</td>
				</tr>
				<tr>
					<td>Account Link</td>
					<td>[account_link]</td>
					<td>
<pre>
'class'  => '', //CSS class for Account link
'text'   => 'Account' //Default Text to be shown 
</pre>
				</td>
				</tr>
				<tr>
					<td>Dashboard Link</td>
					<td>[dashboard_link]</td>
					<td>
<pre>
'class'  => '', //CSS class for Dashboard link
'text'   => 'Dashboard' //Default Text to be shown 
</pre>
				</td>
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
					<td>
<pre>
'capability'     => '', //Defines the capabilities of user
'role'           => '', //Defines the role to be given to user
</pre>
				</td>
				</tr>
				<tr>
					<td>Login Form</td>
					<td>[login]</td>
					<td>
<pre>
'echo'           => 'false', // true/false
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
</pre>
				</td>
				</tr>
				
				<tr>
					<td>Register Form</td>
					<td>[register]</td>
					<td>
<pre>
'echo'           => 'false', // true/false
'captcha'           => 'true', // true/false
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
</pre>
					</td>
				</tr>

				<tr>
					<td>Reset Password</td>
					<td>[reset_password]</td>
					<td></td>
				</tr>
				
				<tr>
					<td>View Profile</td>
					<td>[view_profile]</td>
					<td></td>
				</tr>
				<tr>
					<td>Edit Profile</td>
					<td>[edit_profile]</td>
					<td></td>
				</tr>
				<tr>
					<td>Change Password</td>
					<td>[change_password]</td>
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
					<td>
<pre>
'id'             => '123', id of page/post
'path'           => 'home', //Page/Post path (Not for custom post type)
'title'          => '', //Page/Post title (Not for custom post type)
</pre>
				</td>
				</tr>
				<tr>
					<td>Post Count</td>
					<td>[post_count]</td>
					<td>
<pre>
'post_type'      => 'post',
'status'		=> 'publish',
'cat'            => '', // category slug
'cat_id'         => '', // category ID 
'author'         => '', // user nice_name
'author_id'      => '', // user ID
'order'			=> 'DESC',
'orderby'		=> 'date'
</pre></td>
				</tr>
				<tr>
					<td>List Posts</td>
					<td>[posts]</td>
					<td>
<pre>
'post_type'      => 'post',
'status'		=> 'publish',
'cat'            => '',
'cat_id'         => '',
'author'         => '',
'author_id'      => '',
'meta_key'      => '',
'meta_value'      => '',
'posts_per_page'  => '-1',
'offset'          => '',
'order'			=> 'DESC',
'orderby'		=> 'date',
'template'       => '', // slug of aione template
'pagination'     => 'yes', // no
'id'             => '',
'class'          => ''	
</pre></td>
				</tr>
				<tr>
					<td>Post</td>
					<td>[post]</td>
					<td>
<pre>
'post_id'      => '', Post ID
'template'       => '', // slug of aione template
'id'             => '',
'class'          => ''	
</pre>
					</td>
				</tr>
				<tr>
					<td>Post Meta</td>
					<td>[post_meta]</td>
					<td>
<pre>
'post_id' => '', //Post ID
'field' => 'field_5bc5a7afa60b0', //acf field_key
'show_label' => 'no', //yes
'class'          => '' //CSS class
</pre>
					</td>
				</tr>
				<tr>
					<td>List Comments</td>
					<td>[list_comments]</td>
					<td>
<pre>
'count'          => '',
'id'             => '',
'class'          => ''						
</pre></td>
				</tr>

				
				<tr>
					<td>User Count</td>
					<td>[user_count]</td>
					<td>
<pre>
'site'           =>  1, // ID of site in case of multisite
'role'           => '', //Get users of a perticular role
'roles'          => '', //Get users of a roles saperated by comma
'include'        => '', 
'exclude'        => '', 
</pre>
</td>
					</tr>
					<tr>
					<td>List or Users</td>
					<td>[users]</td>
					<td>
<pre>
'site'           =>  1, // ID of site in case of multisite
'style'          => 'table', only table or keep it blank
'columns'        => 'Username', //Headers for the columns. Seperated with | 
'fields'         => 'user_login', //keys of fields and custom fields to be dispayed.
					Seperated with | 
					user_pass is not allowed 
					For custom fields : {{slug}}
'role'           => '', //Get users of a perticular role
'roles'          => '', //Get users of a roles saperated by comma
'template'       => '', 
'include'       => '', 
'exclude'       => '', 
</pre>
				</td>
				</tr>
					<tr>
						<td>Details about Single User</td>
						<td>[user]</td>
						<td>
<pre>
'user_id'        => '', // ID of user
'field'          => 'user_login', //key of field and custom field to be dispayed
				For custom fields : {{slug}}
'template'     => '', 
</pre>
						</td>
					</tr>
					<tr>
						<td>User Meta</td>
						<td>[user_meta]</td>
						<td>
<pre>
'user_id'        => '', // ID of user
'field'          => 'first_name', //key of field and custom field to be dispayed
'show_label'     => 'no', //yes
'class'			=> '', //css class
'style'			=> 'div', //Leave empty for no html
</pre>
						</td>
					</tr>
					<tr>
						<td>Welcome User</td>
						<td>[welcome]</td>
						<td>
<pre>
'user_id'        => CURRENT_LOGGED_IN_USER, // ID of user
</pre></td>
					</tr>
					<tr>
						<td>Blog Archive</td>
						<td>[blog]</td>
						<td>
<pre>
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
						<td>
<pre>
'class' => 'fa fa-facebook', //Fontawsome class
</pre>
					</td>
					</tr>
					<tr>
						<td>Date</td>
						<td>[date]</td>
						<td>
<pre>
'format' => 'jS F Y H:i A',human //DateTime format 
'time-zone' => 'Asia/Kolkata', // TimeZone 
</pre> 
						<a href="http://php.net/manual/en/timezones.php" target="_blank">Supported TimeZone</a></td>
					</tr>
					<tr>
						<td>Blog Info</td>
						<td>[info]</td>
						<td>
<pre>
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
<pre>
'post_id' => '', //Post ID
</pre>					
				</td>
			</td>
		</tr>
		<tr>
			<td>Post Title</td>
			<td>[title]</td>
			<td>
<pre>
'post_id' => '', //Post ID
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
	<td>
<pre>
'post_id' => '', //Post ID
'excerpt' => 'false', //true/false
'excerpt_length' => '30',
'more_text' => '...'
'class' => ' ',
'id' => ' '
</pre>
</td>
</td>
</tr>
<tr>
	<td>Post Feature Image</td>
	<td>[featured_image]</td>
	<td>
<pre>
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
	<td>Post Author</td>
	<td>[author]</td>
	<td>
<pre>
Return post author ID
</pre>
</td>
</td>
</tr>
<tr>
	<td>Attached Media</td>
	<td>[attached_media]</td>
	<td>
<pre>
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
	<td>
<pre>
'post_id' => '', 
'texonomy' => 'post_tags', //custom texonomy
'style' => 'list', //list,div
'class' => ' ',
'id' => ' '
</pre>
</td>
</tr>
<tr>
	<td>Post Categories</td>
	<td>[categories]</td>
	<td>
<pre>
'texonomy' => 'category', //custom texonomy
'style' => 'list', //list,div
'class' => ' ',
'id' => ' '
</pre>
</td>
</tr>

<tr>
	<td>Custom Field</td>
	<td>[custom-fields]</td>
	<td>
<pre>
'post_id' => '', 
'field_group' => '82', 
'show_label' => 'no', //yes
'seperator' => ' : ',
'class' => '',
'id' => '',
'style' => '', //// table/div/list
</pre>
</td>
</tr>
<tr>
	<td>Upcoming Tag</td>
	<td>[upcoming_tag]</td>
	<td>
<pre>
'text' => 'Upcoming',
</pre>
</td>
</td>
</tr>
<tr>
	<td>Social Icons</td>
	<td>[social_icon]</td>
	<td>
<pre>
'size' => 'small', // medium,large,xlarge
'style' => 'square', // rounded,circle
'direction' => 'horizontal', // vertical
'theme' => 'colored', // dark,dark-solid,dark-outline,light bg-black,
			light-solid bg-black,light-outline bg-black
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
	<td>[social_share]</td>
	<td>
<pre>
<div class="">
	'size' => 'small', // medium,large,xlarge
	'style' => 'square', // rounded,circle
	<span class="aione-more">
		<span id="welcomeDiv" class="content">
			'direction' => 'horizontal', // vertical
			'theme' => 'colored', // dark,dark-solid,dark-outline,light bg-black,
			light-solid bg-black,light-outline bg-black
			'labels' => "false" // true
			'facebook' => "false" // true
			'twitter' => "false" // true
			'googleplus' => "false" // true
			'linkedin' => "false" // true
			'pinterest' => "false" // true
			'reddit' => "false" // true
			'tumblr' => "false" // true
		</span>
		<span id="more-toggle" class="aione-more-toggle" >Show More</span>
	</span> 
</div>
</pre>
</td>
</td>
</tr>
<tr>
	<td>Add New Post</td>
	<td>[add_new]</td>
	<td>
<pre>
'type' => 'post', // page, any custom pot type
'title' => 'true', // false
'content' => 'true', // false
'status' => 'publish', // draft,pending, all wordpres post status
'field_groups' => '123', // ID of acf field group
'fields' => '', // ID of acf field
'id' => 'add_new_form', // css id name
'class' => 'add-new-form', // css class name
</pre>
</td>
</tr>
<tr>
	<td>Edit Post</td>
	<td>[edit]</td>
	<td>
<pre>
'post_id' => '', // Post ID
'title' => 'true', // false
'content' => 'true', // false
'status' => 'publish', // draft,pending, all wordpres post status
'field_groups' => '123', // ID of acf field group
'fields' => '', // ID of acf field
'id' => 'edit_post_form', // css id name
'class' => 'edit-post-form', // css class name

</pre>
	</td>
</tr>
</tbody>
</table>
</div>
</div>
<script type="text/javascript">
	document.getElementById("more-toggle").addEventListener("click", showmore);

	function showmore() {
		var x = document.getElementById("welcomeDiv");
		if (x.style.display == "block") {
			x.style.display = "none";
		}
		else{
			 x.style.display = "block";
		}
	}
</script>
<style type="text/css">
	.aione-more .content{
		display: none;
	}
</style>
