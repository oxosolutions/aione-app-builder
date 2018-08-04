<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       www.oxosolutions.com
 * @since      1.0.0
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/public
 * @author     OXO Solutions <contact@oxosolutions.com>
 */
class Aione_App_Builder_Public {

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
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aione_App_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aione_App_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aione-app-builder-public.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Aione_App_Builder_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Aione_App_Builder_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aione-app-builder-public.js', array( 'jquery' ), $this->version, false );

	}

	function aione_app_builder_login_fail_redirect_filter( $username ) {
	   $referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
	   $referrer = str_replace("?login=failed","",$referrer); //Resolves the problem of duplication of ?login=failed for each failed attempt

	   //$post = serialize($_POST);
	   // if there's a valid referrer, and it's not the default log-in screen
	   if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
	      wp_redirect( $referrer . '?login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
	      exit;
	   }
	}

	//change default Login url 
	function aione_app_builder_login_url( $login_url, $redirect ) {
		$aione_app_builder_login_page = get_option('aione_app_builder_login_page');
		/*if(isset($aione_app_builder_login_page)){
			if(!empty($aione_app_builder_login_page)){
				return get_permalink($aione_app_builder_login_page);
			} else {
				return $login_url;
			}
		}else {
			return $login_url;
		}*/
		if(isset($aione_app_builder_login_page) && !empty($aione_app_builder_login_page)){
			$login_url = add_query_arg( 'redirect_to', $redirect, $aione_app_builder_login_page );
		}
		return $login_url;		
	}

	//change default redirect url after Login
	function admin_login_redirect( $redirect_to, $request, $user ){		
		$admin_login_redirect_page = get_option('admin_login_redirect_page');
		if(isset($admin_login_redirect_page)){
			if(!empty($admin_login_redirect_page)){
				return get_permalink($admin_login_redirect_page);
			} else {
				return $redirect_to;
			}
		}else {
			return $redirect_to;
		}
	}
	//change default Register url
	function aione_app_builder_register_url( $register_url ) {
		$aione_app_builder_register_page = get_option('aione_app_builder_register_page');
		if(isset($aione_app_builder_register_page)){
			if(!empty($aione_app_builder_register_page)){
				return get_permalink($aione_app_builder_register_page);
			} else {
				return $register_url;
			}
		} else {
			return $register_url;
		}
	}
	//change default lost password url
	function aione_app_builder_lostpassword_url($lostpassword_url, $redirect) {
		$aione_app_builder_forgot_password_page = get_option('aione_app_builder_forgot_password_page');
		if(isset($aione_app_builder_forgot_password_page)){
			if(!empty($aione_app_builder_forgot_password_page)){
				return get_permalink($aione_app_builder_forgot_password_page);
			} else {
				return $lostpassword_url;
			}
		} else {
			return $lostpassword_url;
		}
	}
	// After logout redirect
	function logout_redirect(){
		$logout_redirect_page = get_option('logout_redirect_page');
		if(isset($logout_redirect_page)){
			if(!empty($logout_redirect_page)){
				wp_redirect(get_permalink($logout_redirect_page) );
				exit();
			}
		}
	}

	/**
	*
	*Shortcodes Functionality
	*
	*/

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
			$output .= '<div id="register_link" class="user-links register-link '.$class.'">';
			$output .= '<a href="'.wp_registration_url().'" title="' . $text . '">' . $text . '</a>';
			$output .= '</div>';
		} 
		return $output;
	} // End aione_app_builder_register_link_shortcode()

	public function aione_app_builder_reset_password_link_shortcode( $atts ) {
		extract( shortcode_atts(
				array(
					'class'           => '',
					'text'           => 'Reset Password'
				), $atts )
		);
		$output = "";
		
		if ( !is_user_logged_in() ) {
			$output .= '<div id="reset_password_link" class="user-links reset-password-link '.$class.'">';
			$output .= '<a href="'.wp_lostpassword_url().'" title="' . $text . '">' . $text . '</a>';
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

	public function aione_app_builder_id_shortcode( $attr, $content = null ) {
		global $post;
		$defaults = array(
			
		);
		extract( shortcode_atts( $defaults, $attr ) );
		$output = "";
		$output .= $post->ID;
		return $output;
	}

	public function aione_app_builder_login_shortcode( $atts, $content = null ) {
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
			'social_login'      => 'no',
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
			
			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

			// check if wordpress-social-login is activeted
			if ( $social_login == "yes" && is_plugin_active( 'wordpress-social-login/wp-social-login.php' ) ) {
			  //wordpress-social-login plugin is activated
			  
				$output .= '<div class="aione-social-login">';
				//$output .= do_action( 'wordpress_social_login' );
				$output .= do_shortcode( '[wordpress_social_login]' );
				$output .= '</div>';

				$output .= '<style>
				.aione-social-login .wp-social-login-provider-list {
					padding: 0;
				}
				.aione-social-login .wp-social-login-widget .wp-social-login-provider{
					color:#FFFFFF;
					background-color:#3b5998;
					padding: 0 20px;
					display: inline-block;
					font-size:18px;
					line-height:36px;
					font-weight:300;
					font-family:"Open Sans",Arial;
				}
				.aione-social-login .wp-social-login-widget .wp-social-login-provider:hover{
					color:#FFFFFF;
					background-color:#3b5998;
				} 
				</style>
				';
			  
			}
			
		} else {
			$output .= '<div class="center-align">';
			$output .= 'You are already logged in! ';
			$output .= '<a href="'.wp_logout_url().'" title="Logout" class="aione-common-button">Logout</a>';
			$output .= '</div>';
		}
		
		
		
		return $output;


	} // End aione_login_form_shortcode()

	public function aione_app_builder_register_shortcode( $atts, $content = null ) {
			// Attributes
			extract( shortcode_atts(
				array(
					'echo'           => false,
					'captcha'        => true,
					'form_id'        => 'aione_registration_form',
					'show_firstname' => 'yes',
					'show_lastname' 	=> 'yes',
					'label_firstname' => __( 'First Name' ),
					'label_lastname' => __( 'Last Name' ),
					'label_username' => __( 'Username' ),
					'label_email' => __( 'Email Address' ),
					'label_password' => __( 'Password' ),
					'label_password_again' => __( 'Password Again' ),
					'label_submit' => __( 'Register' ),
					'placeholder_firstname' => __( 'Enter Your First Name' ),
					'placeholder_laststname' => __( 'Enter Your Last Name' ),
					'placeholder_username' => __( 'Enter Your Username' ),
					'placeholder_email' => __( 'Enter Your Email Address' ),
					'role' => get_option('default_role'),
				), $atts )
			);

			$args = array(
					'echo'						=> $echo,
					'captcha'                   => $captcha,
					'form_id'					=> $form_id,
					'show_firstname'			=> $show_firstname,
					'show_lastname'				=> $show_lastname,
					'label_firstname'			=> $label_firstname,
					'label_lastname'			=> $label_lastname,
					'label_username'			=> $label_username,
					'label_email'				=> $label_email,
					'label_password'			=> $label_password,
					'label_password_again'		=> $label_password_again,
					'label_submit'				=> $label_submit,
					'placeholder_firstname'		=> $placeholder_firstname,
					'placeholder_laststname'	=> $placeholder_laststname,
					'placeholder_username'		=> $placeholder_username,
					'placeholder_email'			=> $placeholder_email,
					'role' 						=> $role,
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

					if( isset($_POST['action']) && $_POST['action'] == 'add_new'){
						$user_login		= $_POST["aione_user_login"];
						$user_email		= $_POST["aione_user_email"];
						$user_pass		= $_POST["aione_user_pass"];
						$pass_confirm 	= $_POST["aione_user_pass_confirm"];
						$user_first		= $_POST["aione_user_fname"];
						$user_last		= $_POST["aione_user_lname"];

						
						if($captcha == true){
							if (class_exists('ReallySimpleCaptcha'))  {
								$captcha_value= $_POST['captcha_value'];
								$prefix = $_POST['captcha_prefix'];
								$captcha_instance_check = new ReallySimpleCaptcha();
								$is_captcha_correct = $captcha_instance_check->check( $prefix, $captcha_value);
								
								if(!$is_captcha_correct){
									$errors[] = 'Wrong Captcha value';
								}
							}
						}
						
						// this is required for username checks
						if($user_email == '') {
							//empty email
							$errors[] = 'Email address field can not be empty.';
						} else {
							if(!is_email($user_email)) {
								//invalid email
								$errors[] = 'Email address you have entered is invalid. Enter a valid email address.';
							}
							if(email_exists($user_email)) {
								//Email address already registered
								$errors[] = 'Email already registered. You can reset your password instead.';
							}
						}
					
						if($user_login == '') {
							// empty username
						   $errors[] = 'Username cannot be empty. Please enter a username';
						} else {
							
							$pattern = '/^[a-z0-9]+$/';
							if(!preg_match($pattern, $user_login)){
								$errors[] = 'The username you have entered is invalid. Please enter at least 6 alphanumeric characters in lowercase. Special characters and white spaces are not allowed.'; 
							} else{	
								if(!validate_username($user_login)) {
									// invalid username
									$errors[] = 'Username you have entered is invalid. ';
								}
							}


							if(username_exists($user_login)) {
								// Username already registered
								$errors[] = 'Username already taken. Try something else.';
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
						if($user_first == '') {
							//Empty password
							$errors[] = 'First name can not be empry. Humans do have names.';
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
							global $wp_roles;
							$roles = wp_roles()->get_names();
							if(array_key_exists($role,$roles)){
								$user_role = $role;
							} else {
								$user_role = get_option('default_role');
							}
							
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
								if(isset($_POST['fields'])){
									$custom_fields = $_POST['fields'];
									foreach($custom_fields as $custom_field_key => $custom_field){
										update_field($custom_field_key , $custom_field, "user_".$new_user_id);
									}
								
								}
								
								$output .= 'Thank you for registering.';
						
								
							} else {
								$errors[] = 'Some error occurred. Please contact Administrator.';
							}
						} else {
							foreach($errors as $error){
								$output .=  '<div style="color:#cc0000;text-align:center;padding:10px">'.$error.'</div>';
							}
							//$output .= $this->aione_show_errors($errors);
							$output .= $this->aione_app_builder_user_registration_form($args);
						}
					} else {
						$output .= $this->aione_app_builder_user_registration_form($args);
					}
				} else {
					$output .= __('User registration is not enabled!');
				}
			} else {
					$output .= __('You are already logged in!');
			}
			return $output;
		} //END aione_app_builder_register_shortcode()

		public function aione_app_builder_user_registration_form($args) {
		if($args['captcha'] == true){
			if (class_exists('ReallySimpleCaptcha'))  {
				
				$captcha_instance = new ReallySimpleCaptcha();
				$captcha_instance->cleanup($minutes = 30);
					
				$captcha_instance->chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';	
				$captcha_instance->bg = array( 255, 255, 255 );
				$captcha_instance->fg = array( 21, 141, 197 );
				$captcha_instance->img_size = array( 205, 40 );
				$captcha_instance->base = array( 20, 30 );
				$captcha_instance->font_size = 22;
				$captcha_instance->char_length = 6;
				$captcha_instance->font_char_width = 28;
				//$upload_dir = wp_upload_dir();
				//$captcha_instance->tmp_dir = $upload_dir['basedir'].'/captcha/';
				
			}	
		}
		
			$html_before_fields = '';
			$html_before_fields .= '
			
			<form id="'.$args['form_id'].'" class="aione-registration-form aione-form form acf-form" action="'.get_permalink().'" method="post">
				<div class="postbox acf_postbox no_box">';

				if($args['show_firstname'] == 'yes'){

					$html_before_fields .= '<div class="aione-form-field field field-type-text">
						<p class="label"><label for="aione_user_fname">'.$args['label_firstname'].'<span class="required">*</span></label></p>
						<div class="acf-input-wrap"><input name="aione_user_fname" id="aione_user_fname" class="textbox large" type="text" placeholder="'.$args['placeholder_firstname'].'" value="" minlength="2" maxlength="200" required /></div>
					</div>';
				}

				if($args['show_lastname'] == 'yes'){

					$html_before_fields .= '<div class="aione-form-field field field-type-text">
						<p class="label"><label for="aione_user_lname">'.$args['label_lastname'].'</label></p>
						<div class="acf-input-wrap"><input name="aione_user_lname" id="aione_user_lname" class="textbox large" type="text" placeholder="'.$args['placeholder_laststname'].'" value=""/></div>
					</div>';
				}

				
				$html_before_fields .= '<div class="aione-form-field field field-type-text">
					<p class="label"><label for="aione_user_login">'.$args['label_username'].'<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_login" id="aione_user_login" class="textbox large required" type="text" placeholder="'.$args['placeholder_username'].'" value="" minlength="6" maxlength="50" required/></div>
				</div>
				<div class="aione-form-field field field-type-text">
					<p class="label"><label for="aione_user_email">'.$args['label_email'].'<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_email" id="aione_user_email" class="textbox large required" type="email" placeholder="'.$args['placeholder_email'].'" value="" required /></div>
				</div>

				<div class="aione-form-field field field-type-text">
					<p class="label"><label for="password">'.$args['label_password'].'<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_pass" id="password" class="textbox large required" type="password" minlength="6" required /></div>
				</div>

				<div class="aione-form-field field field-type-text">
					<p class="label"><label for="password_again">'.$args['label_password_again'].'<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="aione_user_pass_confirm" id="password_again" class="textbox large required" type="password" minlength="6" required/></div>
				</div>

			';
			if($args['captcha'] == true){
				if (class_exists('ReallySimpleCaptcha'))  {	
					$word = $captcha_instance->generate_random_word();
					$prefix = mt_rand();
					$image_name = $captcha_instance->generate_image( $prefix, $word );
					//$captcha_image_url =  $upload_dir['baseurl'].'/captcha/'.$image_name;
					//$captcha_image_url = plugins_url();
					$captcha_image_url =  plugin_dir_url(dirname(__FILE__))."library/really-simple-captcha/tmp/".$image_name;

					//$blog_template = intval($_GET['template']);
						
					$html_before_fields .= '<div class="aione-form-field field field-type-text">
							<p class="label"><label for="register_form_captcha_value">Captcha<span class="required">*</span></label></p>
							<div class="register_form_captcha_image">
							<img src="'.$captcha_image_url.'" />
							</div> 
							<div class="acf-input-wrap"><input name="captcha_value" id="register_form_captcha_value" type="text" placeholder="Enter Captcha Here" value="" class="textbox large required" >
							<input name="captcha_prefix" type="hidden" value="'.$prefix.'" >
							</div>
						
						</div>
						';
				}
			}
			$html_after_fields = '<div class="aione-form-field field">
				<input type="hidden" name="action" value="add_new">
				<input type="submit" value="'.$args['label_submit'].'">
			</div>
			<script>
				jQuery("#'.$args['form_id'].'").validate();
			</script>

			<style>
			.error{
				color:#cc0000;
			}
			.aione-registration-form p.label{
				margin-bottom:0;
			}
			.aione-registration-form .aione-form-field{
				margin-bottom:20px;
			}
			</style>
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

		public function aione_app_builder_reset_password_shortcode( $atts ) {
		$output = "";
		$errors = array();
		$success_messages = array();
		
		if( isset($action) && !empty($action) ){
			if( isset($_POST['email']) ){
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
		}
		
		$output .= '<form method="post" class="login-signup" id="login-signup" action="'.get_permalink().'">';
		$output .= '<p class="forgot-password-user">';
		$output .= '<label for="email">Your Email Address<span class="required">*</span></label>';
		$output .= '<input class="text-input field-long" name="email" type="email" id="email">';
		$output .= '</p>';
		$output .= '<p class="reset-password">';
		$output .= '<input name="reset-password" type="submit" class="field-long submit btn button-primary button application-button" value="Reset Password">';
		$output .= '<input name="action" type="hidden" value="'.$action.'">';
		$output .= '</p>';
		$output .= '</form>';
		
		return $output;
	}

	public function aione_app_builder_account_shortcode ($attr, $content = null){
		extract( shortcode_atts(
			array(
			'pages'    => 'yes', // yes/no
			'sub_pages'    => 'no', // yes/no
			'layout_menu'    => 'vertical',// horizontal/vertical
			'layout_content'    => 'box',// box/table/list
			), $attr )
		);

		
		$attr_menu = array(
			'pages'    => $pages, // yes/no
			'sub_pages'    => $sub_pages, // yes/no
			'layout'    => $layout_menu,// horizontal/vertical
		);

		$attr_content = array(
			'pages'    => $pages, // yes/no
			'sub_pages'    => $sub_pages, // yes/no
			'layout'    => $layout_content,// horizontal/vertical
		);
		$output = "";
		if(is_user_logged_in() ){
			$output .= $this->aione_app_builder_account_menu_shortcode($attr_menu);	
			$output .= $this->aione_app_builder_account_content_shortcode($attr_content);
		} else {
			$output .="";
		}
			
		return $output;
	}

	public function aione_app_builder_account_menu_shortcode ($attr, $content = null){
		// Attributes
		extract( shortcode_atts(
			array(
			'pages'    => 'yes', // yes/no
			'sub_pages'    => 'no', // yes/no
			'layout'    => 'vertical',// horizontal/vertical
			), $attr )
		);

		$output = "";
		if(is_user_logged_in() ) {
			$output .= '<div id="account_menu" class="account-menu '.$layout.'" >';
			$output .= '<ul class="account-menu-list" id="account_menu_list">';

			if($pages == 'yes'){
				$output .= '<li><a href="?action=account">Account</a></li>';
				$output .= '<li><a href="?action=profile">View Profile</a></li>';
				$output .= '<li><a href="?action=edit-profile">Edit Profile</a></li>';
				$output .= '<li><a href="?action=change-password">Change Password</a></li>';
			}

			if($sub_pages == 'yes'){
				$post_id = get_the_ID();
				$post_ancestors = get_ancestors( $post_id, 'page' );
				$post_parent    = end( $post_ancestors );
				if ( $post_parent ) {
					$children = wp_list_pages( sprintf( 'title_li=&child_of=%s&echo=0', $post_parent ) );
				} else {
					$children = wp_list_pages( sprintf( 'title_li=&child_of=%s&echo=0', $post_id ) );
				}
				if ( $children ) {
					$output .= $children;
				}
			}

			$output .= '</ul>';
			$output .= '</div>';
		} else {
			$output .= "";
		}
		return $output;
	}

	public function aione_app_builder_account_content_shortcode ($attr, $content = null){
		extract( shortcode_atts(
			array(
			'pages'    => 'yes', // yes/no
			'sub_pages'    => 'no', // yes/no
			'layout'    => 'box',// box/table/list
			), $attr )
		);

		$output = "";
		if(is_user_logged_in()) {
			$action = $_GET['action'];
			if(!isset($action)){
				$action = "account";
			}
			if($action == "account"){
				$output .='<div class="account-content-outer" id="account_content_outer">';
				$output .='<div class="account-content" id="account_content">';
				$output .='<ul class="account-items '.$layout.'">';
				if($pages == 'yes'){
					$output .='<li><a class="account-item" href="?action=profile">View Profile</a></li>';
					$output .='<li><a class="account-item" href="?action=edit-profile">Edit Profile</a></li>';
					$output .='<li><a class="account-item" href="?action=change-password">Change Password</a></li>';
				}
				if($sub_pages == 'yes'){
					$post_id = get_the_ID();
					$post_ancestors = get_ancestors( $post_id, 'page' );
					$post_parent    = end( $post_ancestors );
					if ( $post_parent ) {
						$children = wp_list_pages( sprintf( 'title_li=&child_of=%s&echo=0', $post_parent ) );
					} else {
						$children = wp_list_pages( sprintf( 'title_li=&child_of=%s&echo=0', $post_id ) );
					}
					if ( $children ) {
						$output .= $children;
					}

				}

				$output .='<div style="clear:both;"></div>';
				$output .='</ul>';
				$output .='	</div>';
				$output .='</div>';
			}	// Action = Account
			
			if($action == "profile"){
				$output .= $this->aione_app_builder_profile_shortcode();	
			} // Action = Profile
			
			if($action == "edit-profile"){
				$output .= $this->aione_app_builder_edit_profile_shortcode();	
			} // Action = Edit Profile
			
			if($action == "change-password"){
				$output .= $this->aione_app_builder_change_password_shortcode();	
			} // Action = change-password
		} else {
			$output .= "";
		}
		return $output;
	}

	public function aione_app_builder_profile_shortcode ($attr, $content = null){
		$defaults = array(
			
		);
		extract( shortcode_atts( $defaults, $attr ) );
		$output = "";
		if(is_user_logged_in() ) {
			$user = wp_get_current_user();
			$user_id = $user->ID;
			$username = $user->user_login;
			$user_roles = $user->roles;
			$value = get_user_meta($user_id);
			$action = $_GET['action'];
			if($action == "profile"){
				$output .='<div class="account-content-outer" id="account_content_outer">
					<h3 class="account-content-title-heading-center">'.$username.' Profile</h3>';
					$output .= '<ul class="account-content-profile" id="account_content_profile">
					<li><div class="user-detail-label">ID</div>
					<div class="user-detail-value">'.$user_id.'</div>
					<div class="oxo-clearfix"></div></li>
					<li><div class="user-detail-label">First Name</div>
					<div class="user-detail-value">'.$value['first_name'][0].'</div>
					<div class="oxo-clearfix"></div></li>
					<li><div class="user-detail-label">Last name</div>
					<div class="user-detail-value">'.$value['last_name'][0].'</div>
					<div class="oxo-clearfix"></div></li>
					<li><div class="user-detail-label">Email</div>
					<div class="user-detail-value">'.$user->user_email.'</div>
					<div class="oxo-clearfix"></div></li>
					';
					
					$field_groups = get_option('aione_app_builder_registration_custom_field_groups');
					if(!is_array($field_groups)){
						$field_groups = array($field_groups);
					}
					
					 foreach($field_groups as $field_group_key => $field_group){
						$fields = apply_filters('acf/field_group/get_fields',array(), $field_group);
						
						foreach($fields as $fields_key => $field){
							$field_key = $field['key'];
							$field_data = get_field($field_key , "user_".$user_id);
							if(is_array($field_data)){
								$field_data = implode(",",$field_data);
							}
							$output .= '<li><div class="user-detail-label">'.$field['label'].'</div>';
							$output .= '<div class="user-detail-value">'.$field_data.'</div>
							<div class="oxo-clearfix"></div></li>';
						}
						
						
					} 
					
					$output .= '</ul>
					</div>';
			}
		} else {
			$output .= "";
		}
		
		return $output;
	}

	public function aione_app_builder_edit_profile_shortcode ($attr, $content = null){
		$defaults = array(
			
		);
		extract( shortcode_atts( $defaults, $attr ) );
		$output = "";
		if(is_user_logged_in() ){
			$user = wp_get_current_user();
			$user_id = $user->ID;
			$username = $user->user_login;
			$value = get_user_meta($user_id);
			
			$action = $_GET['action'];
			
			if($action == "edit-profile") {
				
				if(isset($_POST['update_profile'])  && $_POST['update_profile'] == 'update_profile'){
					$first_name = $_POST['aione_user_fname'];
					$last_name = $_POST['aione_user_lname'];
					$custom_fields = $_POST['fields'];
					wp_update_user( array( 'ID' => $user_id, 'first_name' => $first_name, 'last_name' => $last_name  ) );
					foreach($custom_fields as $custom_field_key => $custom_field){
						update_field($custom_field_key , $custom_field, "user_".$user_id);
					}
					
				}
				$output .= '<h3 class="account-content-title-heading-center">Edit Profile</h3>';
				$html_before_fields = "";
				$html_before_fields .= '
					<form id="aione_edit_profile_form" class="aione-edit-profile-form aione-form form acf-form" action="'.get_permalink().'?action=edit-profile" method="post">
				';
				$html_before_fields .= '<div class="aione-form-field field field-type-text">
							<p class="label"><label for="aione_user_fname">First Name</label></p>
							<div class="acf-input-wrap"><input name="aione_user_fname" id="aione_user_fname" class="textbox large" type="text" placeholder="" value="'.$value['first_name'][0].'"/></div>
						</div>';
				$html_before_fields .= '<div class="aione-form-field field field-type-text">
							<p class="label"><label for="aione_user_lname">Last Name</label></p>
							<div class="acf-input-wrap"><input name="aione_user_lname" id="aione_user_lname" class="textbox large" type="text" placeholder="" value="'.$value['last_name'][0].'"/></div>
						</div>';		
				
				$html_after_fields = '<div class="aione-form-field field">
					<input type="hidden" name="update_profile" value="update_profile">
					<input type="submit" value="Update">
				</div>
				';
				
				$field_groups = get_option('aione_app_builder_registration_custom_field_groups');
				if(!is_array($field_groups)){
					$field_groups = array($field_groups);
				}
				$options = array(
					'post_id'	            => 'user_'.$user_id,
					'form'                  => false,
					'field_groups'          => $field_groups,
					'post_title'            => false,
					'post_content'          => false,
					'html_before_fields'    => $html_before_fields,
					'html_after_fields'     => $html_after_fields,
					'instruction_placement' => 'field',
					'submit_value'	        => 'Submit',
					'updated_message'	    => 'Updated Successfully',
				);

				ob_start();
				acf_form($options);
				$output .= ob_get_contents();
				ob_end_clean();
			}
		} else {
			$output .= "";
		}	
		return $output;
	}

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
		$output .= '<h3 class="account-content-title-heading-center">Change Password</h3>';
		$output .= '<form method="post" class="aione-change-password-form aione-form form acf-form" id="aione-change-password-form" action="">';
		$output .= '<div class="aione-form-field field field-type-text">
					<p class="label"><label for="current_pass">Current Password<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="current_pass" id="current_pass" class="text-input field-long" type="password" /></div>
				</div>';
		$output .= '<div class="aione-form-field field field-type-text">
					<p class="label"><label for="pass1">New Password<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="pass1" id="pass1" class="text-input field-long" type="password" /></div>
				</div>';
		$output .= '<div class="aione-form-field field field-type-text">
					<p class="label"><label for="pass2">New Password<span class="required">*</span></label></p>
					<div class="acf-input-wrap"><input name="pass2" id="pass2" class="text-input field-long" type="password" /></div>
				</div>';
		
		$output .= '<input name="updateuser" type="submit" id="updateuser" class="field-long submit btn button-primary button application-button" value="Change Password">';
		$output .= '<input name="action" type="hidden" id="action" value="changepassword">';
		$output .= '</form>';
	   }
		return $output;
	} // END aione_app_builder_change_password_shortcode()

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

	public function aione_app_builder_users_shortcode($atts) {
		// Attributes
		extract( shortcode_atts(
			array(
				'site' =>  $GLOBALS['blog_id'],
				'style' => 'table',
				'columns' => 'Username',
				'fields' => 'user_login',
				'role' => '',
				'roles' => '',
			), $atts )
		);
		
		$output = "";

		$roles = explode(",", $roles);
		$args = array(
			'blog_id'      => $site,
			'role'         => $role,
			'role__in'     => $roles,
			'role__not_in' => array(),
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'date_query'   => array(),        
			'include'      => array(),
			'exclude'      => array(),
			'orderby'      => 'login',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '',
			'count_total'  => false,
			'fields'       => 'all',
			'who'          => ''
		 );
		$users = get_users( $args );
		
		if($style == 'table'){
			
			$columns = explode("|", $columns); 
			$fields = explode("|", $fields); 
			
			$output .= '<div class="table-1">';
			$output .= '<table width="100%">';
			$output .= '<thead>';
			$output .= '<tr>';
			
			foreach ( $columns as $column ) {
				$output .= '<th>'.$column.'</th>';
			}
			
			$output .= '</tr>';
			$output .= '</thead>';
			$output .= '<tbody>';
			
			// Array of WP_User objects.
			foreach ( $users as $user ) {
				
				$user_id = $user->ID;
			
				$output .= '<tr>';
				foreach ( $fields as $field ) {
					if( $field == 'status'){
						$output .= '<td>' . esc_html( $user->$field ) . '</td>';
					} elseif(empty($field)){
						$output .= '<td></td>';
					}elseif (preg_match("~\{\{\s*(.*?)\s*\}\}~", $field)) {
						$field = str_replace("{","",$field);
						$field = str_replace("}","",$field);
						$custom_field = get_user_meta( $user_id, $field, true ); 
						$output .= '<td>' . $custom_field . '</td>';
					}else {
						$output .= '<td>' . esc_html( $user->$field ) . '</td>';
					}
					
				}
				$output .= '</tr>';
			}
			
			$output .= '</tbody>';
			$output .= '</table>';
			$output .= '</div>';
			
		} else{ 
			$count = 1;
			// Array of WP_User objects.
			foreach ( $users as $user ) {
				$output .= '<br><span>'.$count.'. ' . esc_html( $user->user_login ) . '</span>';
				$count++;
			}
		}
		return $output;
		
	} // END aione_app_builder_users_shortcode

	public function aione_app_builder_user_shortcode( $atts ) {
		
		$user = wp_get_current_user();
		$userid = $user->ID;

		// Attributes
		extract( shortcode_atts(
			array(
				'user_id' => $userid, // ID of user
				'field' => 'user_login', //key of field and custom field to be dispayed
				'field_type' => 'field', //field/meta
			), $atts )
		);
		
		$user = get_user_by('id', $user_id);

		/*echo "<pre>";
		print_r($user);
		echo "</pre>";*/

		$output = "";
		if($field_type == 'field'){
			if($field == 'user_pass'){
				$output .= '';
			} else {
				$output .= $user->$field;
			}			
		} else {
			$custom_field = get_user_meta( $user_id, $field, true ); 
			$output .= '<td>' . $custom_field . '</td>';
		}
		
		return $output;
		
	} // END aione_app_builder_user_shortcode

	function aione_app_builder_welcome_shortcode($atts){
		$user = wp_get_current_user();
		$userid = $user->ID;

		// Attributes
		extract( shortcode_atts(
			array(
				'class' => '', 
				'id' => '', 
				'user_id' => $userid, // ID of user
			), $atts )
		);
		
		$user = get_user_by('id', $user_id);
		/*echo "<pre>";
		print_r($user);
		echo "</pre>";*/

		$output = "<span>Welcome </span>";

		$displayname = $user->user_displayname;
		if(empty($displayname) || $displayname == ""){
			$first_name = get_user_meta( $user_id, "first_name", true );
			if(empty($first_name) || $first_name == ""){
				$nicename = $user->user_nicename;
				if(empty($nicename) || $nicename == ""){
					$user_login = $user->user_login;
					$output .= "<span>".$user_login."</span>";
				} else {
					$output .= "<span>".$nicename."</span>";
				}
			} else {
				$output .= "<span>".$first_name."</span>";
			}
		} else {
			$output .= "<span>".$displayname."</span>";
		}
		
		return $output;
	} //END aione_app_builder_welcome_shortcode

}
