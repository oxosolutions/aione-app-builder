<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.oxosolutions.com
 * @since      1.0.0
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/includes
 * @author     AmritDeep <amritdeepkaur@gmail.com>
 */
class Aione_App_Builder_Shortcode {
	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Apb_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;
	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'aione_app_builder';
		$this->version = '1.0.0';

		
		/**
		*
		*Shortcode page Functionality
		*
		*/
        add_shortcode( 'login-link', array($this, 'aione_app_builder_login_link_shortcode') );
		add_shortcode( 'register-link', array($this, 'aione_app_builder_register_link_shortcode') );
		add_shortcode( 'logout-link', array($this, 'aione_app_builder_logout_link_shortcode') );
		add_shortcode( 'is_user_logged_in', array($this, 'aione_app_builder_is_user_logged_in_shortcode') );
		add_shortcode( 'user_not_logged_in', array($this, 'aione_app_builder_user_not_logged_in_shortcode') );
		add_shortcode( 'user_not_logged_in_error', array($this, 'aione_app_builder_user_not_logged_in_error_shortcode') );
		add_shortcode( 'access', array($this, 'aione_app_builder_access_check_shortcode') );
		add_shortcode( 'aione-app-builder-login-form', array($this, 'aione_app_builder_login_form_shortcode') );
		add_shortcode( 'home_url', array($this, 'aione_app_builder_home_url_shortcode') );
		
		add_shortcode( 'url', array($this, 'aione_app_builder_url_shortcode') );
		add_shortcode( 'forget-password', array($this, 'aione_app_builder_forget_password_shortcode') );
		add_shortcode( 'list-posts', array($this, 'aione_app_builder_list_post_shortcode') );
		add_shortcode( 'list-comments', array($this, 'aione_app_builder_list_comments_shortcode') );
		add_shortcode( 'faq', array($this, 'aione_app_builder_faq_shortcode') );
		add_shortcode( 'change-password', array($this, 'aione_app_builder_change_password_shortcode') );
		add_shortcode( 'register', array($this, 'aione_app_builder_register_shortcode') );
		add_shortcode( 'users', array($this, 'aione_app_builder_users_shortcode') );
		
		
		add_shortcode( 'aione-post-title', array($this, 'aione_app_builder_template_title_shortcode') );
		add_shortcode( 'aione-post-content', array($this, 'aione_app_builder_template_content_shortcode') );
		add_shortcode( 'aione-post-author', array($this, 'aione_app_builder_template_author_shortcode') );
		add_shortcode( 'aione-post-date', array($this, 'aione_app_builder_template_date_shortcode') );
		add_shortcode( 'aione-custom-fields', array($this, 'aione_app_builder_template_cf_shortcode') );
    }
	public function aione_app_builder_login_link_shortcode( $atts ) {
		extract( shortcode_atts(
				array(
					'class'           => '',
					'text'           => 'Login'
				), $atts )
		);
		$output = "";
		if ( !is_user_logged_in() ) {
			$output .= '<div id="login_link" class="user-links login-link '.$class.'">';
			$output .= '<a href="'.wp_login_url().'" title="' . $text . '">' . $text . '</a>';
			$output .= '</div>';
		} 
		return $output;
	} // End aione_app_builder_login_link_shortcode()
	
	public function aione_app_builder_register_link_shortcode( $atts ) {
		extract( shortcode_atts(
				array(
					'class'           => '',
					'text'           => 'Sign up'
				), $atts )
		);
		$output = "";
		
		if ( !is_user_logged_in() ) {
			$output .= '<div id="login_link" class="user-links login-link '.$class.'">';
			$output .= '<a href="'.wp_registration_url().'" title="' . $text . '">' . $text . '</a>';
			$output .= '</div>';
		} 
		return $output;
	} // End aione_app_builder_register_link_shortcode()
	
	public function aione_app_builder_logout_link_shortcode( $atts ) {
		extract( shortcode_atts(
				array(
					'class'           => '',
					'text'           => 'Logout'
				), $atts )
		);
		$output = "";
		
		if ( is_user_logged_in() ) {
			$output .= '<div id="logout_link" class="user-links logout-link '.$class.'">';
			$output .= '<a href="'.wp_logout_url().'" title="' . $text . '">' . $text . '</a>';
			$output .= '</div>';
		} 
		return $output;
	} // End aione_app_builder_logout_link_shortcode()
	
	public function aione_app_builder_is_user_logged_in_shortcode( $attr, $content = null ) {
		if ( is_feed() || !is_user_logged_in() || is_null( $content ) ){
			return '';
		}
		/* Return the content. */
		return do_shortcode( $content );
		return $return;
	} // aione_app_builder_is_user_logged_in_shortcode ()
	
	public function aione_app_builder_user_not_logged_in_shortcode( $attr, $content = null ) {
		if ( is_user_logged_in() ){	
			return '';
		}
		/* Return the content. */
		return do_shortcode( $content );
	} // aione_app_builder_user_not_logged_in_shortcode ()
	
	public function aione_app_builder_user_not_logged_in_error_shortcode( $attr, $content = null ) {
		if ( is_user_logged_in() ){
			$content = '';
			return $content;
		}
		/* Return the content. */
		return do_shortcode($content);
	} // aione_app_builder_user_not_logged_in_error_shortcode ()

	public function aione_app_builder_access_check_shortcode( $attr, $content = null ) {
		$defaults = array(
			'capability' => '',
			'role' => '',
		);
		extract( shortcode_atts( $defaults, $attr ) );

		if ( is_null( $content ) || is_feed() ){
			return '';
		}
			
		if ( !empty( $capability ) ) {
			$caps = explode( ',', $capability );
			foreach ( $caps as $cap ) {
				if ( current_user_can( trim( $cap ) ) )
				return do_shortcode( $content );
			}
		}

		if ( !empty( $role ) ) {
			$roles = explode( ',', $role );
			foreach ( $roles as $role ) {
				if ( current_user_can( trim( $role ) ) )
				return do_shortcode( $content );
			}
		}
		return '';
	} // aione_app_builder_access_check_shortcode ()
	
	public function aione_app_builder_login_form_shortcode( $atts, $content = null ) {
		extract( shortcode_atts(
			array(
			'echo'           => false,
			'redirect'       => get_option('admin_login_redirect_page'), 
			'form_id'        => 'loginform',
			'label_username' => __( 'Username' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in'   => __( 'Login' ),
			'id_username'    => 'user_login',
			'id_password'    => 'user_pass',
			'id_remember'    => 'rememberme',
			'id_submit'      => 'wp-submit',
			), $atts )
		);
		$output = "";
		$login = (isset($_GET['login']) ? $_GET['login'] : null);
		$errors = array();
		if(isset($login) && $login == 'failed' ){
			//$errors[] = 'Invalid username or password';
			//$output .= $this->aione_show_errors($errors);
			$output .=  '<div style="color:#cc0000;text-align:center;padding:10px">Invalid username or password</div>';
		}

		$args = array(
			'echo'           => $echo,
			'redirect'       => $redirect, 
			'form_id'        => $form_id,
			'label_username' => $label_username,
			'label_password' => $label_password,
			'label_remember' => $label_remember,
			'label_log_in'   => $label_log_in,
			'id_username'    => $id_username,
			'id_password'    => $id_password,
			'id_remember'    => $id_remember,
			'id_submit'      => $id_submit,
			'remember'	     => empty( $instance['remember'] ) ? true : false,
			'value_username' => esc_attr( $instance['value_username'] ),
			'value_remember' => !empty( $instance['value_remember'] ) ? true : false
		);
		if ( !is_user_logged_in() ) {
		$output .= wp_login_form( $args );
		} else {
			$output .= "You are already logged in! ";
			$output .= '<a href="'.wp_logout_url().'" title="Logout" class="aione-common-button">Logout</a>';
		}
		return $output;
	} // End aione_login_form_shortcode()
	
	public function aione_app_builder_home_url_shortcode( $attr, $content = null ) {
		return home_url();
	} // End aione_app_builder_home_url_shortcode()
	
	public function aione_app_builder_url_shortcode( $atts ) {
		extract( shortcode_atts(
			array(
				'type' => '',
				'id' => '0',
				'path' => '',
				'title' => '',
				'action' => '',
				'class' => '',
			), $atts )
		);
		
		if(!$id && !$path && !$title && !$action){
			return home_url();
		} else {
			$page_id = 0;
			if( $id && is_numeric ($id) ){
				$page_id = $id;
			}
			
			if($path != ''){
				$page_id = get_page_by_path($path);
			}
			
			if($title != ''){
				$page_id = get_page_by_title($title);
			}
			
			if($action != ''){
				if($action == 'logout'){
					return wp_logout_url();
				}	
			}
			
			if ($page_id) {
				return get_page_link($page_id);
			} else {
				return null;
			}	
		}
	} // End aione_app_builder_url_shortcode()
	
	public function aione_app_builder_forget_password_shortcode( $atts ) {
		$output = "";
		$errors = array();
		$success_messages = array();
		$action = 'email';
		if( isset($action) && !empty($action) ){
			if($action == 'email'){
				if( isset($_POST['email']) ){

					$email = trim($_POST['email']);

					if(empty($email)) {
						$errors[] = 'Please enter email address';
					}

					if(!is_email($email)) {
						$errors[] = 'Invalid email';
					}

					if(!email_exists($email)) {
						$errors[] = 'Email address does not exist';
					}

					if ( empty($errors) ) {
						$user = get_user_by( 'email', $email );
						$user_id = $user->ID;
						$activation_key = wp_generate_password( 8, false );
						$update_error = wp_update_user( array( 'ID' => $user_id, 'user_pass' => esc_attr( $activation_key ) ) );
						$user_notification = aione_app_builder_forget_password_notification($user_id, $activation_key);
						if(is_int($update_error) && $user_notification){
							$success_messages[] = "Password reset email sent.";
							$success_messages[] = "Please also check spam folder.";
						}
					}
				}
			}	  
			if ( !empty($errors) ) {
				$output .= '<div class="aione_errors"><ul>';
				foreach($errors as $error){
					$output .= '<li class="error"><strong>' . __('Error') . '</strong>: ' . $error . '</li>';
				}
				$output .= '</ul></div>';
			}
			if ( !empty($success_messages) ) {
				$output .= '<div class="aione_success"><ul>';
				foreach($success_messages as $success_message){
					$output .= '<li class="success">' . $success_message . '</li>';
				}
				$output .= '</ul></div>';
			}
			$output .= '<form method="post" class="login-signup" id="login-signup" action="'.get_permalink().'">';
			if($action == 'email'){
				$output .= '<p class="forgot-password-user">';
				$output .= '<label for="email">Your Email Address<span class="required">*</span></label>';
				$output .= '<input class="text-input field-long" name="email" type="text" id="email">';
				$output .= '</p>';
			}
			$output .= '<p class="reset-password">';
			$output .= '<input name="reset-password" type="submit" class="field-long submit btn button-primary button application-button" value="Reset Password">';
			$output .= '<input name="action" type="hidden" value="'.$action.'">';
			$output .= '</p>';
			$output .= '</form>';
		} else {
			$output .= '<p class="aligncenter marginbottom10">Reset Password with Email Address via Activation Key.</p>';
			$output .= '<div class="button application-buttons" id="register-as-parent"><a title="Reset Password with Email" href="?action=email">Reset Password with Email</a></div>';
		}
		return $output;
	} // END aione_app_builder_forget_password_shortcode()
	
	
// Redefine user notification function
    function aione_app_builder_forget_password_notification( $user_id, $user_key ) {

        $user = new WP_User( $user_id );

        $user_login = stripslashes( $user->user_login );
        $user_email = stripslashes( $user->user_email );
        // $headers = array('Content-Type: text/html; charset=UTF-8');
        $headers = 'From: Darlic <no-reply@darlic.com>;';

        $message  = "Hi " . $user->first_name . " " . $user->last_name . ", <br><br>";
        $message .= "Someone has requested to reset password for this account" . "<br><br>";
        $message .= "<strong>Account Details</strong>" . "<br><br><br><br>";
        $message .= "Login URL: " . wp_login_url() . "<br><br>";
        $message .= "Username: " . $user_login . "<br>";
        $message .= "E-mail: " . $user_email . "<br>";
        $message .= "Password : " . $user_key . "<br><br>";

        $message .= "If you have any problems, please contact administrator<br><br>";
        $message .= "Thanks!";
		$subject = sprintf( __('Reset Password for %s account'), get_option('blogname') );;

        $notification_sent = wp_mail( $user_email, $subject, $message, $headers );
        if($notification_sent){
            return true;
        } else {
            return false;
        }
    }
	// END aione_app_builder_forget_password_notification()
	
	public function aione_app_builder_list_post_shortcode( $atts ) {
			// Attributes
			extract( shortcode_atts(
					array(
						'cat'       => '',
						'cat_id'    => '',
						'author'    => '',
						'author_id' => '',
						'count'     => '',
						'id'        => '',
						'class'     => ''	
					), $atts )
			);
			
			global $theme_options, $post;
			$output = "";
			// WP_Query arguments
			$args = array (
				'post_type'              => 'post',
				'post_status'            => 'publish',
				'cat'                    => $cat_id,
				'category_name'          => $cat,
				'author'                 => $author_id,
				'author_name'            => $author,
				'pagination'             => false,
				'posts_per_page'         => $count,
				'ignore_sticky_posts'    => false,
				'order'                  => 'DESC',
				'orderby'                => 'date',
				'cache_results'          => true,
				'update_post_meta_cache' => true,
				'update_post_term_cache' => true,
			);

			$resent_posts = new WP_Query($args);
			
			if($resent_posts->have_posts()){
				$output .= '<ul class="list-posts">';
					while($resent_posts->have_posts()){
						$resent_posts->the_post(); 
						$output .= '<li>';
							if(has_post_thumbnail()){
								$output .= '<div class="post-image">';
								$output .= '<a href="'.get_permalink().'">';
								$output .= 	get_the_post_thumbnail($post->ID,'tabs-img');
								$output .= '</a>';
								$output .= '</div>';
							} else {
								$output .= '<div class="post-image">';
								$output .= '<a href="'.get_permalink().'">';
								$output .= 	'<img width="52" height="50" src="'.plugin_dir_url( __FILE__ ).'/assets/images/placeholder_grey_52x50.png" class="attachment-tabs-img wp-post-image" alt="'.get_the_title().'" >';
								$output .= '</a>';
								$output .= '</div>';						
							}
							$output .= '<div class="post-holder">';
								$output .= '<a href="'.get_permalink().'" class="post-title">'.get_the_title().'</a>';
								$output .= '<div class="post-meta">';
								$output .= 	get_the_time($theme_options['date_format']);
								$output .= '</div>';
							$output .= '</div>';
							$output .= '<div class="aione-clearfix"></div>';
						$output .= '</li>';
						
					}
				$output .= '</ul>';
			}  else {
				$output .= '<h5 class="font-size-16 aligncenter">No Posts Available.</h5>';
			}
			return $output;
		} // END aione_app_builder_list_post_shortcode()
		
		public function aione_app_builder_list_comments_shortcode( $atts ) {
			// Attributes
			extract( shortcode_atts(
					array(
						'count'           => '',
						'id'           => '',
						'class'           => ''
						
					), $atts )
			);
			
			global $theme_options, $post;
			
			$output = "";
			$number = $count;
			
		$args = array(
			'include_unapproved' => '',
			'fields' => '',
			'ID' => '',
			'comment__in' => '',
			'comment__not_in' => '',
			'karma' => '',
			'number' => $number,
			'offset' => '',
			'orderby' => '',
			'order' => 'DESC',
			'parent' => '',
			'post_ID' => '', // ignored (use post_id instead)
			'post_id' => 0,
			'post__in' => '',
			'post__not_in' => '',
			'post_author' => '',
			'post_name' => '',
			'post_parent' => '',
			'post_status' => '',
			'post_type' => '',
			'status' => 'all',
			'type' => '',
			'type__in' => '',
			'type__not_in' => '',
			'user_id' => '',
			'search' => '',
			'count' => false,
			'meta_key' => '',
			'meta_value' => '',
			'meta_query' => '',
			'date_query' => null, // See WP_Date_Query
		);
		$the_comments = get_comments( $args );

			if($the_comments){
				$output .= '<ul class="list-posts resent-comments">';
				foreach($the_comments as $comment) {
					$output .= '<li>';
					$output .= '<div class="post-image">';
					$output .= '<a>';
					$output .= 	get_avatar($comment, '52');
					$output .= '</a>';
					$output .= '</div>';
					$output .= '<div class="post-holder">';
					$output .= strip_tags($comment->comment_author) . ' says:';
					$output .= '<div class="post-meta">';
					$output .= '<a class="comment-text-side" href="' . get_permalink($comment->ID).'#comment-' .$comment->comment_ID . '" title="'.strip_tags($comment->comment_author) .' on '.$comment->post_title .'">';
					$output .= strip_tags($comment->com_excerpt);
					$output .= '...</a>';
					$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="aione-clearfix"></div>';
					$output .= '</li>';
				}
				$output .= '</ul>';
			} else {
				$output .= '<h5 class="font-size-16 aligncenter">No Comments Available.</h5>';
			}
			return $output;
		} // END aione_app_builder_list_comments_shortcode()
		
		public function aione_app_builder_faq_shortcode($atts) {

			extract(shortcode_atts(
				array(
					'category' => '',
					'id' => '',
					'class' => '',
				), $atts)
			);
			
			$output = '';
			if (!$category){
			return '';
				$portfolio_category = get_terms('faq_category');
				if ($portfolio_category){
				$output .= '<ul class="faq-tabs clearfix">';
				$output .= '<li class="active"><a data-filter="*" href="#">' . __('All', 'Aione') . '</a></li>';
				foreach ($portfolio_category as $portfolio_cat){
					$output .= '<li><a data-filter=" ' . urldecode($portfolio_cat->slug) .'"href="#">' . $portfolio_cat->name . '</a></li>';
				}
				$output .= '</ul>';
				}
			} 
			
			$output .= '<div class="portfolio-wrapper">';
			$output .= '<div class="accordian aione-accordian">';
			$output .= '<div class="panel-group" id="accordian-one">';
			
			$args = array(
				'post_type' => 'aione_faq',
				'tax_query' => array(
				array(
					'taxonomy' => 'faq_category',
					'field'    => 'slug',
					'terms'    => $category,
				),
			),
				'nopaging' => true
			);
			$gallery = new WP_Query($args);
			$count = 0;
			while($gallery->have_posts()): $gallery->the_post();
		  
			
				$count++;
				$item_classes = '';
				$item_cats = get_the_terms($post->ID, 'faq_category');
				if ($item_cats):
					foreach ($item_cats as $item_cat) {
						$item_classes .= urldecode($item_cat->slug) . ' ';
					}
				endif;
				$output .= '<div class="aione-panel panel-default faq-item '. $item_classes .'">';
				$output .= aione_render_rich_snippets_for_pages();
				$output .= '<div class="panel-heading">';
				$output .= '<h4 class="panel-title toggle"><a data-toggle="collapse" class="collapsed" data-parent="#accordian-one" href="#collapse-'.get_the_ID().'"><i class="fa-aione-box"></i>';
				$output .= get_the_title();
				$output .= '</a></h4>';
				$output .= '</div>';
				$output .= '<div id="collapse-'.get_the_ID().'" class="panel-collapse collapse">';
				$output .= '<div class="panel-body toggle-content post-content">';
				$output .= get_the_content();
				$output .= '</div>';
				$output .= '</div>';
				$output .= '</div>';
			endwhile;
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
			return $output;

		} // END aione_app_builder_faq_shortcode()
		
		public function aione_app_builder_change_password_shortcode( $atts ) {
			// Attributes
			extract( shortcode_atts(
					array(
					), $atts )
			);
			$output = "";
			$errors = array();

			$current_user = wp_get_current_user();

			if ( !empty($_POST) && !empty( $_POST['action'] ) && $_POST['action'] == 'changepassword' ) {
				/* Update user password */
				if ( !empty($_POST['current_pass']) && !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
					if ( !wp_check_password( $_POST['current_pass'], $current_user->user_pass, $current_user->ID) ) {
						$errors[] = 'Your current password does not match. Please retry.';
					} elseif ( $_POST['pass1'] != $_POST['pass2'] ) {
						$errors[] = 'The passwords do not match. Please retry.';
					} elseif ( strlen($_POST['pass1']) < 5 ) {
						$errors[] = 'New Password is too small. Minimum five characters required.';
					} elseif ( false !== strpos( wp_unslash($_POST['pass1']), "\\" ) ) {
						$errors[] = 'Password may not contain the character "\\" (backslash).';
					} else {
						$update_error = wp_update_user( array( 'ID' => $current_user->ID, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
						if ( !is_int($update_error) ) {
							$errors[] = 'An error occurred while updating your profile. Please retry.';
						} 
					}
					if ( empty($errors) ) {
						$output .= '<div class="aione-alert alert success alert-dismissable alert-success alert-shadow">';
						$output .= '<button aria-hidden="true" data-dismiss="alert" class="close toggle-alert" type="button">×</button>';
						$output .= '<span class="alert-icon"><i class="fa fa-lg fa-check-circle"></i></span>';
						$output .= 'Password Changed successfully!';
						$output .= '</div>';

						$output .= '<div class="aione-alert alert general alert-dismissable alert-info alert-shadow">';
						$output .= 'You will be redirected to login page in <span id="redirectcountdown">10</span> seconds.';
						$output .= '</div>';

					   $output .= "<script type='text/javascript'>jQuery(window).ready( function() { var time = 10; setInterval( function() { time--; if (time >= 0){ jQuery('#redirectcountdown').html(time);} if (time === 0) { window.location = '".home_url( '/' )."';} }, 1000 );});</script>";
					}
				} else {
					if ( empty($_POST['current_pass']) ) {
						$errors[] = 'Current Password can not be empty';
					}
					if ( empty($_POST['pass1'] ) ) {
						$errors[] = 'New Password can not be empty';
					}
					if ( empty( $_POST['pass2'] ) ) {
						$errors[] = 'Confirm Password can not be empty';
					}
				}
			} 

			if ( !empty($errors) ) {
				$output .= '<div class="aione_errors"><ul>';
				foreach($errors as $error){
					$output .= '<li class="error"><strong>' . __('Error') . '</strong>: ' . $error . '</li>';
				}
				$output .= '</ul></div>';
			}
			if(is_user_logged_in()) {
			
			$output .= '<form method="post" class="login-signup" id="login-signup" action="">';
			$output .= '<ul class="form-style-1">';
			$output .= '<li>';
			$output .= '<label for="current_pass">Current Password <span class="required">*</span></label>';
			$output .= '<input class="text-input field-long" name="current_pass" type="password" id="current_pass">';
			$output .= '</li>';
			$output .= '<li>';
			$output .= '<label for="pass1">New Password <span class="required">*</span></label>';
			$output .= '<input class="text-input field-long" name="pass1" type="password" id="pass1">';
			$output .= '</li>';
			$output .= '<li>';
			$output .= '<label for="pass2">Confirm Password <span class="required">*</span></label>';
			$output .= '<input class="text-input field-long" name="pass2" type="password" id="pass2">';
			$output .= '</li>';
			$output .= '<li>';
			$output .= '<input name="updateuser" type="submit" id="updateuser" class="field-long submit btn button-primary button application-button" value="Change Password">';
			$output .= '<input name="action" type="hidden" id="action" value="changepassword">';
			$output .= '</li>';
			$output .= '</ul>';
			$output .= '</form>';
		   }
			return $output;
		} // END aione_app_builder_change_password_shortcode()
		
		public function aione_app_builder_register_shortcode( $atts ) {
			// Attributes
			extract( shortcode_atts(
					array(
					), $atts )
			);
			$output = "";
			// only show the registration form to non-logged-in members
			if(!is_user_logged_in()) {
				global $aione_load_css;
				// set this to true so the CSS is loaded
				$aione_load_css = true;
				// check to make sure user registration is enabled
				$registration_enabled = get_option('users_can_register');

				// only show the registration form if allowed
				if($registration_enabled) {
					$errors = array();
					// load from post

					if( isset($_POST['action']) && $_POST['action'] == 'add_new' && !empty($_POST['fields'])){
						$user_login		= $_POST["aione_user_login"];
						$user_email		= $_POST["aione_user_email"];
						$user_pass		= $_POST["aione_user_pass"];
						$pass_confirm 	= $_POST["aione_user_pass_confirm"];
						$user_first		= $_POST["aione_user_fname"];
						$user_last		= $_POST["aione_user_lname"];
						
						// this is required for username checks
						if($user_email == '') {
							//empty email
							$errors[] = 'Please enter email address';
						} else {
							if(!is_email($user_email)) {
								//invalid email
								$errors[] = 'Invalid email';
							}
							if(email_exists($user_email)) {
								//Email address already registered
								$errors[] = 'Email already registered';
							}
						}
					
						if($user_login == '') {
							// empty username
						   $errors[] = 'Please enter a username';
						} else {
							if(!validate_username($user_login)) {
								// invalid username
								$errors[] = 'Invalid username';
							}

							if(username_exists($user_login)) {
								// Username already registered
								$errors[] = 'Username already taken';
							}				
						}

						if($user_pass == '') {
							//Empty password
							$errors[] = 'Please enter a password';
						}

						if($user_pass != $pass_confirm) {
							// passwords do not match
							$errors[] = 'Passwords do not match';
						}
						if(!empty($user_first) && !preg_match('/^[a-zA-Z\s]+$/', $user_first) ) {
							//Invalid Mobile
							$errors[] = 'Invalid first name. Numbers not allowed.';
						}
						
						if(!empty($user_last) && !preg_match('/^[a-zA-Z\s]+$/', $user_last) ) {
							//Invalid Mobile
							$errors[] = 'Invalid last name. Numbers not allowed.';
						}
					
						// only create the user in if there are no errors
						if(empty($errors)) {
							$user_role = get_option('default_role');
							$new_user_id = wp_insert_user(array(
									'user_login'		=> $user_login,
									'user_pass'	 		=> $user_pass,
									'user_email'		=> $user_email,
									'first_name'		=> $user_first,
									'last_name'			=> $user_last,
									'user_registered'		=> date('Y-m-d H:i:s'),
									'role'			=> $user_role
								)
							);
							if(is_int($new_user_id)) {
								$temp_user_id = "user_".$new_user_id;
								update_field( 'register_phone_number', $_POST['fields']['field_5801ee639f0d5'], $temp_user_id );
								update_field( 'register_address', $_POST['fields']['field_5801ee859f0d6'], $temp_user_id );
								 
								
							
								
								$output .= 'Thank you for registering.';
						
								
							} else {
								$errors[] = 'Some error occurred. Please contact Administrator.';
							}
						} else {
							foreach($errors as $error){
								$output .=  '<div style="color:#cc0000;text-align:center;padding:10px">'.$error.'</div>';
							}
							//$output .= $this->aione_show_errors($errors);
							$output .= $this->aione_app_builder_user_registration_form();
						}
					} else {
						$output .= $this->aione_app_builder_user_registration_form();
					}
				} else {
					$output .= __('User registration is not enabled!');
				}
			} else {
					$output .= __('You are already logged in!');
			}
			return $output;
		} //END aione_app_builder_register_shortcode()

	public function aione_app_builder_user_registration_form( ) {
			$html_before_fields = '
			
			<form id="aione-registration-form" class="aione-form register form acf-form" action="'.get_permalink().'" method="post">
				<div class="postbox acf_postbox no_box">
				
				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="aione_user_login">Enter Username<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_login" id="aione_user_login" class="textbox large required" type="text" placeholder="Username" value=""/></div>
					<p class="label"><label for="aione_user_login">Only lowercase letters, numbers, underscore are accepted</label></p>
				</div>
				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="aione_user_email">Your Email Address<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_email" id="aione_user_email" class="textbox large required" type="email" placeholder="Your Email Address" value=""/></div>
				</div>

				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="password">Enter Password<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_pass" id="password" class="textbox large required" type="password"/></div>
				</div>

				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="password_again">Enter Password Again<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_pass_confirm" id="password_again" class="textbox large required" type="password"/></div>
				</div>
				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="aione_user_fname">Enter First Name</label></p>
					<div class="acf-input-wrap"><input name="aione_user_fname" id="aione_user_fname" class="textbox large" type="text" placeholder="First Name" value=""/></div>
				</div>
				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="aione_user_lname">Enter Last Name</label></p>
					<div class="acf-input-wrap"><input name="aione_user_lname" id="aione_user_lname" class="textbox large" type="text" placeholder="Last Name" value=""/></div>
				</div>


				</div>
			
			';
			$html_after_fields = '<div class="field">
				<input type="hidden" name="action" value="add_new">
				<input type="submit" value="Submit">
			</div>
			';
			
			$field_groups = get_option('aione_app_builder_registration_custom_field_groups');
			if(!is_array($field_groups)){
				$field_groups = array($field_groups);
			}
			$options = array(
				'post_id'	            => 'new_post',
				'form'                  => false,
				'field_groups'          => $field_groups,
				'post_title'            => false,
				'post_content'          => false,
				'html_before_fields'    => $html_before_fields,
				'html_after_fields'     => $html_after_fields,
				'instruction_placement' => 'field',
				'submit_value'	        => 'Submit',
				'updated_message'	    => 'Registered Successfully',
			);

			ob_start();
			acf_form($options);
			$output .= ob_get_contents();
			ob_end_clean();
			return $output;
		} // END aione_app_builder_user_registration_form()
		
	public function aione_app_builder_users_shortcode( ) {
			$output = "";
			$blogusers = get_users( 'blog_id=1&role=subscriber' );
			$count = 1;
			// Array of WP_User objects.
			foreach ( $blogusers as $user ) {
				$output .= '<br><span>'.$count.'. ' . esc_html( $user->user_login ) . '</span>';
				$count++;
			}
			
			return $output;
			
		} // END aione_app_builder_users_shortcode
		
	public function aione_app_builder_template_title_shortcode( $attr, $content = null ) {
		return "<a href='".get_post_permalink($post->ID)."'>".get_the_title()."</a>";
	} 
	public function aione_app_builder_template_content_shortcode( $attr, $content = null ) {
		//return the_content();
		return get_the_content();
	} 
	public function aione_app_builder_template_author_shortcode( $attr, $content = null ) {
		//return the_author();
		return get_the_author();
	} 
	public function aione_app_builder_template_date_shortcode( $attr, $content = null ) {
		$defaults = array(
			'format' => 'D M j',
		);
		extract( shortcode_atts( $defaults, $attr ) );
		if ( !empty( $format ) ) {
			return get_the_date($format);
		}
	} 
	public function aione_app_builder_template_cf_shortcode( $attr, $content = null ) {
		global $post;
		$defaults = array(
			'display' => 'all',
			'label' => true,
			'value' => true,
			'seprator' => ":",
		);
		extract( shortcode_atts( $defaults, $attr ) );
		$output = "";
		if ( !empty( $display ) ) {
			if($display == "all") { 
			 $displayItems = get_field_objects(); echo "<pre>";print_r($displayItems);echo "</pre>";
				if( $displayItems ){
					foreach( $displayItems as $field )
					{
						$field_type = $field['type'];
						if($field_type == "text" || $field_type == "textarea" || $field_type == "number" || $field_type == "email" || $field_type == "password" || $field_type == "true_false"){
							$field_label = $field['label'] ;
							$field_value = $field['value'] ;
							if($label == true && $value == true) {
								$output .= "<div id='".$field['id']."'>".$field_label." ".$seprator." ".$field_value."</div>";
							} else {
								if($label == true && $value == false) {$output .= $field_label;}
								if($label == false && $value == true) {$output .= $field_value;}
							}
						} elseif ($field_type == "image"){
							$field_value = $field['value']['url'] ;
							$output .= "<div id='".$field['id']."'><a href='".$field_value."'><img src='".$field_value."'></a></div>";
						} elseif ($field_type == "file"){
							$field_value = $field['value']['url'] ;
							$output .= "<div id='".$field['id']."'><a href='".$field_value."'>".$field_value."</a></div>";
						} elseif ($field_type == "select" || $field_type == "radio"){
							$field_label = $field['label'] ;
							$ansvalue = $field['value'];
							$anslabel = $field['choices'][ $ansvalue ]; 
							$output .= "<div id='".$field['id']."'>".$field_label." ".$seprator." ".$anslabel."</div>";
						} elseif ($field_type == "checkbox") {
							$field_label = $field['label'] ;
							$field_value = $field['value'] ;
							$field_value = implode(", ",$field_value);
							$output .= "<div id='".$field['id']."'>".$field_label." ".$seprator." ".$field_value."</div>";
						}
						
					}
				}
			} else {
				$displayItems = explode( ',', $display );
					foreach ( $displayItems as $displayItem ) {
					$field = get_field_object($displayItem);
					$field_label = $field['label'] ;
					$field_value = $field['value'] ;
					if($label == true && $value == true) {
						$output .= "<div id='".$field['id']."'>".$field_label." ".$seprator." ".$field_value."</div>";
					} else {
						if($label == true && $value == false) {$output .= "<div id='".$field['id']."'>".$field_label."<div>";}
						if($label == false && $value == true) {$output .= "<div id='".$field['id']."'>".$field_value."<div>";}
					}
				}
			}
			
			
		}
		return $output;
	} 
	
	
		
}
