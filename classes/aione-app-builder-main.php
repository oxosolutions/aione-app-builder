<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Aione_App_Builder {

	private static $_instance = null;
	public $settings = null;
	public $_version;
	public $_token;
	public $file;
	public $dir;
	public $assets_dir;
	public $assets_url;
	public $script_suffix;

	public function __construct ( $file = '', $version = '1.0.1' ) {
		$this->_version = $version;
		$this->_token = 'aione-app-builder';

		// Load plugin environment variables
		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/aione-app-builder/assets/') ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		// add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );
		// Load admin JS & CSS
		// add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );
		
		// Start Add Shotcodes 
		add_shortcode( 'aione-vcarousel', array($this, 'aione_app_builder_shortcode') );
	    add_shortcode( 'is_user_logged_in', array($this, 'aione_is_user_logged_in_shortcode') );
		add_shortcode( 'user_not_logged_in', array($this, 'aione_user_not_logged_in_shortcode') );
		add_shortcode( 'user_not_logged_in_error', array($this, 'aione_user_not_logged_in_error_shortcode') );
		add_shortcode( 'access', array($this, 'aione_access_check_shortcode') );
		add_shortcode( 'aione-login-link', array($this, 'aione_login_link_shortcode') );
		add_shortcode( 'aione-register-link', array($this, 'aione_register_link_shortcode') );
		add_shortcode( 'aione-logout-link', array($this, 'aione_logout_link_shortcode') );
		add_shortcode( 'aione-user-welcome', array($this, 'aione_user_welcome_shortcode') );
		add_shortcode( 'aione-login-form', array($this, 'aione_login_form_shortcode') );
		add_shortcode( 'home_url', array($this, 'home_url_shortcode') );
		add_shortcode( 'url', array($this, 'url_shortcode') );
		add_shortcode( 'aione-forget-password', array($this, 'aione_forget_password_shortcode') );
		add_shortcode( 'aione-list-posts', array($this, 'aione_list_post_shortcode') );
		add_shortcode( 'aione-list-comments', array($this, 'aione_list_comments_shortcode') );
		add_shortcode( 'faq', array($this, 'aione_faq_shortcode') );
		add_shortcode( 'aione-change-password', array($this, 'aione_change_password_shortcode') );
		add_shortcode( 'aione-register', array($this, 'aione_register_shortcode') );
		
		// End Add Shotcodes 
		add_action( 'admin_menu', array($this, 'remove_admin_menus_app_builder'), 9999 );
		add_action( 'wp_dashboard_setup', array($this, 'oxo_remove_wp_dashboard_widgets') );
		add_action('init', array($this, 'oxo_register_redirect')) ;
		add_action( 'admin_menu', array( $this, 'aione_app_builder_menu' ) );
		
		add_action( 'wp_login_failed', array($this, 'aione_login_fail_redirect'), 10 );
		
		//add_filter logout_redirect_page
		$logout_redirect_page_maincheck = get_option('logout_redirect_page');
			if(isset($logout_redirect_page_maincheck)){
				if(!empty($logout_redirect_page_maincheck)){
					add_action('wp_logout',array($this, 'go_home'));
				}
			}

		//add_filter aione_forgot_password_page
		$aione_forgot_password_page_maincheck = get_option('aione_forgot_password_page');
			if(isset($aione_forgot_password_page_maincheck)){
				if(!empty($aione_forgot_password_page_maincheck)){
					add_filter( 'lostpassword_url',  array($this, 'aione_lostpassword_url'), 10, 0 );
				}
			}
		
		//add_filter login_redirect
		$admin_login_redirect_page_maincheck = get_option('admin_login_redirect_page');
			if(isset($admin_login_redirect_page_maincheck)){
				if(!empty($admin_login_redirect_page_maincheck)){
					add_filter('login_redirect', array($this, 'admin_login_redirect'), 10, 3);
				} 
			}
	
		//add_filter login URL
		$aione_login_page_maincheck = get_option('aione_login_page');
			if(isset($aione_login_page_maincheck)){
				if(!empty($aione_login_page_maincheck)){
						add_filter( 'login_url', array($this, 'aione_login_url'), 10, 2 );
				}
			}
			
		//add_filter aione_register_page
		   $aione_register_page_maincheck = get_option('aione_register_page');
				if(isset($aione_register_page_maincheck)){
					if(!empty($aione_register_page_maincheck)){
						add_filter( 'register_url', array($this, 'aione_register_url') );
					}
				}
        
		// Handle localisation
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );
	} // End __construct ()
	
	
	public function aione_is_user_logged_in_shortcode( $attr, $content = null ) {
	if ( is_feed() || !is_user_logged_in() || is_null( $content ) ){
		return '';
	}
	/* Return the content. */
	return do_shortcode( $content );
	return $return;
	} // aione_is_user_logged_in_shortcode ()
	
	
	public function aione_user_not_logged_in_shortcode( $attr, $content = null ) {
	if ( is_user_logged_in() ){	
		return '';
	}
	/* Return the content. */
	return do_shortcode( $content );
	} // aione_user_not_logged_in_shortcode ()
	

	public function aione_user_not_logged_in_error_shortcode( $attr, $content = null ) {
	if ( is_user_logged_in() ){
		$content = '';
		return $content;
	}
	/* Return the content. */
	return do_shortcode($content);
	} // aione_user_not_logged_in_error_shortcode ()
	
	public function aione_access_check_shortcode( $attr, $content = null ) {
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
	} // aione_access_check_shortcode ()
	
	public function aione_login_link_shortcode( $atts ) {
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
	} // End aione_login_link_shortcode()

	public function aione_register_link_shortcode( $atts ) {
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
	} // End aione_register_link_shortcode()


	public function aione_logout_link_shortcode( $atts ) {
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
	} // End aione_logout_link_shortcode()


	public function aione_user_welcome_shortcode( $atts ) {
		extract( shortcode_atts(
				array(
					'class'           => '',
					'text'           => 'Welcome'
				), $atts )
		);
		$output = "";
		
		if ( is_user_logged_in() ) {
			$current_user = wp_get_current_user();
			if( $current_user->display_name != ""){
				$user_welcome = $current_user->display_name;
			} elseif($current_user->user_firstname != '' && $current_user->user_lastname != ''){
				$user_welcome = $current_user->user_firstname . ' ' . $current_user->user_lastname;
			} else {
				$user_welcome = $current_user->user_login;
			}
			$output .= '<div id="user_welcome" class="user-links user-welcome '.$class.'">';
			$output .= $text . ' <a href="' . get_edit_user_link( $current_user->ID ) . '" title="' . $text . ' ' . $user_welcome . '">' . $user_welcome . '</a>';
			$output .= '</div>';
		} 
		return $output;
	} // End aione_user_welcome_shortcode()


	public function aione_login_form_shortcode( $atts, $content = null ) {
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
			$errors[] = 'Invalid username or password';
			$output .= aione_show_errors($errors);
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


	public function aione_login_fail_redirect( $username ) {
	   $referrer = $_SERVER['HTTP_REFERER'];
	   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
		  wp_redirect( $referrer . '?login=failed' );
		  exit;
	   }
	} // End aione_login_fail_redirect()

	public function home_url_shortcode( $attr, $content = null ) {
		return home_url();
	} // End home_url_shortcode()

// URL Shortcode
	public function url_shortcode( $atts ) {
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
	} // End url_shortcode()

	public function aione_forget_password_shortcode( $atts ) {
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
                    $user_notification = aione_forget_password_notification($user_id, $activation_key);
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
} // END aione_forget_password_shortcode()

// Redefine user notification function
    function aione_forget_password_notification( $user_id, $user_key ) {

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
 // END aione_forget_password_notification()

	public function aione_list_post_shortcode( $atts ) {
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
		} // END aione_list_post_shortcode()

		// Comments List Shortcode
	public function aione_list_comments_shortcode( $atts ) {
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
		} // END aione_list_comments_shortcode()

		// Add Shortcode
	public function aione_faq_shortcode($atts) {

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

		} // END aione_faq_shortcode()

	public function aione_change_password_shortcode( $atts ) {
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
		} // END aione_change_password_shortcode()

	public function aione_register_shortcode( $atts ) {
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
								
								// loop through and save $_POST data
								foreach( $_POST['fields'] as $k => $v ){
									// get field
									$f = apply_filters('acf/load_field', false, $k );
									// update field
									do_action('acf/update_value', $v, $post_id, $f );
								}
								// foreach
								
								$output .= 'Thank you for registering.';
						
								//wp_redirect($redirect_url);
								/*
								$output .= '<script type="text/javascript">
								<!--
								   window.location="'.$redirect_url.'";
								//-->
								</script>';
								*/
							} else {
								$errors[] = 'Some error occurred. Please contact Administrator.';
							}
						} else {
							$output .= aione_show_errors($errors);
						}
					} else {
						$output .= $this->aione_user_registration_form();
					}
				} else {
					$output .= __('User registration is not enabled!');
				}
			} else {
					$output .= __('You are already logged in!');
			}
			return $output;
		} //END aione_register_shortcode()

	public function aione_user_registration_form( ) {
			$html_before_fields = '
			<div style="width:100%;padding:10px;">By signing up, users will ultimately be able to customize their dashboard experience and connect with other users that share similar interests. Users will also have the option of receiving future updates on new reports, resources and important changes made to the Dashboard.</div>
			<form id="aione-registration-form" class="aione-form register form acf-form" action="'.get_permalink().'" method="post">
				<div class="postbox acf_postbox no_box">
				
				<div class="aione_form_field field field_type-text">
					<p class="label"><label for="aione_user_login">Enter Username<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_login" id="aione_user_login" class="textbox large required" type="text" placeholder="Username" value=""/></div>
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
				</div>
			
			';
			$html_after_fields = '<div class="field">
				<input type="hidden" name="action" value="add_new">
				<input type="submit" value="Submit">
			</div>
			';
			
			$field_groups = get_option('aione_registration_custom_field_groups');
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
		} // END aione_user_registration_form()


	/* ================== Admin Side Options======================= */
	
    function remove_admin_menus_app_builder() { 

		global $current_user, $wpdb;
		$role = $wpdb->prefix . 'capabilities';
		$current_user->role = array_keys($current_user->$role);
		$role = $current_user->role[0];

		$aione_oxo_admin_options = get_option($role.'_oxo_admin_options');
		$oxomenu = $aione_oxo_admin_options['oxomenu'];
		$oxosubmenu = $aione_oxo_admin_options['oxosubmenu'];
		
				
		if(!empty($oxomenu)){
			foreach($oxomenu as $keyoxomenu => $valueoxomenu){
				foreach($valueoxomenu as $keyioxomenu => $valueioxomenu){
					remove_menu_page($valueioxomenu);
				}
			} 
		}
		
		if(!empty($oxosubmenu)){
			foreach($oxosubmenu as $keyoxosubmenu => $valueoxosubmenu){
				foreach($valueoxosubmenu as $keyioxosubmenu => $valueioxosubmenu){
					$submenuvalues = explode(',',$valueioxosubmenu);
					remove_submenu_page( trim($submenuvalues[0]), trim($submenuvalues[1]) );
		
				}
			} 
		}
		
    } 
	/* ==== */ 
	
	function oxo_remove_wp_dashboard_widgets() {
		
    global $wp_meta_boxes, $wp_registered_widgets, $wp_registered_widget_controls, $wp_dashboard_control_callbacks, $current_user, $wpdb;
		$role = $wpdb->prefix . 'capabilities';
		$current_user->role = array_keys($current_user->$role);
		$role = $current_user->role[0];
        $aione_oxo_admin_options_s = get_option($role.'_oxo_admin_options');
	    $active = $aione_oxo_admin_options_s['offwidget'];

    foreach ($active as $key => $value) { 
        remove_meta_box($key, 'dashboard', 'normal');
        remove_meta_box($key, 'dashboard', 'side');
    }
	
    $detected_widgets = array();
    if (isset($wp_meta_boxes['dashboard']['normal']['core']) && is_array($wp_meta_boxes['dashboard']['normal']['core'])) {
        foreach (array_keys($wp_meta_boxes['dashboard']['normal']['core']) as $name) {
            $detected_widgets[$name] = $wp_meta_boxes['dashboard']['normal']['core'][$name]['title'];
        }
    }
	
    if (isset($wp_meta_boxes['dashboard']['normal']['low']) && is_array($wp_meta_boxes['dashboard']['normal']['low'])) {
        foreach (array_keys($wp_meta_boxes['dashboard']['normal']['low']) as $name) {
            $detected_widgets[$name] = $wp_meta_boxes['dashboard']['normal']['low'][$name]['title'];
        }
    }

    if (isset($wp_meta_boxes['dashboard']['normal']['high']) && is_array($wp_meta_boxes['dashboard']['normal']['high'])) {
        foreach (array_keys($wp_meta_boxes['dashboard']['normal']['high']) as $name) {
            $detected_widgets[$name] = $wp_meta_boxes['dashboard']['normal']['high'][$name]['title'];
        }
    }

    if (isset($wp_meta_boxes['dashboard']['side']['core']) && is_array($wp_meta_boxes['dashboard']['side']['core'])) {
        foreach (array_keys($wp_meta_boxes['dashboard']['side']['core']) as $name) {
            $detected_widgets[$name] = $wp_meta_boxes['dashboard']['side']['core'][$name]['title'];
        }
    }

    if (isset($wp_meta_boxes['dashboard']['side']['low']) && is_array($wp_meta_boxes['dashboard']['side']['low'])) {
        foreach (array_keys($wp_meta_boxes['dashboard']['side']['low']) as $name) {
            $detected_widgets[$name] = $wp_meta_boxes['dashboard']['side']['low'][$name]['title'];
        }
    }

    if (isset($wp_meta_boxes['dashboard']['side']['high']) && is_array($wp_meta_boxes['dashboard']['side']['high'])) {
        foreach (array_keys($wp_meta_boxes['dashboard']['side']['high']) as $name) {
            $detected_widgets[$name] = $wp_meta_boxes['dashboard']['side']['high'][$name]['title'];
        }
    }


    update_option('oxo_all_active_dashboard_widgets', $detected_widgets);
	

}

	 /* ================== Admin Side Options======================= */
	 
//change default Login url

function aione_login_url( $login_url, $redirect ) {
	
    $aione_login_page = get_option('aione_login_page');
	if(isset($aione_login_page)){
		if(!empty($aione_login_page)){
			return get_permalink($aione_login_page);
		}
	}
}

//change default Register url

function aione_register_url( $register_url ) {
	
   $aione_register_page = get_option('aione_register_page');
	if(isset($aione_register_page)){
		if(!empty($aione_register_page)){
			return get_permalink($aione_register_page);
		}
	}
}

//change default lost password url
function aione_lostpassword_url() {
	
	$aione_forgot_password_page = get_option('aione_forgot_password_page');
	if(isset($aione_forgot_password_page)){
		if(!empty($aione_forgot_password_page)){
			return get_permalink($aione_forgot_password_page);
		}
	}
}

//change default redirect url after Login
function admin_login_redirect( $redirect_to, $request, $user ){
	
	$admin_login_redirect_page = get_option('admin_login_redirect_page');
	if(isset($admin_login_redirect_page)){
		if(!empty($admin_login_redirect_page)){
			return get_permalink($admin_login_redirect_page);
		} 
	}
}

function go_home(){
	$logout_redirect_page = get_option('logout_redirect_page');
	if(isset($logout_redirect_page)){
		if(!empty($logout_redirect_page)){
			wp_redirect(get_permalink($logout_redirect_page) );
			exit();
		}
	}
}

function oxo_register_redirect() {
	
	if(strpos($_SERVER['REQUEST_URI'], 'wp-login.php?action=lostpassword')){
		$aione_forgot_password_page = get_option('aione_forgot_password_page');
		if(isset($aione_forgot_password_page)){
			if(!empty($aione_forgot_password_page)){
				
			echo "<script>";
			echo 'window.location.assign("'.get_permalink($aione_forgot_password_page).'");';
			echo "</script>";
			
			}
		}
	}
	if(strpos($_SERVER['REQUEST_URI'], 'wp-login.php?action=register')){
		$aione_register_page = get_option('aione_register_page');
		
	if(isset($aione_register_page)){
		if(!empty($aione_register_page)){

		echo "<script>";
		echo 'window.location.assign("'.get_permalink($aione_register_page).'");';
		echo "</script>";
		}
	}
	} 
	
	if(strpos($_SERVER['REQUEST_URI'], 'wp-login.php')){
		$admin_login_redirect_page = get_option('admin_login_redirect_page');
		if(isset($admin_login_redirect_page)){
			if(!empty($admin_login_redirect_page)){
				echo "<script>";
				echo 'window.location.assign("'.get_permalink($admin_login_redirect_page).'");';
				echo "</script>";
		
			}
		}
	}
	/*
	if(strpos($_SERVER['REQUEST_URI'], 'wp-admin')){
		$admin_login_redirect_pagei = get_option('admin_login_redirect_page');
		if(isset($admin_login_redirect_pagei)){
			if(!empty($admin_login_redirect_pagei)){
				wp_redirect(get_permalink($admin_login_redirect_pagei) ); exit;
			}
		
		}
	} */
	
}
	function aione_app_builder_menu() {
	if ( isset( $_POST['action'] ) && $_POST['action'] == "save" ){
		$validation_key = $_POST['save_settings'];
		if ( ! isset( $validation_key )  || ! wp_verify_nonce( $validation_key, 'validation_key' ) ) {
			echo "Access denied.";
			exit;

		} else {
			if(isset( $_POST['filter'] ) && $_POST['filter'] == "login-tab"){
			$aione_login_page = $_POST['aione_login_page'];
			$admin_login_redirect_page = $_POST['admin_login_redirect_page'];

			update_option( 'aione_login_page', $aione_login_page );
			update_option( 'admin_login_redirect_page', $admin_login_redirect_page );
			}
			if(isset( $_POST['filter'] ) && $_POST['filter'] == "register-tab"){
			$aione_register_page = $_POST['aione_register_page'];
			$aione_registration_custom_field_groups = $_POST['aione_registration_custom_field_groups'];
			update_option( 'aione_register_page', $aione_register_page );
			update_option( 'aione_registration_custom_field_groups', $aione_registration_custom_field_groups );
			}
			if(isset( $_POST['filter'] ) && $_POST['filter'] == "lost-password-tab"){
			$aione_forgot_password_page = $_POST['aione_forgot_password_page'];
			update_option( 'aione_forgot_password_page', $aione_forgot_password_page );
			}
			if(isset( $_POST['filter'] ) && $_POST['filter'] == "logout-tab"){
			$logout_redirect_page = $_POST['logout_redirect_page'];
			update_option( 'logout_redirect_page', $logout_redirect_page );
			}
			
		   /* ================== Admin Side Options======================= */
			if(isset( $_POST['filter'] ) && $_POST['filter'] == "access-control-tab"){
				$menu[] = $_POST['menu'];
				$submenu[] = $_POST['submenu'];
				$oxouser = $_POST['usrole'];
				
				  $active = array();
                    foreach ((array) $_POST['active'] as $key => $value) {
                        if (!isset($active[$value])) {
                         $active[$value] = $value;
                        }
                    }
					
				if($oxouser == "administrator"){ 
				$submenu = "";
				$menu = "";
				}
				if($menu==""){
					$menu = "";
				}
				if($submenu==""){
					$submenu = "";
				}
				$oxoadminoptions = array(
                  'oxomenu' => $menu,
                  'oxosubmenu' => $submenu,
				  'offwidget' => $active
                );
			update_option( $oxouser.'_oxo_admin_options', $oxoadminoptions );
			}
			/* ================== Admin Side Options======================= */
			
		}
		
	}

		add_menu_page( 'Aione App Builder','Aione App Builder','manage_options','aione-builder', array( $this, 'aione_app_builder_menu' ), 'dashicons-arrow-right-alt', 29 );
		$page = 'add_submenu_page';
		 // Login Page
            $page('aione-builder','Login', 'Login', 'manage_options', 'aione-login', array($this,'login_tab'));
		 // Register Page
            $page('aione-builder','Register','Register','manage_options','aione-register', array( $this, 'register_tab' ));
		 // Logout Page
            $page('aione-builder','Logout','Logout','manage_options','aione-logout', array( $this, 'logout_tab' ));
		 // Lost Pass Page
            $page('aione-builder','Lost Password','Lost Password','manage_options','aione-lost-password', array( $this, 'lost_password_tab' ));
	  
	        $page('aione-builder','Access Control Setting','Access Control Setting','manage_options','access-control-setting', array( $this, 'access_control_tab' )); 
	  remove_submenu_page('aione-builder', 'aione-builder' );
	  
	}
	
	public function tabs(){
            $selected = isset ( $_GET['page'] ) ? $_GET['page'] : 'aione-login';
            $nonce    = wp_create_nonce( 'redux-support-hash' );
            ?>
			
            <input type="hidden" id="redux_support_nonce" value="<?php echo esc_attr($nonce); ?>"/>
            <h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo $selected == 'aione-login' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'aione-login' ), 'admin.php' ) ) ); ?>">
                    <?php esc_attr_e( "Login", 'redux-framework' ); ?> </a> 
				
				<a class="nav-tab <?php echo $selected == 'aione-register' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'aione-register' ), 'admin.php' ) ) ); ?>">
                    <?php esc_attr_e( 'Register', 'redux-framework' ); ?> </a>
				
				<a class="nav-tab <?php echo $selected == 'aione-logout' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'aione-logout' ), 'admin.php' ) ) ); ?>">
                    <?php esc_attr_e( 'Logout', 'redux-framework' ); ?> </a> 
					
				<a class="nav-tab <?php echo $selected == 'aione-lost-password' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'aione-lost-password' ), 'admin.php' ) ) ); ?>">
                    <?php _e( 'Lost Password', 'redux-framework' ); ?> </a>  
					
				<a class="nav-tab <?php echo $selected == 'access-control-setting' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'access-control-setting' ), 'admin.php' ) ) ); ?>">
                    <?php _e( 'Access Control Setting', 'redux-framework' ); ?> </a> 
					
            </h2>
        <?php
		
        }
			
		public function login_tab() {
				// Stupid hack for Wordpress alerts and warnings
				echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';
				$this->tabs();
				require_once plugin_dir_path( __FILE__ ) .'views/login-tab.php';
			}
		
        public function access_control_tab(){
	     echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';
		 $this->tabs();
         require_once plugin_dir_path( __FILE__ ) .'views/access-control-tab.php';
            }
			
        public function register_tab() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';
			$this->tabs();
            require_once plugin_dir_path( __FILE__ ) .'views/register-tab.php';

        }

        public function logout_tab() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';
			$this->tabs();
           require_once plugin_dir_path( __FILE__ ) .'views/logout-tab.php';

        }

        public function lost_password_tab() {
            // Stupid hack for Wordpress alerts and warnings
            echo '<div class="wrap" style="height:0;overflow:hidden;"><h2></h2></div>';
			$this->tabs();
            require_once plugin_dir_path( __FILE__ ) .'views/lost-password-tab.php';

        }
//**************************************************************************

//****************************************************************************
    
	public function enqueue_styles () {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/style.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
	} // End enqueue_styles ()

	public function enqueue_scripts () {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/custom' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts ()
	
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin-style.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()
	
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin-custom' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	
	
	public function load_localisation () {
		load_plugin_textdomain( 'aione-vcarousel', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	public function load_plugin_textdomain () {
	    $domain = 'aione-vcarousel';

	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );

	    load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()
	
	public static function instance ( $file = '', $version = '1.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance ()

	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone ()

	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup ()
	
	public function install () {
		$this->_log_version_number();
	} // End install ()
	
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
new Aione_App_Builder();