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


// Use the REST API Client to make requests to the Twilio REST API
use Twilio\Rest\Client;
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

		add_action('wp_head', array($this, 'getting_ajaxurl') );
		add_action('wp_ajax_export', array($this, 'export') );
		add_action('wp_ajax_nopriv_export', array($this, 'export') );
		add_action('wp_ajax_upload', array($this, 'upload_callback') );
		add_action('wp_ajax_nopriv_upload', array($this, 'upload_callback') );
		add_action('wp_ajax_import', array($this, 'import') );
		add_action('wp_ajax_nopriv_import', array($this, 'import') );

		add_filter('acf/update_value/name=featured_image', array($this, 'acf_set_featured_image'), 10, 3 );

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

	function getting_ajaxurl() {

	   	echo '<script type="text/javascript">
	           	var ajaxurl = "' . admin_url('admin-ajax.php') . '";
         	</script>';
	}


	function acf_set_featured_image( $value, $post_id, $field ) {

		

	    if( $value != '' ) {
	        //Add the value which is the image ID to the _thumbnail_id meta data for the current post
	        add_post_meta( $post_id, '_thumbnail_id', $value );
	    }

	    return $value;
	}


	function aione_app_builder_login_fail_redirect_filter( $username ) {
	   	$referrer = $_SERVER['HTTP_REFERER'];  // where did the post submission come from?
	   	$referrer = str_replace("?login=failed","",$referrer ); //Resolves the problem of duplication of ?login=failed for each failed attempt

	   	//$post = serialize($_POST );
	   	// if there's a valid referrer, and it's not the default log-in screen
	   	if ( !empty($referrer) && !strstr($referrer,'wp-login') && !strstr($referrer,'wp-admin') ) {
	   		$referrer = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $query = parse_url($referrer, PHP_URL_QUERY);

            // Returns a string if the URL has parameters or NULL if not
            if ($query) {
                $referrer .= '&';
            } else {
                $referrer .= '?';
            } 
	      	wp_redirect( $referrer . 'login=failed' );  // let's append some information (login=failed) to the URL for the theme to use
	      	exit;
	  	}
	}

	//change default Login url 
	function aione_app_builder_login_url( $login_url, $redirect ) {
		$aione_app_builder_login_page = get_option('aione_app_builder_login_page' );
		/*if( isset($aione_app_builder_login_page) ) {
			if( !empty($aione_app_builder_login_page) ) {
				return get_permalink($aione_app_builder_login_page );
			} else {
				return $login_url;
			}
		}else {
			return $login_url;
		}*/
		if( isset($aione_app_builder_login_page) && !empty($aione_app_builder_login_page) ) {
			$login_page = get_permalink($aione_app_builder_login_page );
			$login_url = add_query_arg( 'redirect_to', $redirect, $login_page );
		}
		return $login_url;		
	}

	//change default redirect url after Login
	function admin_login_redirect( $redirect_to, $request, $user ) {		
		$admin_login_redirect_page = get_option('admin_login_redirect_page' );
		if( isset($admin_login_redirect_page) ) {
			if( !empty($admin_login_redirect_page) ) {
				return get_permalink($admin_login_redirect_page );
			} else {
				return $redirect_to;
			}
		}else {
			return $redirect_to;
		}
	}


	function send_sms($user, $otp_number ) {

		$login_page_otp_mobile_field = get_option('login_page_otp_mobile_field' );
		$mobile_number = get_user_meta( $user->ID, $login_page_otp_mobile_field, true );
		
		$sms_service_provider = get_option('sms_service_provider' );

		if( $sms_service_provider == "twillio" ) {
			$twillio_sms_service_provider_key = get_option('twillio_sms_service_provider_key' );
			$twillio_sms_service_provider_secret = get_option('twillio_sms_service_provider_secret' );
			$twillio_sms_service_provider_phone_number = get_option('twillio_sms_service_provider_phone_number' );
			$client = new Client($twillio_sms_service_provider_key, $twillio_sms_service_provider_secret );

			$message = $client->messages->create($mobile_number, // to
				array(
					"body" => "Your Login confirmation code for ".get_bloginfo( 'name' )." is ".$otp_number,
					//"from" => "+17076634858"
					"from" => $twillio_sms_service_provider_phone_number
				)
			 );
		}

		if( $sms_service_provider == "msgclub" ) {
			$authKey = get_option('msgclub_sms_service_provider_key' );
			$name = "DARLIC";
			$message = "Your Login confirmation code for ".get_bloginfo( 'name' )." is ".$otp_number;;
			$mobileNumber= $mobile_number;
			$email="";
			$senderId="DARLIC";
			//$serverUrl="msg.msgclub.net";
			$serverUrl="mysms.msgclub.net";
			$route="1";
			echo $this->sendsmsGET($mobile_number,$senderId,$route,$message,$serverUrl,$authKey );	
		}
	}

	function sendsmsGET($mobileNumber,$senderId,$routeId,$message,$serverUrl,$authKey ) {

      	$route = "default";
		$getData = 'mobileNos='.$mobileNumber.'&message='.urlencode($message).'&senderId='.$senderId.'&routeId='.$routeId;

		$url="http://".$serverUrl."/rest/services/sendSMS/sendGroupSms?AUTH_KEY=".$authKey."&".$getData;
		
		$ch = curl_init( );

		   curl_setopt_array($ch, array(

		       CURLOPT_URL => $url,

		       CURLOPT_RETURNTRANSFER => true,

		       CURLOPT_SSL_VERIFYHOST => 0,

		       CURLOPT_SSL_VERIFYPEER => 0

		   ) );
		$output = curl_exec($ch );
		
		if( curl_errno($ch) ) {
		    echo 'error:' . curl_error($ch );
		}
		curl_close($ch );
		return $output;
	}

	function calculate_time_difference($date_time ) {
		$start_date = new DateTime($date_time );
		$since_start = $start_date->diff(new DateTime(date("Y-m-d h:i:s")) );

		return $since_start->i; //return Minutes
	}

	function validate_authentication( $user, $generated_otp_number, $entered_otp_number, $generated_otp_number_date_time ) {
		
		$time_difference = $this->calculate_time_difference($generated_otp_number_date_time );

		if( ($time_difference)<10 ) {
			if( $generated_otp_number == $entered_otp_number ) {
				return true;
			}else{
				return false;
			}	
		}else{
			return false;
		}

		// return true;
		
	}

	function send_otp_mail( $user, $otp_number ) {
		// echo("<script>console.log('PHP: ".$admin_email."' );</script>" );
		$admin_email = get_option('admin_email' );

		$from = $admin_email;
		$to = $user->user_email;
		$subject = "Login OTP";
		$message = "";
		$message .= '-------------------';
		$message .= '<br/>Time: '.gmdate("M d Y H:i:s" );
		$message .= '<br/>IP Address: '.$_SERVER['REMOTE_ADDR'];
		$message .= '<br/>Browser: '.$_SERVER['HTTP_USER_AGENT'];
		$message .= '<br/>-------------------<br/><br/>';
		$message .= 'To protect your account at <strong>'.home_url(). '</strong>, we have blocked the login attempt. If this was an authorized login, please provide the below token on the challenge page:';
		$message .= "Your 6 Digit OTP Number is :  <h2>".$otp_number."</h2><br/>";
		$message .= 'If this was not an authorized login, please change your password by going here:<br/>';
		$message .= '<a href="'.home_url().'" target="_blank">'.home_url().'</a>';
		$headers = "From:" . $from;

		// To send HTML mail, the Content-type header must be set
		$headers  = 'MIME-Version: 1.0' . "\r\n";
		$headers .= 'Content-type: text/html; charset=UTF-8' . "\r\n";

		wp_mail($to,$subject,$message, $headers );
	}


	function create_login_nonce( $user_id ) {
		$login_nonce               = array( );
		try {
			$login_nonce['key'] = bin2hex( random_bytes( 32 ) );
		} catch (Exception $ex) {
			$login_nonce['key'] = wp_hash( $user_id . mt_rand() . microtime(), 'nonce' );
		}
		$login_nonce['expiration'] = time() ;

		if ( ! update_user_meta( $user_id, '_two_factor_nonce', $login_nonce ) ) {
			return false;
		}

		return $login_nonce;
	}
	function delete_login_nonce( $user_id ) {
		return delete_user_meta( $user_id, '_two_factor_nonce' );
	}
	function my_added_login_field( ) {
		if ( get_option('enable_two_factor_auth') == 'yes' ) {
			if( get_option('two_factor_auth')=='user_can_select' ) {
				?>
				<p>
					<label for="my_extra_field">Select Authentication Method</label>
					<div id="user_selection">
						<select name="auth_method" id="auth_method">
							<option value="email">Email</option>
							<option value="mobile">Mobile</option>
							<option value="none">None</option>
						</select> 
					</div>
				</p>
				<?php
			}
		}
		?>
		
		<?php
	}
	function my_added_login_field_frontend( ) { 
		$output = ""; 
		if ( get_option('enable_two_factor_auth') == 'yes' ) {
			if( get_option('two_factor_auth')=='user_can_select' ) {
				$output .='
				<div>
					<label for="my_extra_field">Select Authentication Method</label>
					<div id="user_selection">
						<select name="auth_method" id="auth_method">
							<option value="email">Email</option>
							<option value="mobile">Mobile</option>
							<option value="none">None</option>
						</select> 
					</div>
				</div>
				';
			}
		}
		return $output;
	}
	function my_login_message() {
		$return_value = '';
		if( $_REQUEST['error'] == 'authmethod' ) {
			$return_value =  '<div id="login_error"><strong>You are not authorized to skip authentication method. Please select any authentication method other than "None"</stong></div>';
		}
		return $return_value;
	}

	function wp_login( $user_login, $user ) {
		$enable_two_factor_auth = get_option('enable_two_factor_auth' );
		if ( empty( $enable_two_factor_auth ) || $enable_two_factor_auth == "no" ) {
			return;
		}

		wp_clear_auth_cookie( );

		$login_page_tfa_role = get_option('login_page_tfa_role',array() );
		$otp_page = array( );
		foreach ($login_page_tfa_role as $value) {
			if( in_array($value, (array) $user->roles) ) {
				$otp_page[] = "true";
			}
		}
		if( in_array("true", $otp_page) ) { 
			$this->show_two_factor_login( $user );
			exit;
		} else { 
			wp_set_auth_cookie($user->ID );
    		return;
		}

		//$this->show_two_factor_login( $user );
		//exit;
	}

	function show_two_factor_login( $user ) {
		if ( ! $user ) {
			$user = wp_get_current_user( );
		}

		$login_nonce = $this->create_login_nonce( $user->ID );
		if ( ! $login_nonce ) {
			wp_die( esc_html__( 'Failed to create a login nonce.', 'aione-app-builder' ) );
		}

		$admin_login_redirect_page = get_option('admin_login_redirect_page' );
		if( isset($admin_login_redirect_page) ) {
			if( !empty($admin_login_redirect_page) ) {
				$redirect_to = get_permalink($admin_login_redirect_page );
			} else {
				$redirect_to = admin_url( );
			}
		}else {
			$redirect_to = admin_url( );
		}


		$this->login_html( $user, $login_nonce['key'], $redirect_to );
	}

	function login_html( $user, $login_nonce, $redirect_to, $error_msg = '', $provider='', $generated_otp_number='', $generated_otp_time='' ) {
		//$wp_login_url = wp_login_url( );
		$rememberme = 0;
		if ( isset( $_REQUEST['rememberme'] ) && $_REQUEST['rememberme'] ) {
			$rememberme = 1;
		}
		if ( $GLOBALS['pagenow'] === 'wp-login.php' ) {
			login_header( );
		} else {			
			do_action( 'login_header' );
		}
		

		if ( ! empty( $error_msg ) ) {
			echo '<div id="login_error"><strong>' . esc_html( $error_msg ) . '</strong><br /></div>';
		}
		
		?>

		<?php
		if( empty($generated_otp_number) ) {
			$generated_otp_number = (rand(100000, 999999) );
		}
		if( empty( $generated_otp_time ) ) {
			$generated_otp_time = date("Y-m-d h:i:s" );
		}
		update_user_meta($user->ID,"wp-generated-otp-number",$generated_otp_number );
		?>

		<?php 
		if( get_option('two_factor_auth')=='user_can_select' ) {
			$method = $_REQUEST['auth_method'];
		} else {
			$method ="";
		} 

		if( $user ) {
			
				$this->custom_authentication_page( $user, $generated_otp_number, $generated_otp_time ,$rememberme,$login_nonce, $redirect_to ,$method );
			
		}
		
		do_action( 'login_footer' ); ?>
		<div class="clear"></div>
	
	<?php
	}

	function custom_authentication_page($user, $generated_otp_number, $generated_otp_time,$rememberme ,$login_nonce, $redirect_to,$method ) {
		?>
		<div class="aione-tfa-bg"></div>
		<div class="aione-tfa">
		<form class="aione-tfa-form-container" name="validate_2fa_form" id="" action="<?php echo esc_url( set_url_scheme( add_query_arg( 'action', 'validate_2fa', '' ), 'login_post' ) ); ?>" method="post" autocomplete="off">	
			<input type="hidden" name="wp-auth-id"    id="wp-auth-id"    value="<?php echo esc_attr( $user->ID ); ?>" />
			<input type="hidden" name="wp-auth-nonce" id="wp-auth-nonce" value="<?php echo esc_attr( $login_nonce ); ?>" />
			<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>" />
			<input type="hidden" name="rememberme"    id="rememberme"    value="<?php echo esc_attr( $rememberme ); ?>" />
			<input type="hidden" name="method"    id="method"    value="<?php echo esc_attr( $method ); ?>" />
			<?php 
			$this->authentication_page( $user, $generated_otp_number, $generated_otp_time,$method );
			?>
		</form>
		</div>
		<style>
		#aione_wrapper {
			opacity: 0.5;
		}
		.aione-tfa {
		  position: absolute;
		  top: 20%;
		  width: 60%;
		  right: 25%;
		  border: 3px solid #f1f1f1;
		  z-index: 99;
		  text-align: center;
		}
		.aione-tfa-bg{
			display: block;
			position: absolute;
			top:0;
			right: 0;
			bottom: 0;
			left: 0;
			z-index: 98;
			background-color: rgba(0,0,0,0.6 );
		}
		.aione-tfa-form-container {
		  padding: 10px;
		  background-color: white;
		}
		</style>
		<?php 
	}

	function authentication_page( $user, $generated_otp_number, $generated_otp_time='',$method ) {
		require_once( ABSPATH .  '/wp-admin/includes/template.php' );

		?>
		<h3 class="send-otp-title"><?php esc_html_e( 'Authentication', 'aione-app-builder' ); ?></h3>
		
		
		<?php
		$generated_otp_number = get_user_meta($user->ID,"wp-generated-otp-number",true );
		
		if( get_option('two_factor_auth')=='email' ) {
			$this->send_otp_mail( $user,$generated_otp_number );
			?>
			<p>You will receive OTP code on the Email associate to your account</p>
			<?php
		}
		if( get_option('two_factor_auth')=='mobile' ) {
			$this->send_sms( $user,$generated_otp_number );
			?>
			<p>You will receive OTP code on the Mobile Number associate to your account</p>
			<?php
		}
		if( get_option('two_factor_auth')=='both' ) {
			$this->send_otp_mail( $user,$generated_otp_number );
			$this->send_sms( $user,$generated_otp_number );
			?>
			<p>You will receive OTP code on Email as well as Mobile Number associate to your account</p>
			<?php
		}
		if( get_option('two_factor_auth')=='user_can_select' ) {
			if( $method == 'email' ) {
				$this->send_otp_mail( $user,$generated_otp_number );
				?>
				<p>You will receive OTP code on the Email associate to your account</p>
				<?php
			}
			if( $method == 'mobile' ) {
				$this->send_sms( $user,$generated_otp_number );
				?>
				<p>You will receive OTP code on the Mobile Number associate to your account</p>
				<?php
			}
		}
		
		?>
		<div id="otp_box">
			<p class="send-otp-label"><?php esc_html_e( 'Enter 6 digit OTP code', 'aione-app-builder' ); ?></p>
			<p> 
				<input type="text" maxlength="6" placeholder="######" name="wp-entered-otp-number" id="wp-entered-otp-number" >

				<input type="hidden" maxlength="6" name="wp-generated-otp-number-date-time" id="wp-generated-otp-number-date-time" value="<?php echo $generated_otp_time;?>">
			</p>
			<?php
			
			submit_button( __( 'Proceed to login', 'aione-app-builder' ) );
			?>
		</div>
		<p id="backtoblog">
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="Are you lost?"><?php echo sprintf( '&larr; Back to %s', get_bloginfo( 'title', 'display' ) ); ?></a>
		</p>
		<?php
		if( $this->calculate_time_difference($generated_otp_time)>10 ) {
			submit_button( $text = 'Re-send OTP', $type = 'primary', $name = 're_send_otp', $wrap = true, $other_attributes = null );
		}
		
	}

	function login_form_validate_2fa() { 
		if ( ! isset( $_POST['wp-auth-id'], $_POST['wp-auth-nonce'] ) ) {
			return;
		}

		$user = get_userdata( $_POST['wp-auth-id'] );

		$generated_otp_number = get_user_meta($user->ID,"wp-generated-otp-number",true );
		$entered_otp_number=$_POST['wp-entered-otp-number'];
		$generated_otp_number_date_time=$_POST['wp-generated-otp-number-date-time'];
		
		if ( ! $user ) {
			return;
		}

			//re-send button Clicked
		if( isset($_POST['re_send_otp']) ) {
			$login_nonce = $this->create_login_nonce( $user->ID );
			$this->login_html( $user, $login_nonce['key'], $_REQUEST['redirect_to'] );
			exit;
		}
		 
		if ( true !== $this->validate_authentication( $user, $generated_otp_number, $entered_otp_number, $generated_otp_number_date_time ) ) { 
			do_action( 'wp_login_failed', $user->user_login );

			$login_nonce = $this->create_login_nonce( $user->ID );
			if ( ! $login_nonce ) {
				wp_die( esc_html__( 'Failed to create a login nonce.', 'aione-app-builder' ) );
			}

			$this->login_html( $user, $login_nonce['key'], $_REQUEST['redirect_to'], esc_html__( 'ERROR: Invalid verification code.', 'aione-app-builder' ), $provider, $generated_otp_number, $generated_otp_number_date_time );
			exit;
		}
		
		$rememberme = false;
		if ( isset( $_REQUEST['rememberme'] ) && $_REQUEST['rememberme'] ) {
			$rememberme = true;
		}

		wp_set_auth_cookie( $user->ID, $rememberme );

		$redirect_to = apply_filters( 'login_redirect', $_REQUEST['redirect_to'], $_REQUEST['redirect_to'], $user );
		wp_safe_redirect( $redirect_to );

		exit;
	}


	
	//change default Register url
	function aione_app_builder_register_url( $register_url ) {

		$aione_app_builder_register_page = get_option('aione_app_builder_register_page' );
		if( isset($aione_app_builder_register_page) ) {
			if( !empty($aione_app_builder_register_page) ) {
				return get_permalink($aione_app_builder_register_page );
			} else {
				return $register_url;
			}
		} else {
			return $register_url;
		}

	}


	//change default lost password url
	function aione_app_builder_lostpassword_url( $lostpassword_url, $redirect ) {
		$aione_app_builder_forgot_password_page = get_option('aione_app_builder_forgot_password_page' );
		if( isset($aione_app_builder_forgot_password_page) ) {
			if( !empty($aione_app_builder_forgot_password_page) ) {
				return get_permalink($aione_app_builder_forgot_password_page );
			} else {
				return $lostpassword_url;
			}
		} else {
			return $lostpassword_url;
		}
	}


	// After logout redirect
	function logout_redirect( ) {
		$logout_redirect_page = get_option('logout_redirect_page' );
		if( isset($logout_redirect_page) ) {
			if( !empty($logout_redirect_page) ) {
				wp_redirect(get_permalink($logout_redirect_page) );
				exit( );
			}
		}
	}


	/**
	*
	*Shortcodes Functionality
	*
	*/




	/**
	 * Display link to login page
	 *
	 * Function for shortcode [login_link]. Returns html/link/url to display button/link to login page 
	 *
	 * @type 	shortcode
	 * @since 	1.0.0.0
	 * @access 	public
	 * 
	 * @example [login_link id="custom_login_link" class="custom-login-link" text="Login" return="link"]
	 * 
	 * @param 	id 		login_link 	text 	
	 * @param 	class 	null 		text 	
	 * @param 	text 	Login 		text 	
	 * @param 	return 	html 		text 	html/link/url
	 * 
	 * @return 	html
	 */

	public function aione_app_builder_login_link_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'id'		=> 'login_link',
				'class'		=> '',
				'text'		=> 'Login',
				'return'	=> 'html', //html/link/url
			), $atts, 'login_link' );

		$atts = $this->clean_shortcode_parameters( $atts );


		$output = "";

		if ( !is_user_logged_in() ) {

			if( $atts['return'] == 'html' ) {
				$output .= '<div id="'.$atts['id'].'" class="user-links login-link '.$atts['class'].'">';
			}

			if( $atts['return'] == 'url' ) {
				$output .= wp_login_url( );
			} else {
				$output .= '<a href="'.wp_login_url().'" title="' . $atts['text'] . '">' . $atts['text'] . '</a>';
			}

			if( $atts['return'] == 'html' ) {
				$output .= '</div>';
			}

		} 

		return $output;

	} // End aione_app_builder_login_link_shortcode()



	/**
	 * Display link to Sign up page
	 *
	 * Function for shortcode [register_link]. Returns html/link/url to display button/link to Sign up page 
	 *
	 * @type 	shortcode
	 * @since 	1.0.0.0
	 * @access 	public
	 * 
	 * @example [register_link id="register_link" class="register-link" text="Register" return="link"]
	 * 
	 * @param 	id 		register_link 	text 	
	 * @param 	class 	null 			text 	
	 * @param 	text 	Sign up 		text 	
	 * @param 	return 	html 			text 	html/link/url
	 * 
	 * @return 	html
	 */

	public function aione_app_builder_register_link_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'id'		=> 'register_link',
				'class'		=> '',
				'text'		=> 'Sign up',
				'return'	=> 'html', //html/link/url
			), $atts, 'register_link' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";


		if ( !is_user_logged_in() ) {

			if( $atts['return'] == 'html' ) {
				$output .= '<div id="'.$atts['id'].'" class="user-links register-link '.$atts['class'].'">';
			}

			if( $atts['return'] == 'url' ) {
				$output .= wp_registration_url( );
			} else {
				$output .= '<a href="'.wp_registration_url().'" title="' . $atts['text'] . '">' . $atts['text'] . '</a>';
			}

			if( $atts['return'] == 'html' ) {
				$output .= '</div>';
			}

		} 

		return $output;


	} // End aione_app_builder_register_link_shortcode()


	/**
	 * Display link to Reset Password page
	 *
	 * Function for shortcode [reset_password_link]. Returns html/link/url to display button/link to Reset Password page
	 *
	 * @type 	shortcode
	 * @since 	1.0.0.0
	 * @access 	public
	 * 
	 * @example [reset_password_link id="forgot_password_link" class="forgot-password-link" text="Forgot Password" return="link"]
	 * 
	 * @param 	id 		reset_password_link 	text 	
	 * @param 	class 	null 				text 	
	 * @param 	text 	Sign up 			text 	
	 * @param 	return 	html 				text 	html/link/url
	 * 
	 * @return 	html
	 */

	public function aione_app_builder_reset_password_link_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'id'		=> 'reset_password_link',
				'class'		=> '',
				'text'		=> 'Reset Password',
				'return'	=> 'html', //html/link/url
			), $atts, 'reset_password_link' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";

		
		if ( !is_user_logged_in() ) {

			if( $atts['return'] == 'html' ) {
				$output .= '<div id="'.$atts['id'].'" class="user-links reset-password-link '.$atts['class'].'">';
			}

			if( $atts['return'] == 'url' ) {
				$output .= wp_lostpassword_url( );
			} else {
				$output .= '<a href="'.wp_lostpassword_url().'" title="' . $atts['text'] . '">' . $atts['text'] . '</a>';
			}

			if( $atts['return'] == 'html' ) {
				$output .= '</div>';
			}

		}

		return $output;

	} // End aione_app_builder_reset_password_link_shortcode()

	public function aione_app_builder_map_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'zoom'		=> '10',
				'height'	=> '300',
				'address'	=> 'California, USA',
				'api_key'	=> AIONE_API_KEY,
			), $atts, 'aione_map' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";

		$output .= '<div class="aione-map">';
		$output .= '<iframe loading="lazy" width="100%" height="' . $atts['height'] . '" src="https://www.google.com/maps/embed/v1/place?q=' . $atts['address'] . '&maptype=roadmap&zoom=' . $atts['zoom'] . '&key=' . $atts['api_key'] . '" frameborder="0"></iframe>';
		$output .= '</div>';

		return $output;
	} // End aione_app_builder_map_shortcode()

	public function aione_app_builder_account_link_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'class'           => '',
				'text'           => 'Account'
			), $atts , 'account_link' );

		$admin_login_redirect_page_link = "#";

		$admin_login_redirect_page = get_option('admin_login_redirect_page' );
		if( isset($admin_login_redirect_page) ) {
			if( !empty($admin_login_redirect_page) ) {
				$admin_login_redirect_page_link = get_permalink($admin_login_redirect_page );
			}
		}

		$output = "";

		$output .= '<div id="account_link" class="user-links account-link '.$atts['class'].'">';
		$output .= '<a href="'.$admin_login_redirect_page_link.'" title="' . $atts['text'] . '">' . $atts['text'] . '</a>';
		$output .= '</div>';


		return $output;
	} // End aione_app_builder_account_link_shortcode()

	public function aione_app_builder_dashboard_link_shortcode( $atts ) {
		$atts = shortcode_atts(
			array(
				'class'           => '',
				'text'           => 'Dashboard'
			), $atts , 'dashboard_link' );
		$output = "";

		$output .= '<div id="dashboard_link" class="user-links dashboard-link '.$atts['class'].'">';
		$output .= '<a href="'.get_bloginfo( 'url' ).'/wp-admin/" title="' . $atts['text'] . '">' . $atts['text'] . '</a>';
		$output .= '</div>';

		return $output;
	} // End aione_app_builder_dashboard_link_shortcode()


		public function aione_app_builder_logout_link_shortcode( $atts ) {
		// Attributes
		$atts = shortcode_atts(
			array(
				'id'		=> 'logout_link',
				'class'		=> '',
				'text'		=> 'Logout',
				'return'	=> 'html', //html/link/url
			), $atts, 'logout_link' );

		$atts = $this->clean_shortcode_parameters( $atts );


		$output = "";

		if ( is_user_logged_in() ) {

			if( $atts['return'] == 'html' ) {

				$output .= '<div id="'.$atts['id'].'" class="user-links logout-link '.$atts['class'].'">';

			}

			if( $atts['return'] == 'url' ) {

				$output .= wp_logout_url( );

			} else {

				$output .= '<a href="'.wp_logout_url().'" title="' . $atts['text'] . '">' . $atts['text'] . '</a>';

			}

			if( $atts['return'] == 'html' ) {

				$output .= '</div>';

			}

		} 

		return $output;
	} // End aione_app_builder_logout_link_shortcode()
	

	public function aione_app_builder_is_user_logged_in_shortcode( $attr, $content = null ) {
		if ( is_feed() || !is_user_logged_in() || is_null( $content ) ) {
			return '';
		}
		/* Return the content. */
		return do_shortcode( $content );
	} // aione_app_builder_is_user_logged_in_shortcode ()

	public function aione_app_builder_user_not_logged_in_shortcode( $attr, $content = null ) {
		if ( is_user_logged_in() ) {	
			return '';
		}
		/* Return the content. */
		return do_shortcode( $content );
	} // aione_app_builder_user_not_logged_in_shortcode ()

	public function aione_app_builder_user_not_logged_in_error_shortcode( $attr, $content = null ) {
		if ( is_user_logged_in() ) {
			$content = '';
			return $content;
		}
		/* Return the content. */
		return do_shortcode($content );
	} // aione_app_builder_user_not_logged_in_error_shortcode ()

	public function aione_app_builder_is_user_shortcode( $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'capabilities' 	=> '',
			'roles' 		=> '',
			'users' 		=> '',
		), $atts, 'is_user' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$capabilities 	= trim( $atts['capabilities'] );
		$roles 			= trim( $atts['roles'] );
		$users 			= trim( $atts['users'] );

		if ( is_null( $content ) || is_feed() ) {
			return '';
		}

	
		/*
		echo "<br>capability =  ".$capability ;
		echo "<br>role =  ".$role ;
		echo "<br>users =  ".$users ;
		*/
		

		
		if ( !empty( $user ) && !empty( $role ) ) {

			$users = explode( ',', $users );
			$current_user_id = get_current_user_id( );
			$roles = explode( ',', $role );

			foreach ( $roles as $role ) {
				if ( current_user_can( trim( $role ) ) && in_array( $current_user_id, $users ) ) {
					return do_shortcode( $content );
				}
			}
		}

		if ( !empty( $users ) ) {

			$users = explode( ',', $users );
			$current_user_id = get_current_user_id( );

			if( in_array( $current_user_id, $users ) ) {
				return do_shortcode( $content );
			}
		}

		if ( !empty( $roles ) ) {

			$roles = explode( ',', $roles );

			$user = wp_get_current_user( );
			$user_roles = (array) $user->roles;

			foreach ( $user_roles as $user_role ) {
				if ( in_array( $user_role, $roles ) ) {
					return do_shortcode( $content );
				}
			}
		}

		
		if ( !empty( $capabilities ) ) {
			$capabilities = explode( ',', $capabilities );
			foreach ( $capabilities as $capability ) {
				if ( current_user_can( trim( $capability ) ) ) {
					return do_shortcode( $content );
				}
			}
		}
		
		return '';
	} // aione_app_builder_is_user_shortcode ()

	public function aione_app_builder_login_shortcode( $atts, $content = null ) {		
		$admin_login_redirect_page = get_option('admin_login_redirect_page' );
		$enable_two_factor_auth = get_option('enable_two_factor_auth' );
		if( isset($admin_login_redirect_page) ) {
			if( !empty($admin_login_redirect_page) ) {
				$custom_redirect =  get_permalink($admin_login_redirect_page );
			} else {
				$custom_redirect =  admin_url( );
			}
		}else {
			$custom_redirect =  admin_url( );
		}
		$atts = shortcode_atts(
			array(
				'echo'           => false,
				'redirect'       => $custom_redirect, 
				'form_id'        => 'loginform',
				'label_username' => __( 'Username' ),
				'label_password' => __( 'Password' ),
				'label_remember' => __( 'Remember Me' ),
				'label_log_in'   => __( 'Login' ),
				'id_username'    => 'user_login',
				'id_password'    => 'user_pass',
				'id_remember'    => 'rememberme',
				'id_submit'      => 'wp-submit',
			), $atts , 'login' );
		
		$output = "";


		$login = (isset($_GET['login']) ? $_GET['login'] : null );
		$errors = array( );
		if( isset($login) && $login == 'failed' ) {
			$output .=  '<div style="color:#cc0000;text-align:center;margin-bottom:-30px;">Email or password is incorrect. Please try again.</div>';
		}

		$args = array(
			'echo'           => $atts['echo'],
			'redirect'       => $atts['redirect'], 
			'form_id'        => $atts['form_id'],
			'label_username' => $atts['label_username'],
			'label_password' => $atts['label_password'],
			'label_remember' => $atts['label_remember'],
			'label_log_in'   => $atts['label_log_in'],
			'id_username'    => $atts['id_username'],
			'id_password'    => $atts['id_password'],
			'id_remember'    => $atts['id_remember'],
			'id_submit'      => $atts['id_submit'],
			'remember'	     => empty( $instance['remember'] ) ? true : false,
			'value_username' => esc_attr( $instance['value_username'] ),
			'value_remember' => !empty( $instance['value_remember'] ) ? true : false
		 );
		if ( !is_user_logged_in() ) {
			$output .= '<div id="aione-login-wrap" class="aione-user-forms">';			
			
				$output .= $this->frontend_login_form( apply_filters( 'frontend_login_form_args', $args ) );
			
			$output .= '</div>';
		
		} else {
			$output .= '<div class="center-align">';
			$output .= 'You are already logged in! ';
			$output .= '<a href="'.wp_logout_url().'" title="Logout" class="aione-common-button">Logout</a>';
			$output .= '</div>';
		}
		
		return $output;


	} // End aione_login_form_shortcode().

	function frontend_login_form( $args = array() ) {
		$defaults = array(
			'echo' => true,
			'redirect' => admin_url(),
			'form_id' => 'loginform',
			'label_username' => __( 'Username or Email Address' ),
			'label_password' => __( 'Password' ),
			'label_remember' => __( 'Remember Me' ),
			'label_log_in' => __( 'Log In' ),
			'id_username' => 'user_login',
			'id_password' => 'user_pass',
			'id_remember' => 'rememberme',
			'id_submit' => 'wp-submit',
			'remember' => true,
			'value_username' => '',
			'value_remember' => false,

		 );

		/**
		 * Filters the default login form output arguments.
		 */
		$args = wp_parse_args( $args, apply_filters( 'login_form_defaults', $defaults ) );

		/**
		 * Filters content to display at the top of the login form.
		 */
		$login_form_top = apply_filters( 'login_form_top', '', $args );

		/**
		 * Filters content to display in the middle of the login form.
		 */
		$login_form_middle = apply_filters( 'login_form_middle', '', $args );

		/**
		 * Filters content to display at the bottom of the login form.
		 */
		$login_form_bottom = apply_filters( 'login_form_bottom', '', $args );
		/**
		 * Filters content to display TFA Selection at the bottom of the login form.
		 */
		$tfa_selection = apply_filters( 'tfa_selection', '', $args );

		$form = '
			<form name="' . $args['form_id'] . '" id="' . $args['form_id'] . '" action=""  method="post">
				' . $login_form_top . '
				<div class="login-username">
					<label for="' . esc_attr( $args['id_username'] ) . '">' . esc_html( $args['label_username'] ) . '</label>
					<input type="text" name="username" id="' . esc_attr( $args['id_username'] ) . '" class="input" value="' . esc_attr( $args['value_username'] ) . '" size="20" />
				</div>
				<div class="login-password">
					<label for="' . esc_attr( $args['id_password'] ) . '">' . esc_html( $args['label_password'] ) . '</label>
					<input type="password" name="password" id="' . esc_attr( $args['id_password'] ) . '" class="input" value="" size="20" />
				</div>
				' . $login_form_middle . '
				' . $tfa_selection . '
				' . ( $args['remember'] ? '<div class="login-remember"><label><input name="rememberme" type="checkbox" id="' . esc_attr( $args['id_remember'] ) . '" value="forever"' . ( $args['value_remember'] ? ' checked="checked"' : '' ) . ' /> ' . esc_html( $args['label_remember'] ) . '</label></div>' : '' ) . '
				<div class="login-submit">
					<input type="submit" name="wp-submit" id="' . esc_attr( $args['id_submit'] ) . '" class="button button-primary" value="' . esc_attr( $args['label_log_in'] ) . '" />
					<input type="hidden" name="redirect_to" value="' . esc_url( $args['redirect'] ) . '" />
				</div>
				'. wp_nonce_field( 'aione_login_nonce', 'CSRFToken-aione', true, false ) .'
				' . $login_form_bottom . '
			</form>';

		if ( $args['echo'] )
			echo $form;
		else
			return $form;
	}

	function frontend_login_process( ) {
		$output = "";
		$enable_two_factor_auth = get_option('enable_two_factor_auth' );
		if (isset($_POST['CSRFToken-aione']) && wp_verify_nonce($_POST['CSRFToken-aione'], 'aione_login_nonce')) {
			if (isset($_POST['username']) && isset($_POST['password'])) {
				$creds = array( );
			    $creds['user_login'] = $_POST['username'];
			    $creds['user_password'] = $_POST['password'];
			    $creds['user_rememberme'] = $_POST['rememberme'];

			    //$user = wp_signon( $creds );
			    $user = wp_authenticate($creds['user_login'], $creds['user_password'] );

			    if ( is_wp_error($user) ) {
			      do_action( 'wp_login_failed', $user->user_login );
			    } else {
			    	if( empty($generated_otp_number) ) {
						$generated_otp_number = (rand(100000, 999999) );
					}
					if( empty( $generated_otp_time ) ) {
						$generated_otp_time = date("Y-m-d h:i:s" );
					}
					update_user_meta($user->ID,"wp-generated-otp-number",$generated_otp_number );

			    	if ( empty( $enable_two_factor_auth ) || $enable_two_factor_auth == "no" ) {
			    		wp_set_auth_cookie($user->ID, $creds['user_rememberme'], $secure_cookie="" );
			    		if (isset($_POST['redirect_to']) && $_POST['redirect_to']) {
					        $redirect_to = apply_filters( 'login_redirect', $_REQUEST['redirect_to'], $_REQUEST['redirect_to'], $user );
							wp_safe_redirect( $redirect_to );

							exit;
					    }
			    	} else {
			    		$login_page_tfa_role = get_option('login_page_tfa_role',array() );
			    		$otp_page = array( );
			    		foreach ($login_page_tfa_role as $value) {
			    			if( in_array($value, (array) $user->roles) ) {
			    				$otp_page[] = "true";
			    			}
			    		}
			    		if( in_array("true", $otp_page) ) { 
			    			$this->show_two_factor_login( $user );
			    		} else { 
			    			wp_set_auth_cookie($user->ID, $creds['user_rememberme'], $secure_cookie="" );
				    		if (isset($_POST['redirect_to']) && $_POST['redirect_to']) {
						        $redirect_to = apply_filters( 'login_redirect', $_REQUEST['redirect_to'], $_REQUEST['redirect_to'], $user );
								wp_safe_redirect( $redirect_to );

								exit;
						    }
			    		}
			    		//$this->show_two_factor_login( $user );			    		
			    	}
			    }
			    
			}
		}	
		return $output;	
	}

	/*public function aione_app_builder_login_shortcode( $atts, $content = null ) {
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
		$login = (isset($_GET['login']) ? $_GET['login'] : null );
		$errors = array( );
		if( isset($login) && $login == 'failed' ) {
			//$errors[] = 'Invalid username or password';
			//$output .= $this->aione_show_errors($errors );
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


	} // End aione_login_form_shortcode()*/

	public function aione_app_builder_register_shortcode( $atts, $content = null ) {
		// Attributes
		$atts =  shortcode_atts(
			array(
				'echo'						=> false,
				'captcha'					=> "true",
				'form_id'					=> 'aione_registration_form',
				'show_firstname' 			=> 'yes',
				'show_lastname' 			=> 'yes',
				'label_firstname'			=> __( 'First Name' ),
				'label_lastname' 			=> __( 'Last Name' ),
				'label_username' 			=> __( 'Username' ),
				'label_email'				=> __( 'Email Address' ),
				'label_password' 			=> __( 'Password' ),
				'label_password_again' 		=> __( 'Password Again' ),
				'label_submit' 				=> __( 'Register' ),
				'placeholder_firstname' 	=> __( 'Enter Your First Name' ),
				'placeholder_laststname' 	=> __( 'Enter Your Last Name' ),
				'placeholder_username' 		=> __( 'Enter Your Username' ),
				'placeholder_email' 		=> __( 'Enter Your Email Address' ),
				'role' 						=> get_option('default_role'),
			), $atts , 'register' );

		$args = array(

			'echo'						=> $atts['echo'],
			'captcha'                   => $atts['captcha'],
			'form_id'					=> $atts['form_id'],
			'show_firstname'			=> $atts['show_firstname'],
			'show_lastname'				=> $atts['show_lastname'],
			'label_firstname'			=> $atts['label_firstname'],
			'label_lastname'			=> $atts['label_lastname'],
			'label_username'			=> $atts['label_username'],
			'label_email'				=> $atts['label_email'],
			'label_password'			=> $atts['label_password'],
			'label_password_again'		=> $atts['label_password_again'],
			'label_submit'				=> $atts['label_submit'],
			'placeholder_firstname'		=> $atts['placeholder_firstname'],
			'placeholder_laststname'	=> $atts['placeholder_laststname'],
			'placeholder_username'		=> $atts['placeholder_username'],
			'placeholder_email'			=> $atts['placeholder_email'],
			'role' 						=> $atts['role'],

		 );
		
		

		$output = "";

		// only show the registration form to non-logged-in members
		if( !is_user_logged_in() ) {

			global $aione_load_css;

			// set this to true so the CSS is loaded
			$aione_load_css = true;

			// check to make sure user registration is enabled
			$registration_enabled = get_option('users_can_register' );
			
			// only show the registration form if allowed
			if( $registration_enabled) {

				$errors = array( );
				
				// load from post
				if( isset($_POST['action']) && $_POST['action'] == 'add_new') {

					$user_login		= $_POST["aione_user_login"];
					$user_email		= $_POST["aione_user_email"];
					$user_pass		= $_POST["aione_user_pass"];
					$pass_confirm 	= $_POST["aione_user_pass_confirm"];
					$user_first		= $_POST["aione_user_fname"];
					$user_last		= $_POST["aione_user_lname"];

					
					if( $atts['captcha'] == "true" ) {

						if ( class_exists( 'ReallySimpleCaptcha' ) )  {

							$captcha_value 	= $_POST['captcha_value'];
							$prefix 		= $_POST['captcha_prefix'];

							$captcha_instance_check = new ReallySimpleCaptcha( );
							$is_captcha_correct 	= $captcha_instance_check->check( $prefix, $captcha_value );
							
							if( !$is_captcha_correct ) {

								$errors[] = 'Wrong Captcha value';

							}

						}

					}
					
					// this is required for username checks
					if( $user_email == '') {

						//empty email
						$errors[] = 'Email address field can not be empty.';

					} else {

						if( !is_email( $user_email ) ) {

							//invalid email
							$errors[] = 'Email address you have entered is invalid. Enter a valid email address.';

						}

						if( email_exists( $user_email ) ) {

							//Email address already registered
							$errors[] = 'Email already registered. You can reset your password instead.';

						}

					}
					
					if( $user_login == '' ) {

							// empty username
						$errors[] = 'Username cannot be empty. Please enter a username';

					} else {
						
						$pattern = '/^[a-z0-9]+$/';
						
						if( !preg_match($pattern, $user_login) ) {
							$errors[] = 'The username you have entered is invalid. Please enter at least 6 alphanumeric characters in lowercase. Special characters and white spaces are not allowed.'; 
						} else{	
							if( !validate_username($user_login)) {
									// invalid username
								$errors[] = 'Username you have entered is invalid. ';
							}
						}


						if( username_exists($user_login)) {
								// Username already registered
							$errors[] = 'Username already taken. Try something else.';
						}				
					}

					if( $user_pass == '') {
							//Empty password
						$errors[] = 'Please enter a password';
					}

					if( $user_pass != $pass_confirm) {
							// passwords do not match
						$errors[] = 'Passwords do not match';
					}
					if( $user_first == '') {
							//Empty password
						$errors[] = 'First name can not be empry. Humans do have names.';
					}

					if( !empty($user_first) && !preg_match('/^[a-zA-Z\s]+$/', $user_first) ) {
							//Invalid Mobile
						$errors[] = 'Invalid first name. Numbers not allowed.';
					}
					
					if( !empty($user_last) && !preg_match('/^[a-zA-Z\s]+$/', $user_last) ) {
							//Invalid Mobile
						$errors[] = 'Invalid last name. Numbers not allowed.';
					}
					
						// only create the user in if there are no errors
					if( empty($errors)) {
						global $wp_roles;
						$roles = wp_roles()->get_names( );
						if( array_key_exists($atts['role'],$roles) ) {
							$user_role = $atts['role'];
						} else {
							$user_role = get_option('default_role' );
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
						if( is_int($new_user_id)) {
							if( isset($_POST['acf']) ) {
								$custom_fields = $_POST['acf'];
								foreach($custom_fields as $custom_field_key => $custom_field ) {
									update_field($custom_field_key , $custom_field, "user_".$new_user_id );
								}
								
							}
							
							apply_filters( 'activate_registration_filter', $new_user_id );

							$success_messages .= apply_filters( 'custom_text_after_registration_filter', $success_text );
							if($success_messages == ''){								
								$output .= 'Thank you for registering.';
							} else {
								$output .= $success_messages;
							}
							
							
						} else {
							$errors[] = 'Some error occurred. Please contact Administrator.';
						}
					} else {
						foreach($errors as $error ) {
							$output .=  '<div style="color:#cc0000;text-align:center;padding:10px">'.$error.'</div>';
						}
							//$output .= $this->aione_show_errors($errors );
						$output .= $this->aione_app_builder_user_registration_form($args );
					}
				} else {
					$output .= $this->aione_app_builder_user_registration_form($args );
				}
			} else {
				$output .= __('User registration is not enabled!' );
			}
		} else {

			$output .= __('You are already logged in!' );
		}

		return $output;

	} //END aione_app_builder_register_shortcode()

	public function aione_app_builder_user_registration_form( $args ) {
		if( $args['captcha'] == "true" ) {
			if (class_exists('ReallySimpleCaptcha'))  {
				
				$captcha_instance = new ReallySimpleCaptcha( );
				$captcha_instance->cleanup($minutes = 30 );
				
				$captcha_instance->chars = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789';	
				$captcha_instance->bg = array( 255, 255, 255 );
				$captcha_instance->fg = array( 21, 141, 197 );
				$captcha_instance->img_size = array( 205, 40 );
				$captcha_instance->base = array( 20, 30 );
				$captcha_instance->font_size = 22;
				$captcha_instance->char_length = 6;
				$captcha_instance->font_char_width = 28;
			//$upload_dir = wp_upload_dir( );
			//$captcha_instance->tmp_dir = $upload_dir['basedir'].'/captcha/';
				
			}	
		}
		
		$html_before_fields = '';
		$html_before_fields .= apply_filters( 'custom_text_before_registration_form_filter', $before_text );
		$html_before_fields .= '
		
		<form id="'.$args['form_id'].'" class="aione-registration-form aione-form form acf-form" action="'.get_permalink().'" method="post">
		<div class="postbox acf_postbox no_box">';

		if( $args['show_firstname'] == 'yes' ) {

			$html_before_fields .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="aione_user_fname">'.$args['label_firstname'].'<span class="required">*</span></label></div>
			<div class="acf-input-wrap"><input name="aione_user_fname" id="aione_user_fname" class="textbox large" type="text" placeholder="'.$args['placeholder_firstname'].'" value="" minlength="2" maxlength="200" required /></div>
			</div>';
		}

		if( $args['show_lastname'] == 'yes' ) {

			$html_before_fields .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="aione_user_lname">'.$args['label_lastname'].'</label></div>
			<div class="acf-input-wrap"><input name="aione_user_lname" id="aione_user_lname" class="textbox large" type="text" placeholder="'.$args['placeholder_laststname'].'" value=""/></div>
			</div>';
		}

		
		$html_before_fields .= '<div class="aione-form-field field field-type-text">
		<div class="label"><label for="aione_user_login">'.$args['label_username'].'<span class="required">*</span></label></div>
		<div class="acf-input-wrap"><input name="aione_user_login" id="aione_user_login" class="textbox large required" type="text" placeholder="'.$args['placeholder_username'].'" value="" minlength="6" maxlength="50" required/></div>
		</div>
		<div class="aione-form-field field field-type-text">
		<div class="label"><label for="aione_user_email">'.$args['label_email'].'<span class="required">*</span></label></div>
		<div class="acf-input-wrap"><input name="aione_user_email" id="aione_user_email" class="textbox large required" type="email" placeholder="'.$args['placeholder_email'].'" value="" required /></div>
		</div>

		<div class="aione-form-field field field-type-text">
		<div class="label"><label for="password">'.$args['label_password'].'<span class="required">*</span></label></div>
		<div class="acf-input-wrap"><input name="aione_user_pass" id="password" class="textbox large required" type="password" minlength="6" required /></div>
		</div>

		<div class="aione-form-field field field-type-text">
		<div class="label"><label for="password_again">'.$args['label_password_again'].'<span class="required">*</span></label></div>
		<div class="acf-input-wrap"><input name="aione_user_pass_confirm" id="password_again" class="textbox large required" type="password" minlength="6" required/></div>
		</div>

		';
		if( $args['captcha'] == "true" ) {
			if (class_exists('ReallySimpleCaptcha'))  {	
				$word = $captcha_instance->generate_random_word( );
				$prefix = mt_rand( );
				$image_name = $captcha_instance->generate_image( $prefix, $word );
				//$captcha_image_url =  $upload_dir['baseurl'].'/captcha/'.$image_name;
				//$captcha_image_url = plugins_url( );
				//$captcha_image_url =  plugin_dir_url(dirname(__FILE__))."library/really-simple-captcha/tmp/".$image_name;
				$captcha_image_url =  plugin_dir_url(dirname(__FILE__))."tmp/".$image_name;

				//$blog_template = intval($_GET['template'] );
				
				$html_before_fields .= '<div class="aione-form-field field field-type-text">
				<div class="label"><label for="register_form_captcha_value">Captcha<span class="required">*</span></label></div>
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
		jQuery("#'.$args['form_id'].'").validate( );
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
		$html_after_fields .= apply_filters( 'custom_text_after_registration_form_filter', $after_text );
		$field_groups = get_option('aione_app_builder_registration_custom_field_groups' );
		if( $field_groups == "" ) {
			$field_groups = 0;
		}
		if( !is_array($field_groups) ) {
			$field_groups = array($field_groups );
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
		
		ob_start( );
		acf_form($options );
		$output .= ob_get_contents( );
		ob_end_clean( );
		return $output;
	} // END aione_app_builder_user_registration_form()

	public function aione_app_builder_reset_password_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'id'		=> 'reset_password',
				'class'		=> '',
				'text'		=> 'Reset Password'
			), $atts, 'reset_password' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";

		if ( !is_user_logged_in() ) {

			$errors 			= array( );
			$success_messages 	= array( );

			$email 		= trim( wp_unslash( $_POST['user_login'] ) );
			$action 	= trim( $_POST['action'] );
			
			
			if( !empty( $action ) && $action == 'reset_password_submit' ) {

				if( !empty( $email ) ) {

					if( !is_email( $email ) ) {
						$errors[] = __( "The email address you have entered is invalid. Please enter a valid email address" );
					}

					if( !email_exists( $email ) ) {
						$errors[] = __( "The email address you have entered does not exist in our user database. Please make sure that the email address you have entered is . " );
					}

					if ( empty($errors) ) {
						
						$user = get_user_by( 'email', $email );
						$user_id = $user->ID;

						do_action( 'retrieve_password', $email );

						$reset_key = get_password_reset_key( $user );

						if ( is_wp_error( $reset_key ) ) {
							$errors[] = __( "Unable to reset password" );
						} else{
							
							// $success_messages[] = __( "Reset password process is initiated." );

							$user_notification = $this->aione_app_builder_reset_password_notification( $user_id, $reset_key );

							if( $user_notification ) { 
								$success_messages[] = __( "Password reset email sent. Please also check spam folder." );
							} else {
								$errors[] = __( "Unable to send reset password email." );
							}
						}
						
					}
				} else{
					$errors[] = __( "The email address field is required and can not be empty." );
				}

				
				if ( !empty( $errors ) ) {
					$output .= '<div class="aione-message error">';
					$output .= '<ul class="aione-messages">';
					foreach($errors as $error ) {
						$output .= '<li class="error"><strong>' . __('Error') . '</strong>: ' . $error . '</li>';
					}
					$output .= '</ul>';
					$output .= '</div>';
				}

				if ( !empty( $success_messages ) ) {
					$output .= '<div class="aione-message success">';
					$output .= '<ul class="aione-messages">';
					foreach($success_messages as $success_message ) {
						$output .= '<li class="success">' . $success_message . '</li>';
					}
					$output .= '</ul>';
					$output .= '</div>';
				}
			}

	
			$output .= '<div id="'.$atts['id'].'" class="reset-password-form-wrapper '.$atts['class'].'">';
			$output .= '<p>To recover your account password, enter your email address to the form below and click on <strong>Reset Password</strong> button. You will receive an email containing the link and instructions to reset your account password.</p>';
			$output .= '<form id="reset_password_form" class="reset-password-form"  action="'.get_permalink().'" method="post">';


			$output .= '<div class="field-wrapper">';
			$output .= '<div class="field-label">';
			$output .= '<label for="user_login">Email Address<span class="required">*</span></label>';
			$output .= '</div>';
			$output .= '<div class="field field-type-email">';
			$output .= '<input class="text-input field-long" name="user_login" type="email" id="user_login" required="required">';
			$output .= '</div>';
			$output .= '</div>';

			$output .= '<div class="field-wrapper">';
			$output .= '<div class="field field-type-submit">';
			$output .= '<input name="action" type="hidden" value="reset_password_submit">';
			$output .= '<input name="reset-password" type="submit" class="field-long submit btn button-primary button application-button" value="Reset Password">';
			$output .= '</div>';
			$output .= '</div>';


			$output .= '</form>';
			$output .= '</div>';

		} else {

			$output .= '<div id="'.$atts['id'].'" class="reset-password-form-wrapper '.$atts['class'].'">';
			$output .= '<p>';
			$output .= __( "You are already logged in to you account. Please logout first to reset password." );
			$output .= '</p>';
			$output .= '</div>';


		}
	
		return $output;

	}

	public function aione_app_builder_reset_password_notification( $user_id, $reset_key ) {

        // Get user by user_id 
        $user 			= get_userdata( $user_id );
        $user_meta 		= get_user_meta( $user_id );

        $user_login 	= $user->user_login;
        $user_email 	= $user->user_email;

        $first_name 	= $user_meta['first_name'][0];
        $last_name 		= $user_meta['last_name'][0];

        $name 			= $first_name . ' ' . $last_name;

        if( empty( $first_name ) ) {
        	if( empty( $last_name ) ) {
	        	$name = $user_login;
	        }
        }
        

        $site_title 	= get_option('blogname' );
        $site_link 		= home_url( );


        $headers = array( );
        $headers[] = "Content-Type: text/html";
        $headers[] = "charset=UTF-8";
        
		$subject = sprintf( __('🔐 Reset Password for %s account'), $site_title );

        $message = '';
        $message .= '<p>Hi ' . $name . ', </p>';
        $message .= '<p>This email is in response to your request to reset your account password.</p>';
        $message .= '<p>Please click the below link to reset your password.</p>';
		$message .= '<a href="' . network_site_url( "wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode( $user_login ), 'login' ) . ' target="_blank" >Click Here to Reset Password</a>';
        $message .= '<br>';
        $message .= '<p>Or copy and paste following url into browser\'s address bar</p>';
		$message .= network_site_url( "wp-login.php?action=rp&key=$reset_key&login=" . rawurlencode( $user_login ), 'login' );

        $message .= '<br>';
        $message .= '<br>';
        $message .= '<p><strong>Your Account Details</strong><p>';
        // $message .= '<p>Login URL: ' . wp_login_url() . '</p>';
        $message .= '<p>Username: ' . $user_login . '</p>';
        $message .= '<p>E-mail: ' . $user_email . '</p>';
        $message .= '<br>';

        $message .= '<p>Website Link: ' . $site_link . '</p>';
        $message .= '<p>If this was a mistake, just ignore this email and nothing will happen and your password will not change.</p>';
        $message .= '<p>If you have any problems, please contact administrator on contact page.</p>';
        $message .= '<p>Thanks & Regards</p>';
        $message .= '<p>' . $site_title . '</p>';

        $notification_sent = wp_mail( $user_email, $subject, $message, $headers );

        if( $notification_sent ) {
            return true;
        } else {
            return false;
        }

    } // END aione_app_builder_forget_password_notification()



	/*public function aione_app_builder_account_shortcode ($attr, $content = null ) {
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
		if( is_user_logged_in() ) {
			$output .= $this->aione_app_builder_account_menu_shortcode($attr_menu );	
			$output .= $this->aione_app_builder_account_content_shortcode($attr_content );
		} else {
			$output .="";
		}
		
		return $output;
	}

	public function aione_app_builder_account_menu_shortcode ($attr, $content = null ) {
	// Attributes
		extract( shortcode_atts(
			array(
			'pages'    => 'yes', // yes/no
			'sub_pages'    => 'no', // yes/no
			'layout'    => 'vertical',// horizontal/vertical
		), $attr )
		 );

		$output = "";
		if( is_user_logged_in() ) {
			$output .= '<div id="account_menu" class="account-menu '.$layout.'" >';
			$output .= '<ul class="account-menu-list" id="account_menu_list">';

			if( $pages == 'yes' ) {
				$output .= '<li><a href="?action=account">Account</a></li>';
				$output .= '<li><a href="?action=profile">View Profile</a></li>';
				$output .= '<li><a href="?action=edit-profile">Edit Profile</a></li>';
				$output .= '<li><a href="?action=change-password">Change Password</a></li>';
			}

			if( $sub_pages == 'yes' ) {
				$post_id = get_the_ID( );
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

	public function aione_app_builder_account_content_shortcode ($attr, $content = null ) {
		extract( shortcode_atts(
			array(
			'pages'    => 'yes', // yes/no
			'sub_pages'    => 'no', // yes/no
			'layout'    => 'box',// box/table/list
		), $attr )
		 );

		$output = "";
		if( is_user_logged_in()) {
			$action = $_GET['action'];
			if( !isset($action) ) {
				$action = "account";
			}
			if( $action == "account" ) {
				$output .='<div class="account-content-outer" id="account_content_outer">';
				$output .='<div class="account-content" id="account_content">';
				$output .='<ul class="account-items '.$layout.'">';
				if( $pages == 'yes' ) {
					$output .='<li><a class="account-item" href="?action=profile">View Profile</a></li>';
					$output .='<li><a class="account-item" href="?action=edit-profile">Edit Profile</a></li>';
					$output .='<li><a class="account-item" href="?action=change-password">Change Password</a></li>';
				}
				if( $sub_pages == 'yes' ) {
					$post_id = get_the_ID( );
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
		
		if( $action == "profile" ) {
			$output .= $this->aione_app_builder_profile_shortcode( );	
		} // Action = Profile
		
		if( $action == "edit-profile" ) {
			$output .= $this->aione_app_builder_edit_profile_shortcode( );	
		} // Action = Edit Profile
		
		if( $action == "change-password" ) {
			$output .= $this->aione_app_builder_change_password_shortcode( );	
		} // Action = change-password
	} else {
		$output .= "";
	}
	return $output;
	}*/

	public function aione_app_builder_view_profile_shortcode( $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'class'	=> 'view-profile',
			'id'	=> 'view_profile'
		), $atts, 'view_profile' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = '';

		if( is_user_logged_in() ) {

			$user 		= wp_get_current_user( );
			$user_id 	= $user->ID;
			$username 	= $user->user_login;
			$user_roles = $user->roles;
			$user_meta 	= get_user_meta( $user_id );
			$action 	= $_GET['action'];
			

			$output .='<div id="' . $atts['id'] . '" class="' . $atts['class'] . '">';

			$output .= '<ul>
			<li class="user_id"><div class="user-detail-label">ID</div>
			<div class="user-detail-value">' . $user_id . '</div>
			<div class="clear"></div></li>
			<li class="user_first_name"><div class="user-detail-label">First Name</div>
			<div class="user-detail-value">' . $user_meta['first_name'][0] . '</div>
			<div class="clear"></div></li>
			<li class="user_last_name"><div class="user-detail-label">Last name</div>
			<div class="user-detail-value">' . $user_meta['last_name'][0] . '</div>
			<div class="clear"></div></li>
			<li class="user_email"><div class="user-detail-label">Email</div>
			<div class="user-detail-value">' . $user->user_email . '</div>
			<div class="clear"></div></li>
			';
			
			$field_groups = get_option( 'aione_app_builder_registration_custom_field_groups' );

			if( !empty( $field_groups ) ) {
				if( !is_array( $field_groups ) ) {
					$field_groups = array($field_groups );
				}
			} else {
				$field_groups = array( );
			}

			foreach( $field_groups as $field_group_key => $field_group ) {
				//$fields = apply_filters('acf/field_group/get_fields',array(), $field_group );
				$fields = acf_get_fields( $field_group );
				
				foreach( $fields as $fields_key => $field ) {
					$field_key = $field['key'];
					$field_data_shortcode = '[user_meta field="' . $field_key . '" user_id="' . $user_id . '" style="" show_label="no"]';;
					$field_data = do_shortcode( $field_data_shortcode );

					$output .= '<li class="'.$field_key.'"><div class="user-detail-label">' . $field['label'] . '</div>';
					$output .= '<div class="user-detail-value">' . $field_data . '</div>
					<div class="clear"></div></li>';
				}
			} 
			$output .= '</ul>
			</div>';
		}

		return $output;
	}

	public function aione_app_builder_edit_profile_shortcode( $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'class'	=> 'edit-profile',
			'id'	=> 'edit_profile'
		), $atts, 'edit_profile' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";

		if( is_user_logged_in() ) {

			$user 		= wp_get_current_user( );
			$user_id 	= $user->ID;
			$username 	= $user->user_login;
			$user_meta 	= get_user_meta( $user_id );
			$action 	= $_POST['action'];
			
			if( !empty( $action )  && $action == 'update_profile' ) {
				$first_name 	= $_POST['aione_user_fname'];
				$last_name 		= $_POST['aione_user_lname'];
				$custom_fields 	= $_POST['acf'];

				wp_update_user( array( 
					'ID' 			=> $user_id, 
					'first_name' 	=> $first_name, 
					'last_name' 	=> $last_name 
				) );
				
				foreach( $custom_fields as $custom_field_key => $custom_field ) {
					update_field( $custom_field_key , $custom_field, "user_".$user_id );
				}
			}

			$html_before_fields = "";
			$html_before_fields .= '
			<form id="aione_edit_profile_form" class="aione-edit-profile-form aione-form form acf-form" action="'.get_permalink().'" method="post">
			';
			$html_before_fields .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="aione_user_fname">First Name</label></div>
			<div class="acf-input-wrap"><input name="aione_user_fname" id="aione_user_fname" class="textbox large" type="text" placeholder="" value="'.$user_meta['first_name'][0].'"/></div>
			</div>';
			$html_before_fields .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="aione_user_lname">Last Name</label></div>
			<div class="acf-input-wrap"><input name="aione_user_lname" id="aione_user_lname" class="textbox large" type="text" placeholder="" value="'.$user_meta['last_name'][0].'"/></div>
			</div>';		
			
			$html_after_fields = '<div class="aione-form-field field">
			<input type="hidden" name="action" value="update_profile">
			<input type="submit" value="Update">
			</div>
			';
			
			$field_groups = get_option('aione_app_builder_registration_custom_field_groups' );
			
			if( !empty( $field_groups ) ) {
				if( !is_array( $field_groups ) ) {
					$field_groups = array($field_groups );
				}
			} else {
				$field_groups = array( );
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

			acf_form_head( );
			
			ob_start( );
			acf_form( $options );
			$output .= ob_get_contents( );
			ob_end_clean( );
			
		} else {
			$output .= "";
		}	
		return $output;
	}

	public function aione_app_builder_change_password_shortcode( $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'class'	=> 'change-password',
			'id'	=> 'change_password'
		), $atts, 'change_password' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";
		$errors = array( );

		$current_user = wp_get_current_user( );

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
					$output .= '<div class="aione-message success">';					
					$output .= 'Password Changed successfully!';
					$output .= '</div>';

					/*$output .= '<div class="aione-message success">';
					$output .= 'You will be redirected to login page in <span id="redirectcountdown">10</span> seconds.';
					$output .= '</div>';

					$output .= "<script type='text/javascript'>jQuery(window).ready( function() { var time = 10; setInterval( function() { time--; if (time >= 0 ) { jQuery('#redirectcountdown').html(time );} if (time === 0) { window.location = '".do_shortcode("[login_link return='url']")."';} }, 1000 );} );</script>";*/
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
			$output .= '<div class="aione-message error"><ul class="aione-messages">';
			foreach($errors as $error ) {
				$output .= '<li><strong>' . __('Error') . '</strong>: ' . $error . '</li>';
			}
			$output .= '</ul></div>';
		}
		if( is_user_logged_in()) {
			$output .= '<form method="post" class="aione-change-password-form aione-form form acf-form" id="aione-change-password-form" action="">';
			$output .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="current_pass">Current Password <span class="required red">*</span></label></div>
			<div class="acf-input-wrap"><input name="current_pass" id="current_pass" class="text-input field-long" type="password" /></div>
			</div>';
			$output .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="pass1">New Password <span class="required red">*</span></label></div>
			<div class="acf-input-wrap"><input name="pass1" id="pass1" class="text-input field-long" type="password" /></div>
			</div>';
			$output .= '<div class="aione-form-field field field-type-text">
			<div class="label"><label for="pass2">Re-enter New Password <span class="required red">*</span></label></div>
			<div class="acf-input-wrap"><input name="pass2" id="pass2" class="text-input field-long" type="password" /></div>
			</div>';
			
			$output .= '<input name="updateuser" type="submit" id="updateuser" class="field-long submit btn button-primary button application-button" value="Change Password">';
			$output .= '<input name="action" type="hidden" id="action" value="changepassword">';
			$output .= '</form>';
		}
		return $output;
	} // END aione_app_builder_change_password_shortcode()



	public function aione_app_builder_home_url_shortcode( $attr, $content = null ) {
		return home_url( );
	} // End aione_app_builder_home_url_shortcode()

	public function aione_app_builder_url_shortcode( $atts ) {


		$atts = shortcode_atts(
			array(
				'id' 		=> '',
				'path' 		=> '',
				'title' 	=> '',
				'action' 	=> '',
				'class' 	=> '',
			), $atts,'url' );

		$atts = $this->clean_shortcode_parameters( $atts );
		
		if( empty( $atts['id'] ) && empty( $atts['path'] ) && empty( $atts['title'] ) ) {

			return home_url( );

		} else {

			$page_id = 0;

			if( $atts['id'] && is_numeric( $atts['id'] ) ) {

				$page_id = $atts['id'];

			}
			
			if( $atts['path'] != '' ) {

				$page_id = get_page_by_path($atts['path'] );

			}
			
			if( $atts['title'] != '' ) {

				$page_id = get_page_by_title($atts['title'] );

			}
			
			if ( $page_id ) {

				return get_page_link($page_id );

			} else {

				return null;

			}	

		}

	} // End aione_app_builder_url_shortcode()

	public function aione_app_builder_post_count_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts(
			array(
				'post_type'			=> 'post',
				'status'			=> array('publish'),
				'cat'				=> '',
				'cat_id'			=> '',
				'meta_key'			=> '',
				'meta_value'		=> '',
				'meta_compare'		=> 'LIKE',
				'meta_query'		=> '',
				'author'			=> '',
				'author_id'			=> '',
				'posts_per_page'	=> '99999999999',
				'post_count'		=> '999999999',
				'order'				=> 'DESC',
				'orderby'			=> 'date',
			), $atts, 'post_count' );
		/*echo "<pre>";
		print_r( $atts );
		echo "</pre>";*/
		$atts = $this->clean_shortcode_parameters( $atts );
		/*echo "<pre>";
		print_r( $atts );
		echo "</pre>";*/
		global $theme_options, $post;

		$output = "";

		if( !empty( $atts['status'] ) ) {
			$status = explode( ',', $atts['status'] );
		}
		if( !empty( $atts['meta_query'] ) ) {
			$atts['meta_query'] = json_decode( $atts['meta_query'] , TRUE );
		}
		//$atts = $this->clean_shortcode_parameters( $atts );

		// WP_Query arguments
		$args = array (
			'post_type'				=> $atts['post_type'],
			'post_status'			=> $status,
			'cat'					=> $atts['cat_id'],
			'category_name'			=> $atts['cat'],
			'meta_key'				=> $atts['meta_key'],
			'meta_value'			=> $atts['meta_value'],
			'meta_compare'			=> $atts['meta_compare'],
			'meta_query'			=> $atts['meta_query'],
			'author'				=> $atts['author_id'],
			'author_name'			=> $atts['author'],
			'posts_per_page'		=> $atts['posts_per_page'],
			'post_count'			=> $atts['post_count'],
			'ignore_sticky_posts'	=> false,
			'order'					=> $atts['order'],
			'orderby'				=> $atts['orderby'],
			'cache_results'			=> true,
			'update_post_meta_cache'=> true,
			'update_post_term_cache'=> true,
		 );

		
		/*echo "<pre>";
		print_r( $args );
		echo "</pre>";*/

		
		
		$posts = new WP_Query( $args );

		/*echo "<pre>";
		print_r( $posts );
		echo "</pre>";*/

		
		$output = $posts->found_posts;
			
		return $output;
	} // END aione_app_builder_post_count_shortcode()



	public function aione_app_builder_posts_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts( array(
			'post_type'			=> 'post',
			'status'			=> 'publish',
			'cat'				=> '',
			'cat_id'			=> '',
			'post__in'			=> '',
			'meta_key'			=> '',
			'meta_value'		=> '',
			'meta_value_num'	=> '',
			'meta_compare'		=> 'LIKE',
			'meta_query'		=> '',
			'author'			=> '',
			'author_id'			=> '',
			'tax_query'			=> '',
			'offset'			=> '',
			'posts_per_page'	=> -1,
			'order'				=> 'DESC',
			'orderby'			=> 'date',
			'template'			=> '',
			'pagination'		=> 'yes',
			'api'				=> 'no', // yes/no
			'style'				=> 'list', // div/list/''
			'id'				=> '',
			'class'				=> ''	
		), $atts, 'posts' );

		global $theme_options, $post;
		$backup = $post;
		$output = "";

		$atts = $this->clean_shortcode_parameters( $atts );

		$status = explode(',',$atts['status'] );


		if( !empty( $atts['post__in'] ) ) {
			$post__in = explode( ',', $atts['post__in'] );
		}

		if( !empty( $atts['meta_query'] ) ) {
			$atts['meta_query'] = json_decode( $atts['meta_query'] , TRUE );
		}
		
	    $current_page = get_query_var('paged' );

	    if( $current_page > 0 ) {
	    	$offset = $atts['posts_per_page'] * ( $current_page - 1 );
	    } else{
	    	$offset = 0;
	    }

	    $tax_query = $atts['tax_query'];
	    if( !empty( $tax_query ) ) {

	        $tax_query = explode("=",$tax_query );

			$tax_query_array = array(
				'relation' => 'AND',
				array(
					'taxonomy'         => $tax_query[0],
					'terms'            => $tax_query[1],
					'field'            => 'slug',
					'operator'         => 'IN',
					'include_children' => false,
				),
			 );
	    }

		// WP_Query arguments
		$args = array (
			'post_type'				=> $atts['post_type'],
			'post_status'			=> $status,
			'cat'					=> $atts['cat_id'],
			'category_name'			=> $atts['cat'],
			'author'				=> $atts['author_id'],
			'author_name'			=> $atts['author'],
			'meta_key'				=> $atts['meta_key'],
			'meta_value'			=> $atts['meta_value'],
			'meta_value_num'		=> $atts['meta_value_num'],
			'meta_compare'			=> $atts['meta_compare'],
			'meta_query'			=> $atts['meta_query'],
			'posts_per_page'		=> $atts['posts_per_page'],
			'post__in'				=> $post__in,
			'offset'				=> $offset,
			'ignore_sticky_posts'	=> false,
			'order'					=> $atts['order'],
			'orderby'				=> $atts['orderby'],
			'cache_results'			=> true,
			'update_post_meta_cache'=> true,
			'update_post_term_cache'=> true,
		 );

		if( !empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query_array;
		}	
		
		$posts = new WP_Query( $args );

		if( $atts['api'] == 'yes' ) {

			$api_array = array( );
			
			foreach ( $posts->posts as $api_key => $api_post ) {
				$fields = array( );
				$post_meta = array( );
				foreach ( $api_post as $key => $field ) {
					$fields[$key] = $field;
				}

				$api_post_meta = get_post_meta( $api_post->ID );


				foreach ( $api_post_meta as $key => $field ) {
					$post_meta[$key] = $field;
				}

				$fields['post_meta'] = $post_meta;
				$api_array[$api_key] = $fields;
				
			}
			
			$output .= json_encode( $api_array, JSON_PRETTY_PRINT );

			return $output;

		}



		$total_posts = $posts->found_posts;
		$total_pages = $posts->max_num_pages;

		if( $total_pages < 2 ) {
			$atts['pagination'] = 'no';
		}

		$is_template = false;

		$template = $atts['template'];


		if( !empty( $template ) ) {
			$aione_templates = @get_option( 'aione-templates' );
			$aione_template = @$aione_templates[$template]['content'];
			if( !empty( $aione_template ) ) {
				$is_template = true;
			}
		}

		$id_attribute ='';
		if( !empty($atts['id']) ) {
			$id_attribute = 'id="'.$atts['id'].'"';
		}

		
		if( $posts->have_posts() ) {
			if( $is_template ) {
				if( !empty( $atts['style'] ) ) {
					$output .= '<div class="aione-template type-archive '.$atts['template'].'">';
					$output .= do_shortcode( $aione_templates[$template]['archive_header'] );
				}
			} else {
				if( !empty( $atts['style'] ) ) {
					$output .= '<ul '.$id_attribute.' class="list-posts '.$atts['class'].'">';
				}
			}
			while( $posts->have_posts() ) {

				$posts->the_post( ); 
				// $output .= '['.$post->post_type .' '. $post->ID. ' ' .$args['meta_value'].']';


				if( !$is_template ) {
					if( !empty( $atts['style'] ) ) {
						$output .= '<li>';
					}
				}
				if( $is_template ) {
					$output .= do_shortcode( $aione_template );
				} else { 
					if( has_post_thumbnail($post->ID) ) {
						$output .= '<div class="post-image">';
						$output .= '<a href="'.get_permalink().'">';
						$output .= 	get_the_post_thumbnail($post->ID,'tabs-img' );
						$output .= '</a>';
						$output .= '</div>';
					} 
					$output .= '<div class="post-holder">';
					$output .= '<a href="'.get_permalink().'" class="post-title">'.get_the_title().'</a>';
					$output .= '<div class="post-meta">';
					$output .= 	get_the_date($theme_options['date_format'] );
					$output .= '</div>';
					$output .= '</div>';
					$output .= '<div class="clear"></div>';
				
				}
				if( !$is_template ) {
					if( !empty( $atts['style'] ) ) {
						$output .= '</li>';
					}
				}
			}


			if( $is_template ) {
				if( !empty( $atts['style'] ) ) {
					$output .= do_shortcode( $aione_templates[$template]['archive_footer'] );
					$output .= '</div>';
				}
			} else{
				if( !empty( $atts['style'] ) ) {
					$output .= '</ul>';
				}
			}

			if( $atts['pagination'] == 'yes' ) {				
				$output .= aione_pagination( $posts );
			}

		}  else {
			if( !empty( $atts['style'] ) ) {
				$output .= '<h5 class="font-size-16 align-center">No Posts Available.</h5>';
			}
		}

		$posts->reset_postdata( );
		$post = $backup;
		
		return $output;
	} // END aione_app_builder_posts_shortcode()

	public function aione_app_builder_structured_data_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts( array(
			'post_type'			=> 'post',
			'status'			=> 'publish',
			'cat'				=> '',
			'cat_id'			=> '',
			'post__in'			=> '',
			'meta_key'			=> '',
			'meta_value'		=> '',
			'meta_value_num'	=> '',
			'meta_compare'		=> 'LIKE',
			'meta_query'		=> '',
			'author'			=> '',
			'author_id'			=> '',
			'tax_query'			=> '',
			'offset'			=> '',
			'posts_per_page'	=> -1,
			'order'				=> 'DESC',
			'orderby'			=> 'date',
			'template'			=> '',
		), $atts, 'structured_data' );

		global $theme_options, $post;
		$backup = $post;
		$output = "";

		$atts = $this->clean_shortcode_parameters( $atts );

		$status = explode(',',$atts['status'] );

		if( !empty( $atts['post__in'] ) ) {
			$post__in = explode( ',', $atts['post__in'] );
		}

		if( !empty( $atts['meta_query'] ) ) {
			$atts['meta_query'] = json_decode( $atts['meta_query'] , TRUE );
		}
		
	    $current_page = get_query_var('paged' );

	    if( $current_page > 0 ) {
	    	$offset = $atts['posts_per_page'] * ( $current_page - 1 );
	    } else{
	    	$offset = 0;
	    }

	    $tax_query = $atts['tax_query'];
	    if( !empty( $tax_query ) ) {

	        $tax_query = explode("=",$tax_query );

			$tax_query_array = array(
				'relation' => 'AND',
				array(
					'taxonomy'         => $tax_query[0],
					'terms'            => $tax_query[1],
					'field'            => 'slug',
					'operator'         => 'IN',
					'include_children' => false,
				),
			 );
	    }

		// WP_Query arguments
		$args = array (
			'post_type'				=> $atts['post_type'],
			'post_status'			=> $status,
			'cat'					=> $atts['cat_id'],
			'category_name'			=> $atts['cat'],
			'author'				=> $atts['author_id'],
			'author_name'			=> $atts['author'],
			'meta_key'				=> $atts['meta_key'],
			'meta_value'			=> $atts['meta_value'],
			'meta_value_num'		=> $atts['meta_value_num'],
			'meta_compare'			=> $atts['meta_compare'],
			'meta_query'			=> $atts['meta_query'],
			'posts_per_page'		=> $atts['posts_per_page'],
			'post__in'				=> $post__in,
			'offset'				=> $offset,
			'ignore_sticky_posts'	=> false,
			'order'					=> $atts['order'],
			'orderby'				=> $atts['orderby'],
			'cache_results'			=> true,
			'update_post_meta_cache'=> true,
			'update_post_term_cache'=> true,
		 );

		if( !empty( $tax_query ) ) {
			$args['tax_query'] = $tax_query_array;
		}	
		
		$posts = new WP_Query( $args );

		$total_posts = $posts->found_posts;
		$total_pages = $posts->max_num_pages;

		$is_template = false;

		$template = $atts['template'];

		$structured_data_array = array( );


		if( !empty( $template ) ) {
			$aione_templates = @get_option( 'aione-templates' );
			$aione_template = @$aione_templates[$template]['structured_data'];
			if( !empty( $aione_template ) ) {
				$is_template = true;
			}
		}

		
		if( $posts->have_posts() ) {
			while( $posts->have_posts() ) {

				$posts->the_post( ); 

				$structured_data_string = do_shortcode( $aione_template );
				$structured_data_text = strip_tags( $structured_data_string );
				$structured_data_array[] = $structured_data_text;
			}
		}

		if( $is_template ) {
			$output .= do_shortcode( $aione_templates[$template]['structured_data_header'] );

			$output .= implode(',', $structured_data_array );

			$output .= do_shortcode( $aione_templates[$template]['structured_data_footer'] );
		} 

		$posts->reset_postdata( );
		$post = $backup;
		
		return $output;
	} // END aione_app_builder_structured_data_shortcode()

	


	public function aione_app_builder_pagination_shortcode( $atts, $content = null ) {
		// Attributes
		$atts = shortcode_atts( array(
			'id'		=> '',
			'class'		=> ''	
		), $atts, 'pagination' );
		
		$atts = $this->clean_shortcode_parameters( $atts );

		$output .= aione_pagination( );

		return $output;

	} // END aione_app_builder_pagination_shortcode()

	public function aione_app_builder_post_shortcode( $atts, $content = null ) {
		
		// Attributes
		$atts = shortcode_atts( array(
			'post_id'	=> '',
			'template'	=> '',
			'id'		=> '',
			'class'		=> ''	
		), $atts, 'post' );

		global $post, $theme_options;
		
		$atts = $this->clean_shortcode_parameters( $atts );
		
		$output = "";

		$post =  get_post( $atts['post_id'] );
		setup_postdata( $post ); 
		
		$is_template = false;

		if( !empty( $atts['template'] ) ) {
			$template = $atts['template'];
			$aione_templates = @get_option( 'aione-templates' );
			$aione_template = @$aione_templates[$template]['content'];
			if( !empty( $aione_template ) ) {
				$is_template = true;
			}
		}

		$id_attribute ='';
		if( !empty($atts['id']) ) {
			$id_attribute = 'id="'.$atts['id'].'"';
		}

		if( get_post_status ( $atts['post_id'] ) ) {

			if( $is_template ) {
				$output .= '<div '.$id_attribute.' class="aione-post '.$atts['template'].' '.$atts['class'].'">';
				$output .= do_shortcode( $aione_template );
				$output .= '</div>';
			} else{
				$output .= '<h5 class="font-size-16 align-center">Template does not exist</h5>';
			}
		} else{
			$output .= '<h5 class="font-size-16 align-center">Post does not exist</h5>';
		}

		wp_reset_postdata( );
		
		return $output;
	} // END aione_app_builder_post_shortcode()

	public function aione_app_builder_post_data_shortcode( $atts, $content = null ) {
		global $post;
		
		// Attributes
		$atts = shortcode_atts( array(
			'post_id'	=> $post->ID,
			'field' 	=> '',	
		), $atts, 'post_data' );

		
		$atts = $this->clean_shortcode_parameters( $atts );
		
		$output = "";

		$post =  get_post( $atts['post_id'] );
		
		$field = $atts['field'];

		$output .= $post->$field;
		
		return $output;
	} // END aione_app_builder_post_shortcode()

	public function aione_app_builder_list_comments_shortcode( $atts ) {
		global $theme_options, $post;
		// Attributes
		$atts = shortcode_atts(
			array(
				'post_id'	=> $post->ID,
				'count'           => '',
				'id'           => '',
				'class'           => ''
			), $atts,'list_comments' );
		
		$output = "";
		$number = $atts['count'];
		
		$id_attribute ='';
		if( !empty($atts['id']) ) {
			$id_attribute = 'id="'.$atts['id'].'"';
		}

		
		$args = array(
			'include_unapproved'=> '',
			'fields'			=> '',
			'ID'				=> '',
			'comment__in'		=> '',
			'comment__not_in'	=> '',
			'karma'				=> '',
			'number'			=> $number,
			'offset'			=> '',
			'orderby'			=> '',
			'order'				=> 'DESC',
			'parent'			=> '',
			'post_ID'			=> '', // ignored (use post_id instead)
			'post_id'			=> $atts['post'],
			'post__in'			=> '',
			'post__not_in'		=> '',
			'post_author'		=> '',
			'post_name'			=> '',
			'post_parent'		=> '',
			'post_status'		=> '',
			'post_type'			=> '',
			'status'			=> 'all',
			'type'				=> '',
			'type__in'			=> '',
			'type__not_in'		=> '',
			'user_id'			=> '',
			'search'			=> '',
			'count'				=> false,
			'meta_key'			=> '',
			'meta_value'		=> '',
			'meta_query'		=> '',
			'date_query'		=> null, // See WP_Date_Query
		 );
		$the_comments = get_comments( $args );

		if( $the_comments ) {
			$output .= '<ul '.$id_attribute.' class="list-posts resent-comments '.$atts['class'].'">';
			foreach($the_comments as $comment) { //echo "<pre>";print_r($comment );echo "</pre>";
				$output .= '<li>';
				$output .= '<div class="post-image">';
				$output .= '<a>';
				$output .= 	get_avatar($comment, '52' );
				$output .= '</a>';
				$output .= '</div>';
				$output .= '<div class="post-holder">';
				$output .= strip_tags($comment->comment_author) . ' says:';
				$output .= '<div class="post-meta">';
				/*$output .= '<a class="comment-text-side" href="' . get_permalink($comment->ID).'#comment-' .$comment->comment_ID . '" title="'.strip_tags($comment->comment_author) .' on '.$comment->post_title .'">';
				$output .= strip_tags($comment->comment_content );
				$output .= '</a>';*/
				$output .= strip_tags($comment->comment_content );
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

	public function aione_app_builder_comment_count_shortcode( $atts ) {
		global $theme_options, $post;
		// Attributes
		$atts = shortcode_atts(
			array(
				'post_id'	=> $post->ID,
				'style'     => '',
				'id'        => '',
				'class'     => ''
			), $atts,'comment_count' );
		
		$output = "";
		
		$output .= get_comments_number( $atts['post_id'] );

		return $output;

	} // END aione_app_builder_comment_count_shortcode()

	public function aione_app_builder_faq_shortcode($atts) {

		$atts = shortcode_atts(
			array(
				'category' => '',
				'id' => '',
				'class' => '',
			), $atts,'faq' );
		
		$output = '';
		if (!$atts['category'] ) {
			return '';
			$portfolio_category = get_terms('faq_category' );
			if ($portfolio_category ) {
				$output .= '<ul class="faq-tabs clearfix">';
				$output .= '<li class="active"><a data-filter="*" href="#">' . __('All', 'Aione') . '</a></li>';
				foreach ($portfolio_category as $portfolio_cat ) {
					$output .= '<li><a data-filter=" ' . urldecode($portfolio_cat->slug) .'"href="#">' . $portfolio_cat->name . '</a></li>';
				}
				$output .= '</ul>';
			}
		} 
		
		$output .= '<div class="portfolio-wrapper">';
		$output .= '<div class="accordian aione-accordian">';
		$output .= '<div class="panel-group" id="accordian-one">';
		
		$args = array(
			'post_type' => 'faqs',
			'tax_query' => array(
				array(
					'taxonomy' => 'faq_category',
					'field'    => 'slug',
					'terms'    => $atts['category'],
				),
			),
			'nopaging' => true
		 );
		$gallery = new WP_Query($args );
		$count = 0;
		while($gallery->have_posts()): $gallery->the_post( );
			
			
			$count++;
			$item_classes = '';
			$item_cats = get_the_terms($post->ID, 'faq_category' );
			if ($item_cats):
				foreach ($item_cats as $item_cat) {
					$item_classes .= urldecode($item_cat->slug) . ' ';
				}
			endif;
			$output .= '<div class="aione-panel panel-default faq-item '. $item_classes .'">';
			$output .= aione_render_rich_snippets_for_pages( );
			$output .= '<div class="panel-heading">';
			$output .= '<h4 class="panel-title toggle"><a data-toggle="collapse" class="collapsed" data-parent="#accordian-one" href="#collapse-'.get_the_ID().'"><i class="fa-aione-box"></i>';
			$output .= get_the_title( );
			$output .= '</a></h4>';
			$output .= '</div>';
			$output .= '<div id="collapse-'.get_the_ID().'" class="panel-collapse collapse">';
			$output .= '<div class="panel-body toggle-content post-content">';
			$output .= get_the_content( );
			$output .= '</div>';
			$output .= '</div>';
			$output .= '</div>';
		endwhile;
		wp_reset_postdata( );
		$output .= '</div>';
		$output .= '</div>';
		$output .= '</div>';
		return $output;

	} // END aione_app_builder_faq_shortcode()

	//Function To counts the users 
	public function aione_app_builder_count_users_shortcode($atts ) {
		// Attributes
		$atts =  shortcode_atts(
			array(
				'site' 		=>  $GLOBALS['blog_id'],
				'include'   => '',
				'exclude'   => '',
				'role' 		=> '',
				'roles' 	=> '',
			), $atts,'count_users' );
		
		$output = "";
		
		if( !empty($atts['roles']) ) {
			$roles = explode(",", $atts['roles'] );
		} else {
			$roles = array( );
		}
		if( !empty($atts['include']) ) {
			$include = explode(",", $atts['include'] );
		} else {
			$include = array( );
		}
		if( !empty($atts['exclude']) ) {
			$exclude = explode(",", $atts['exclude'] );
		} else {
			$exclude = array( );
		}
		$args = array(
			'blog_id'      => $atts['site'],
			'role'         => $atts['role'],
			'role__in'     => $roles,
			'role__not_in' => array(),
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'date_query'   => array(),        
			'include'      => $include,
			'exclude'      => $exclude,
			'orderby'      => 'login',
			'order'        => 'ASC',
			'offset'       => '',
			'search'       => '',
			'number'       => '',
			'count_total'  => false,
			'fields'       => 'all',
			'who'          => ''
		 );
		//echo "<pre>";print_r($args );echo "</pre>";
		$users = get_users( $args );
		return count($users );
		
	}

	public function aione_app_builder_users_shortcode( $atts ) {

		// Attributes
		$atts = shortcode_atts( array(
			'site'		=>  $GLOBALS['blog_id'],
			'style'		=> 'table',
			'columns'	=> 'Username',
			'fields'	=> 'user_login',
			'include'	=> array(),
			'exclude'	=> array(),
			'template'	=> '',
			'api'		=> 'no',
			'role'		=> '',
			'roles'		=> array(),
		), $atts, 'users' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";
		
		$args = array(
			'blog_id'      => $atts['site'],
			'role'         => $atts['role'],
			'role__in'     => explode( ",", $atts['roles'] ),
			'role__not_in' => array(),
			'meta_key'     => '',
			'meta_value'   => '',
			'meta_compare' => '',
			'meta_query'   => array(),
			'date_query'   => array(),        
			'include'      => explode( ",", $atts['include'] ),
			'exclude'      => explode( ",", $atts['exclude'] ),
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


		if( $atts['api'] == 'yes' ) {

			$api_array = array( );
			
			foreach ( $users as $api_key => $user ) {

				$fields = array( );
				$fields['data'] 	= $user->data;
				$fields['roles'] 	= $user->roles;

				$post_meta = array( );

				$user_meta = get_user_meta( $user->ID );

				foreach ( $user_meta as $key => $field ) {
					$post_meta[$key] = $field;
				}

				$fields['meta'] = $post_meta;
				$api_array[$api_key] = $fields;
				// $api_array[$api_key]['meta'] = "META";
				
			}
			


			$output .= json_encode( $api_array, JSON_PRETTY_PRINT );

			return $output;


		}
		//echo "<pre>";print_r($users );echo "</pre>";

		$template = $atts['template'];

		if( !empty( $template ) ) {
			$aione_templates		= get_option( 'aione-templates' );
			$aione_template_array 	= $aione_templates[$template];
			$aione_template_content = $aione_template_array['content'];
		}


		
		if( !empty( $aione_template_content ) ) {
			if( !empty( $atts['style'] ) ) {
				$output .= '<div class="aione-template type-users '.$template.'">';
				
			}

			foreach ( $users as $user ) {
				$template_content = $aione_template_content;
				$template_content = str_replace( "{{user_id}}", $user->ID, $template_content );
				$output .= do_shortcode( $template_content );
			}

			if( !empty( $atts['style'] ) ) {
				$output .= '</div>';
			}
		} else {
			if( $atts['style'] == 'table') {
			
				$columns = explode("|", $atts['columns'] ); 
				$fields = explode("|", $atts['fields'] ); 
				
				$output .= '<div class="users aione-table">';
				$output .= '<table class="compact">';
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
						
						if( empty($field) || $field == 'user_pass' ) { 
							$output .= '<td></td>';
						}elseif (preg_match("~\{\{\s*(.*?)\s*\}\}~", $field)) { 
							$field = str_replace("{","",$field );
							$field = str_replace("}","",$field );
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


		}

		return $output;
		
	} // END aione_app_builder_users_shortcode

	public function aione_app_builder_user_shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
			'user_id'		=> '', // ID of user
			'field'			=> 'user_login', //key of field and custom field to be dispayed
			'template'		=> '',
		), $atts, 'user' );

		if( empty( $atts['user_id'] ) ) {

			$current_user = wp_get_current_user( );
			$current_user_id = $current_user->ID;
			$atts['user_id'] = $current_user_id;

		}

		$atts = $this->clean_shortcode_parameters( $atts );

		$template 	= $atts['template'];
		$field 		= $atts['field'];
		$user_id 	= $atts['user_id'];

		if( strtolower( $field ) == 'id' ) {
				//$output .= "===".$atts['user_id'];
				$output .= $user->ID;
		}

		$output = "";

		$user = get_user_by( 'id', $user_id );
		$user = $user->data;

		if( !empty( $template ) ) {
			$aione_templates		= get_option( 'aione-templates' );
			$aione_template_array 	= $aione_templates[$template];
			$aione_template_content = $aione_template_array['content'];
		}
		
		if( !empty( $aione_template_content ) ) {
			$output .= do_shortcode( $aione_template_content );
		} else{
			if( $field == 'user_pass' ) {
				$output .= '';
			} else {
				$output .= $user->$field;
			}
		}
		
		return $output;
		
	} // END aione_app_builder_user_shortcode

	public function aione_app_builder_user_id_shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
			'field'			=> '', //key of field and custom field to be dispayed
			'value'			=> '', //key of field and custom field to be dispayed
		), $atts, 'user_id' );


		$atts = $this->clean_shortcode_parameters( $atts );

		$field 		= $atts['field'];
		$value 		= $atts['value'];
		$user_id 	= get_current_user_id();


		if( !empty( $field ) && !empty( $field ) ) {

			$user = get_user_by( $field, $value );
			$user_id = $user->ID;
		}

		return $user_id;
		
	} // END aione_app_builder_user_id_shortcode

	public function aione_app_builder_user_meta_shortcode( $atts ) {
		
		$atts =  shortcode_atts( array(
			'user_id'			=> '', // ID of user
			'field'				=> 'first_name', //key of field and custom field to be dispayed
			'subfields'			=> '', // field names(slugs) to be displayed
			'subfield_operator'	=> '', // + or - etc
			'check_conditions'	=> 'no', //show field label
			'show_label'		=> 'no', //show field label
			'class'				=> '',
			'style'				=> 'div',
		), $atts, 'user_meta' );

		$user = wp_get_current_user( );
		$current_user_id = $user->ID;

		if( empty( $atts['user_id'] ) ) {
			$atts['user_id'] = $current_user_id;
		}

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";

		$field = get_field_object( $atts['field'], 'user_'.$atts['user_id'] );

		if( $field ) {
			$shortcode = '[post_meta post_id="user_' . $atts['user_id'] . '" field="' . $field['key'] . '" subfields="'.$atts['subfields'].'" subfield_operator="'.$atts['subfield_operator'].'" show_label="' . $atts['show_label'] . '" class="' . $atts['class'] . '" check_conditions="' . $atts['check_conditions'] . '"" style="' . $atts['style'] . '"]';
			$output .=  do_shortcode( $shortcode );
		} else {
			$output .= get_user_meta( $atts['user_id'], $atts['field'], true ); 
		}
 
		return $output;	
	} // END aione_app_builder_user_meta_shortcode



	function aione_app_builder_welcome_shortcode( $atts ) {

		$user = wp_get_current_user( );
		$user_id = $user->ID;

		// Attributes
		$atts = shortcode_atts(
			array(
				'class' => '', 
				'id' => '', 
				'user_id' => $user_id, // ID of user
			), $atts,'welcome' );
		
		$user = get_user_by('id', $atts['user_id'] );



		$output = "<span>Welcome </span>";

		$displayname = $user->user_displayname;

		if( empty( $displayname ) || $displayname == "" ) {

			$first_name = get_user_meta( $atts['user_id'], "first_name", true );

			if( empty( $first_name ) || $first_name == "" ) {

				$nicename = $user->user_nicename;

				if( empty( $nicename ) || $nicename == "" ) {

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


	function aione_app_builder_blog_shortcode( $atts, $content = null ) {
		global $theme_options;
		// Attributes
		$atts = shortcode_atts(
			array(
				'post_type'              => array( 'post' ), // Post Types
				'post_status'            => array( 'publish' ), // Post status
				'order'			   		 => 'DESC', // ASC/DESC Order
				'orderby'			   	 => 'date', // Order By
				'class'			   		 => '', // Custom CSS Class
				'id'				 	 => '', // Custom CSS ID
				'number_posts'			 => '-1', // Number of posts per page
				'offset'			     => '', // Number of post to displace or pass over
				'cat_slug'			     => '', // Category Slug
				'exclude_cats'			 => '', // 
				'show_title'			 => $theme_options['blog_archive_title_enable'], //yes/no. Default=yes
				'title_link'			 => $theme_options['blog_archive_title_link_enable'], //yes/no. Default=yes
				'thumbnail'			  	 => $theme_options['blog_archive_featured_image_enable'], //yes/no. Default=yes
				'excerpt'			  	 => $theme_options['blog_archive_excerpt'], //yes/no. Default=yes
				'excerpt_length'	 	 => $theme_options['blog_archive_excerpt_length'], //Number. Default=55
				'meta_author' 		  	 => $theme_options['blog_archive_author_meta_enable'], //yes/no. Default=yes
				'meta_categories'  	  	 => $theme_options['blog_archive_categories_meta_enable'], //yes/no. Default=yes
				'meta_date' 		  	 => $theme_options['blog_archive_date_meta_enable'], //yes/no. Default=yes
				'meta_date_format' 		  => $theme_options['blog_archive_date_format'], //F j, Y
				'meta_tags'  	  	  	 => $theme_options['blog_archive_tags_meta_enable'], //yes/no. Default=no
				'pagination'			  	 => 'yes',
				'blog_grid_column_spacing'	=> '40',
				'blog_grid_columns'   	=> $theme_options['blog_archive_grid_columns'], //Default=3
				'read_more'   	        => $theme_options['blog_archive_read_more_enable'], //yes/no. Default=yes
				'read_more_text'   		=> $theme_options['blog_archive_read_more_text'], //Text. Default is Read More
				'layout' 			  	=> $theme_options['blog_archive_layout'], // list,grid,timeline. Default =list
			), $atts,'blog' );

		$post_type = $atts['post_type'];
		$post_status = $atts['post_status'];
		$order = $atts['order'];
		$orderby = $atts['orderby'];
		$class = $atts['class'];
		$id = $atts['id'];
		$number_posts = $atts['number_posts'];
		$offset = $atts['offset'];
		$cat_slug = $atts['cat_slug'];
		$exclude_cats = $atts['exclude_cats'];
		$show_title = $atts['show_title'];
		$title_link = $atts['title_link'];
		$thumbnail = $atts['thumbnail'];
		$excerpt = $atts['excerpt'];
		$excerpt_length = $atts['excerpt_length'];
		$meta_author = $atts['meta_author'];
		$meta_categories = $atts['meta_categories'];
		$meta_date = $atts['meta_date'];
		$meta_date_format = $atts['meta_date_format'];
		$meta_tags = $atts['meta_tags'];
		$pagination = $atts['pagination'];
		$blog_grid_column_spacing = $atts['blog_grid_column_spacing'];
		$blog_grid_columns = $atts['blog_grid_columns'];
		$read_more = $atts['read_more'];
		$read_more_text = $atts['read_more_text'];
		$layout = $atts['layout'];

		//setting up categories to be used .Transform slugs to ids
		$cat_ids ='';
		$categories = explode( ',' , $cat_slug );
		if ( isset( $categories ) && 
			$categories 
		) {
			foreach ( $categories as $category ) {
				
				$id_obj = get_category_by_slug( $category );
				
				if ( $id_obj ) {
					if ( strpos( $category, '-' ) === 0 ) {
						$cat_ids .= '-' . $id_obj->cat_ID . ',';
					} else {
						$cat_ids .= $id_obj->cat_ID . ',';
					}
				}
			}
		}
		$cat = substr( $cat_ids, 0, -1 );

		//check for categories to exclude.Transform slugs to ids
		$cats_to_exclude = explode( ',' , $exclude_cats );
		$cats_id_to_exclude = array( );
		if ( $cats_to_exclude ) {
			foreach ( $cats_to_exclude as $cat_to_exclude ) {
				$id_obj = get_category_by_slug( $cat_to_exclude );
				if ( $id_obj ) {
					$cats_id_to_exclude[] = $id_obj->term_id;
				}
			}
		}

		if ( $number_posts == -1 ) {
			$pagination = 'no';
		}

		$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;
		// WP_Query arguments
		$args = array(
			'post_type'              => explode(",",$post_type),
			'post_status'            => explode(",",$post_status),
			'order'			   		 => $order,
			'orderby'			   	 => $orderby,
			'posts_per_page'		 => $number_posts,
			'offset'		         => $offset,
			'paged' 				 => $paged,
			'cat'		             => $cat,
			'category__not_in'		 => $cats_id_to_exclude,
		 );

		( $show_title == "yes" || $show_title == "1" ) ? ( $show_title = TRUE ) : ( $show_title = FALSE );
		( $title_link == "yes" || $title_link == "1" ) ? ( $title_link = TRUE ) : ( $title_link = FALSE );
		( $thumbnail == "yes" || $thumbnail == "1" ) ? ( $thumbnail = TRUE ) : ( $thumbnail = FALSE );
		( $excerpt == "yes" || $excerpt == "1" ) ? ( $excerpt = TRUE ) : ( $excerpt = FALSE );
		( $read_more == "yes" || $read_more == "1" ) ? ( $read_more = TRUE ) : ( $read_more = FALSE );
		( $meta_author == "yes" || $meta_author == "1" ) ? ( $meta_author = TRUE ) : ( $meta_author = FALSE );
		( $meta_categories == "yes" || $meta_categories == "1" ) ? ( $meta_categories = TRUE ) : ( $meta_categories = FALSE );
		( $meta_date == "yes" || $meta_date == "1" ) ? ( $meta_date = TRUE ) : ( $meta_date = FALSE );
		( $meta_tags == "yes" || $meta_tags == "1" ) ? ( $meta_tags = TRUE ) : ( $meta_tags = FALSE );
		( $pagination == "yes" ) ? ( $pagination = TRUE ) : ( $pagination = FALSE );
		
		
		$blog_query = new WP_Query( $args );
		$output = "";
		//echo "<pre>";print_r($args );echo "</pre>";
		if ( $blog_query->have_posts() ) { 
			while ( $blog_query->have_posts() ) : 
				$blog_query->the_post( ); 
				$post_id = get_the_ID( );

				if( $thumbnail ) {
					$attachment_ids = array( );
					if ( get_post_thumbnail_id( $post_id ) ) {
						$attachment_ids[] = get_post_thumbnail_id( $post_id );
					}
				}				
				($thumbnail && !empty($attachment_ids)) ? ( $has_thumbnail = TRUE ) : ( $has_thumbnail = FALSE );

				$meta_info_combined = '';
				if( $meta_date ) {
					$time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time>';

					$time_string = sprintf( $time_string,
						esc_attr( get_the_date( 'c' ) ),
						esc_html( get_the_date($meta_date_format) )
					 );
					$posted_on = sprintf(
						esc_html_x( 'Posted on %s', 'post date', 'gutenbergtheme' ),
						'<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
					 );
					$meta_info_combined .= '<span class="posted-on">' . $posted_on . '</span>';
				}
				if( $meta_author ) {
					$byline = sprintf(
						esc_html_x( 'by %s', 'post author', 'gutenbergtheme' ),
						'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
					 );
					$meta_info_combined .= ' <span class="byline">' . $byline . '</span>';
				}
				if( $meta_categories ) {
					if ( 'post' === get_post_type() ) {
						$categories_list = get_the_category_list( esc_html__( ', ', 'gutenbergtheme' ) );
						if ( $categories_list ) {
							$incat = sprintf( ' <span class="cat-links">' . esc_html__( 'Posted in %1$s', 'gutenbergtheme' ) . '</span>', $categories_list ); 
							$meta_info_combined .= $incat;
						}
					}
				}
				if( $meta_tags ) {
					if ( 'post' === get_post_type() ) {
						$tags_list = get_the_tag_list( '', esc_html_x( ', ', 'list item separator', 'gutenbergtheme' ) );
						if ( $tags_list ) {
							$tags = sprintf( ' <span class="tags-links">' . esc_html__( 'Tagged %1$s', 'gutenbergtheme' ) . '</span>', $tags_list ); 
							$meta_info_combined .= $tags;
						}
					}
				}
				$id_attribute ='';
					if( !empty($id) ) {
						$id_attribute = 'id="'.$id.'"';
					}
				$output .='<div '.$id_attribute.' class="'.$class.'">
				<article id="'.$id.' post_'. get_the_ID().'" '. get_post_class().'> 
				<div class="ar list-blog '. $layout.'">';
				
				if( $has_thumbnail) { 
					
					$output .='<div class="ac s100 m50 l40">
					<div class="featured-image aione-rounded">
					'. get_the_post_thumbnail( $post_id,'medium' ).'	
					</div>
					</div>
					<div class="ac s100 m50 l60">
					';
				}else {
					
					$output .='<div class="ac s100 m100 l100">';
					
				}
				
				if( $show_title) { 
					
					$output .='<header class="entry-header">';
					$output .= '<h1 class="entry-title">';
					if( $title_link ) {
						$output .= '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">';
					}
					$output .= get_the_title($before,$after );
					if( $title_link ) {
						$output .= '</a>';
					}
					$output .= '</h1>';
					
					if( $meta_info_combined ) {
						
						$output .='<div class="entry-meta">
						'. $meta_info_combined .'
						</div>';
						
					}
					
					$output .='</header>';
					
				}
				

				$output .='<div class="entry-content"> ';
				if( $excerpt ) {
					$output .= wp_trim_words( wp_strip_all_tags( get_the_content() ), $excerpt_length, '...' );
				} else {
					$output .= wp_strip_all_tags( get_the_content() );
				}
				
				$output .='</div>';
				
				if( $read_more ) {
					
					$output .='<footer class="entry-footer">
					<a class="read-more-link" href="'. get_permalink().'">'. $read_more_text.'</a>
					</footer>';
					
				}
				
				$output .='</div>
				</div>
				</article>
				</div>';	
				
			endwhile;
			wp_reset_postdata( );
			// Get the pagination
			if( $pagination ) {
				$output .= aione_pagination( $blog_query );
			}			
		}else{

		}
		wp_reset_query( );

		return $output;	
		
	} //END aione_app_builder_blog_shortcode

	/**
	* Shortcode [icon]
	* 
	*/
	function aione_app_builder_aione_icon_shortcode( $atts ) {
		$atts =  shortcode_atts( array(
			'name' => 'logo-facebook',
			'size'	=> 'small',
			'src'	=> '',
			'class'	=> 'aione-icon',
		), $atts, 'icon' );

		$output = ''; 

		$aione_src = $atts['src'];
		$aione_icon = $atts['name'];
		$aione_icon_size = $atts['size'];
		$aione_icon_class = $atts['class'];

		if( $aione_src ) {
			$output .= '<span class="' . $aione_icon_class . '"><ion-icon src="'.$aione_src.'" size="'.$aione_icon_size.'"></ion-icon></span>';
		} else {
			if( $aione_icon ) {
				$output .= '<span class="' . $aione_icon_class . '"><ion-icon name="'.$aione_icon.'" size="'.$aione_icon_size.'"></ion-icon></span>';
			}
		}

		

		return $output;
	} 
	/**
	* Shortcode [date]
	* 
	*/
	function aione_app_builder_date_shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'format' => 'jS F Y h:i:s',
			'time-zone' => 'Asia/Kolkata',			
			'style' => '' //div/ul/span/
		), $atts, 'date' );

		$output = '';
		global $post;

		if ( in_the_loop() ) {
			if( $atts['format'] == 'human' ) {				
				$output .=  $this->human_readable_date( get_the_date() );
			} else{
				if(!empty( $atts['style'] )){
						$arr = str_split($atts['format']);
						if(!empty($arr)){
							foreach($arr as $val){
								$output .= '<' . $atts['style'] . '>';
									$output .= get_the_date( $val );
								$output .= '</' . $atts['style'] . '>';
							}
						} else{
							$output .= get_the_date( $atts['format'] );
						}
				}else{
					$output .= get_the_date( $atts['format'] );
				}
				
			}
		} else{
			//$output = date($atts['format'] );
			$tz = $atts['time-zone'];
			$timestamp = time( );
			$dt = new DateTime("now", new DateTimeZone($tz) ); //first argument "must" be a string
			$dt->setTimestamp($timestamp ); //adjust the object to correct timestamp
			$output .=  $dt->format( $atts['format'] );

		}

		return $output;
	}
	/**
	* Shortcode [info]
	* 
	*/
	function aione_app_builder_info_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'field' => 'name',
		), $atts, 'info' );

		$atts = $this->clean_shortcode_parameters( $atts );
		$field = $atts['field'];

		$output = '';

		$output = get_bloginfo( $field, $filter );

		return $output;
	}

	/**
	* Shortcode [contact_info]
	* 
	*/
	function aione_app_builder_contact_info_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'field' => 'name',
			'style' => '',
			'class' => '',
			'id' 	=> '',
		), $atts, 'contact_info' );

		$atts = $this->clean_shortcode_parameters( $atts );
		$field = $atts['field'];
		$field_value = '';

		$contact_data = get_option( 'content_form_data' );

		$output = '';

		switch ( $field ) {
			case 'contact_about':
				$contact_about = $contact_data['field_609a09ec7184c'];

				$field_value = $contact_about;
				
			break;

			case 'contact_number':
				$contact_number = $contact_data['field_609bb50f73674'][0]['field_609bb53273675'];

				$contact_number_formated = str_replace(" ","", $contact_number );
				$contact_number_formated = str_replace("-","", $contact_number_formated );
				$contact_number_formated = str_replace("(","", $contact_number_formated );
				$contact_number_formated = str_replace(")","", $contact_number_formated );

				$field_value = '<a href="tel:' . $contact_number_formated . '" target="_blank">' . $contact_number . '</a>';
				
			break;

			case 'contact_email':
				$contact_email = $contact_data['field_609bb50f73674'][0]['field_609bb57a73676'];

				$contact_email_formated = str_replace(" ","", $contact_email );

				$field_value = '<a rel="noreferrer noopener" href="mailto:' . $contact_email_formated . '" target="_blank">' . $contact_email . '</a>';
				
			break;

			
			case 'contact_address':
				$contact_address = $contact_data['field_609bb50f73674'][0]['field_609bb59c73677'];

				$contact_address_formated = str_replace(","," ", $contact_address );
				$contact_address_formated = str_replace("  "," ", $contact_address_formated );
				$contact_address_formated = str_replace(" ","+", $contact_address_formated );

				$field_value = '<a rel="noreferrer noopener" href="https://www.google.com/maps/search/?api=1&query=' . $contact_address_formated . '" target="_blank">' . $contact_address . '</a>';
			break; 

			case 'contact_website':
				$contact_website = home_url( );

				$contact_website_formated = str_replace("http://","", $contact_website );
				$contact_website_formated = str_replace("https://","", $contact_website_formated );
				$contact_website_formated = str_replace("/","", $contact_website_formated );
				$contact_website_formated = str_replace("www","", $contact_website_formated );

				$field_value = '<a href="https://' . $contact_website_formated . '">www.' . $contact_website_formated . '</a>';
			break;

			case 'contact_social':

				if( empty( $atts['class'] ) ) {

					$atts['class'] = 'aione-social-icons small colored circle';

				} else{

					$atts['class'] = 'aione-social-icons ' . $atts['class'];

				}

				if( empty( $atts['style'] ) ) {

					$atts['style'] = 'ul';

				}

				$array_count = count($contact_data['field_609a075a71842'] );

				for($i = 0; $i < $array_count; $i++) {
					$field_value .= '<li class="'.$contact_data['field_609a075a71842'][$i]['field_609a077271843'].'">';

					$field_value .= '<a href="'.$contact_data['field_609a075a71842'][$i]['field_609a07a771844'].'"  aria-label="'.$contact_data['field_609a075a71842'][$i]['field_609a077271843'].'" target="_blank" rel="noopener noreferrer">'; 

					$field_value .= '<span class="icon"></span>';

					$field_value .= '<span class="label" >'.$contact_data['field_609a075a71842'][$i]['field_609a077271843'].'</span>';

					$field_value .= '</a>';

					$field_value .= '</li>';

				}

			break;

			case 'contact_offices':

				if( $contact_data['field_609bb60e73678'] == 'yes' ) {

					if( empty( $atts['class'] ) ) {

						$wrapper_class = 'bg-white p-20 border-radius-10 shadow';
						$atts['class'] = 'ar';

					} else{

						$wrapper_class = $atts['class'];
						$atts['class'] = 'ar';

					}
					
					if( empty( $atts['style'] ) ) {

						$atts['style'] = 'div';

					}

					$office_count = count( $contact_data['field_609a011c7183e'] );

					if( $office_count == 1 ) { $column_class = 'l100'; }
					if( $office_count == 2 ) { $column_class = 'l50'; }
					if( $office_count % 3 == 0 ) { $column_class = 'l33'; }
					if( $office_count % 4 == 0 ) { $column_class = 'l25'; }
					if( $office_count == 5 ) { $column_class = 'l33'; }
					if( $office_count == 7 ) { $column_class = 'l25'; }
					if( $office_count == 10 ) { $column_class = 'l25'; }
					if( $office_count == 11 ) { $column_class = 'l33'; }
					if( $office_count == 13 ) { $column_class = 'l33'; }
					if( $office_count == 14 ) { $column_class = 'l33'; }
					if( $office_count > 16 ) { $column_class = 'l25'; }


					for( $i = 0; $i < $office_count; $i++ ) {

						$field_value .= '<div class="ac ' . $column_class . ' m100 s100 mb-26">';
						$field_value .= '<div class="wrapper ' . $wrapper_class . '">';

						if( !empty( $contact_data['field_609a011c7183e'][$i]['field_60c067d54e995'] ) ) {

							$field_value .= '<h3 class="font-size-18 mb-12">' . $contact_data['field_609a011c7183e'][$i]['field_60c067d54e995'] . '</h3>';

						}

						if( !empty( $contact_data['field_609a011c7183e'][$i]['field_609a014e7183f'] ) ) {

							$contact_number = $contact_data['field_609a011c7183e'][$i]['field_609a014e7183f'];

							$contact_number_formated = str_replace(" ","", $contact_number );
							$contact_number_formated = str_replace("-","", $contact_number_formated );
							$contact_number_formated = str_replace("(","", $contact_number_formated );
							$contact_number_formated = str_replace(")","", $contact_number_formated );

							$field_value .= '<span class="primary float-left font-size-24 line-height-30"><ion-icon name="call-sharp"></ion-icon></span>';
							// $field_value .= '<p class="pl-50 mb-0"><strong>Contact Number</strong></p>';
							$field_value .= '<p class="pl-32 mb-10"><a href="tel:' . $contact_number_formated . '" target="_blank">'.$contact_data['field_609a011c7183e'][$i]['field_609a014e7183f'].'</a></p>';

						}

						if( !empty( $contact_data['field_609a011c7183e'][$i]['field_609a017f71840'] ) ) {


							$contact_email = $contact_data['field_609a011c7183e'][$i]['field_609a017f71840'];

							$contact_email_formated = str_replace(" ","", $contact_email );

							$field_value .= '<span class="primary float-left font-size-24 line-height-30"><ion-icon name="mail-sharp"></ion-icon></span>';
							// $field_value .= '<p class="pl-50 mb-0"><strong>Email Address</strong></p>';
							$field_value .= '<p class="pl-32 mb-10"><a rel="noreferrer noopener" href="mailto:' . $contact_email_formated . '" target="_blank">'.$contact_email.'</a></p>'; 

						}

						if( !empty( $contact_data['field_609a011c7183e'][$i]['field_609a024071841'] ) ) {

							$contact_address = $contact_data['field_609bb50f73674'][0]['field_609bb59c73677'];

							$contact_address_formated = str_replace(","," ", $contact_address );
							$contact_address_formated = str_replace("  "," ", $contact_address_formated );
							$contact_address_formated = str_replace(" ","+", $contact_address_formated );

							$field_value .= '<span class="primary float-left font-size-24 line-height-30"><ion-icon name="location-sharp"></ion-icon></span>';
							// $field_value .= '<p class="pl-50 mb-0"><strong>Address</strong></p>';
							$field_value .= '<p class="pl-32 mb-10"><a rel="noreferrer noopener" href="https://www.google.com/maps/search/?api=1&query=' . $contact_address_formated . '" target="_blank">'.$contact_address.'</a></p>'; 

						}

						$field_value .= '</div>';
						$field_value .= '</div>';

					}

				}

			break;
			

			default:
				$field_value .= '';
		}

		if( !empty( $atts['class'] ) ) {
			$class_attribute = 'class="' . $atts['class'] . '"';
		}

		if( !empty( $atts['id'] ) ) {
			$id_attribute = 'id="' . $atts['id'] . '"';
		}


		if( !empty( $atts['style'] ) ) {
			
			$output .= '<' . $atts['style'] . ' ' . $id_attribute . ' '. $class_attribute . '>';
			$output .= $field_value;
			$output .= '</' . $atts['style'] . '>';

		} else{

			$output .= $field_value;

		}

		return $output;
	}


		/**
	* Shortcode [contact_info]
	* 
	*/
	function aione_app_builder_filters_shortcode( $atts ) {
		$atts = shortcode_atts( array(
			'field' => 'name',
			'style' => '',
			'class' => '',
			'id' 	=> '',
		), $atts, 'filters' );

		
		$output = '';

		global $theme_options;
		global $post;
		global $wp_query;


		// _to_be_deleted
		// echo "<br>ID = ".$post->ID;
		/*$posts_page_id = get_option( 'page_for_posts' );


		echo "*****************".$posts_page_id;*/

		$post_id 				= $post->ID;
		$post_type 				= get_post_type( $post_id );
		
		$aione_components 		= get_option( 'aione-components' );
		$aione_component 		= $aione_components[$post_type];
		$archive_template_slug 	= $aione_component['archive_template'];
		$filter_columns 		= $aione_component['filters'];

		if( $_POST['submit']) { 
			$data = json_encode($_POST['acf'] ); ?>
			<script>
				// var filters = [];
				var filters = <?php echo $data; ?>;
				var post_id = <?php echo json_encode($post_id ); ?>;
				var query = <?php echo json_encode($wp_query ); ?>;
				console.log('filters')
				console.log(filters)
				console.log('post_id')
				console.log(post_id)

				$.ajax({
			        type: 'POST',
			        url: '<?php echo admin_url('admin-ajax.php' );?>',
			        data: { action : 'get_ajax_filters', post_id : post_id, filters : filters, query:query.query },
			        success: function( response ) {
			        	//console.log('response')
			        	//console.log(response)
			        	// response = JSON.parse(response );
			        	//console.log(JSON.parse(response))
			            // $( '.aione-page-content > .wrapper' ).html( response ); 
			            $( '.content' ).html( response ); 
			        }
			    } );
			</script>
		<?php }
		// $aione_templates 		= get_option( 'aione-templates' );
		// $aione_template_archive = $aione_templates[$archive_template_slug]['content'];

		// $output .= 'post_id = '.$post_id.'<br>';
		// $output .= 'post_type = '.$post_type;
		// $output .= '<br>archive_template_slug = '.$archive_template_slug;
		/*$output .= '<pre>';
		$output .= print_r( $filter_columns , true );
		$output .= '</pre>';*/
		// $output .= 'aione_template_archive = '.$aione_template_archive;

		$fields = implode(',', array_keys( $filter_columns ) );
		$output .= do_shortcode('[form form="false" fields='.$fields.']' );



		return $output;
	}


	

	public function aione_app_builder_post_id_shortcode( $attr, $content = null ) {

		global $post;

		$output = $post->ID;

		return $output;
	}

	public function aione_app_builder_post_name_shortcode( $attr, $content = null ) {

		global $post;

		$atts = shortcode_atts( array(
			'post_id'	=>	$post->ID,
		), $atts, 'post_name' );

		$output = "";

		$atts = $this->clean_shortcode_parameters( $atts );
		$post_id = $atts['post_id'];

		if( !empty( $post_id ) ) {

			$post= get_post( $post_id ); 
			$output .= $post->post_name;
		}

		return $output;
	}

	public function aione_app_builder_post_link_shortcode( $atts, $content = null ) {

		global $post;

		$atts = shortcode_atts( array(
			'post_id'	=>	$post->ID,
		), $atts, 'link' );

		$output = "";

		$atts = $this->clean_shortcode_parameters( $atts );
		$post_id = $atts['post_id'];
		
		if( !empty( $post_id ) ) {

			$output .= get_permalink( $post_id );

		}

		return $output; 
	}


	function aione_app_builder_post_title_shortcode( $atts ) {

		global $post;
		
		$atts = shortcode_atts( array(
			'link'		=>	"true",
			'post_id'	=>	$post->ID,
			'class'		=>	'',
			'id'		=>	'',
			'style'		=>	'div', //h1,h2,h3,h4,h5,h6,span
			'stripslashes' => 'no', //yes,no
		), $atts, 'title' );

		$atts = $this->clean_shortcode_parameters( $atts );
		$title = "";
		$id_attribute ='';
		if( !empty($atts['id']) ) {
			$id_attribute = 'id="'.$atts['id'].'"';
		}

		$post_id = $atts['post_id'];

		if( !empty($post_id) ) {

			$post_object = get_post( $post_id ); 
			$post_title = $post_object->post_title; 
			if( $atts['stripslashes'] == 'yes') {
				// $post_title = addslashes($post_title );
				$post_title = htmlspecialchars($post_title, ENT_QUOTES, 'UTF-8' );
			}

			if( $atts['style'] != "" ) {
				$title .= '<'.$atts['style'].' '.$id_attribute.' class="'.$atts['class'].'">';
			}
			if( $atts['link'] == "true" ) {
				if( $atts['style'] != "" ) {
					$title .= '<a  href="'.get_permalink( $post_id ).'">'.$post_title.'</a>';
				} else {
					$title .= '<a '.$id_attribute.' class="'.$atts['class'].'" href="'.get_permalink( $post_id ).'">'.$post_title.'</a>';
				}
				
			} else {
				$title .= $post_title;
			}

			if( $atts['style'] != "" ) {
				$title .= '</'.$atts['style'].'>';
			}
		}
		
		return $title;
	}

	function aione_app_builder_post_content_shortcode( $atts ) {

		global $post;

		$atts = shortcode_atts( array(
			'excerpt'			=> 'false',
			'excerpt_length'	=> '30',
			'more_text'			=> '...',
			'post_id'			=> $post->ID,
			'style'				=> 'div', //div, p 
			'class'				=> '',
			'id'				=> '',
			'stripslashes' 		=> 'no', //yes,no
		), $atts, 'content' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$content = '';
		$output = '';

		$id_attribute ='';
		if( !empty($atts['id']) ) {
			$id_attribute = 'id="'.$atts['id'].'"';
		}

		$post_id = $atts['post_id'];

		$post_object = get_post( $post_id ); 
		$post_content = $post_object->post_content; 
		if( $atts['stripslashes'] == 'yes') {
			// $post_content = addslashes($post_content );
			$post_content = htmlspecialchars($post_content, ENT_QUOTES, 'UTF-8' );
		}

		if( $atts['excerpt'] == "true" ) {
			$content = wp_trim_words( $post_content, $atts['excerpt_length'], $atts['more_text'] );
		} else {
			$content = $post_content;
		}

		if( $atts['style'] != "" ) {
			$output .= '<'.$atts['style'].' '.$id_attribute.' class="'.$atts['class'].'">';
		}

		$output .= do_shortcode( $content );

		if( $atts['style'] != "" ) {
			$output .= '</'.$atts['style'].'>';
		}

		return $output;

	}

	function aione_app_builder_author_shortcode( $atts ) {
		
		$atts = shortcode_atts( array(
			'class' => '',
			'id' => '',
			'style'	=> 'div', //div, p 
		), $atts, 'author' );

		$author_id =  get_the_author_meta( 'ID' );

		if( $atts['style'] != "" ) {

			$output .= '<'.$atts['style'].' '.$atts['id'].' class="'.$atts['class'].'">'.$author_id.'</'.$atts['style'].'>';

		} else{

			$output .= $author_id;

		}

		return $output;
	}

	function aione_app_builder_post_featured_image_shortcode($atts ) {
		global $post;
		$atts = shortcode_atts( array(
			'post_id' => $post->ID,
			'size' => 'full',
			'class' => '',
			'id' => '',
			'placeholder' => 'true',
			'placeholder_image' => plugin_dir_url( __FILE__ ) . 'images/user.svg',
			'return' => 'image',// image/url/width/height/alt
		), $atts, 'featured_image' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = "";
		$id_attribute ='';
		$post_id = $atts['post_id'];

		if( !empty(	$atts['id']	) ) {
			$id_attribute = 'id="'.$atts['id'].'"';
		}
		
		if ( has_post_thumbnail( $post_id ) ) {

			$featured_image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ),  $atts['size'] );
			$featured_image_url = $featured_image[0];
			$featured_image_width = $featured_image[1];
			$featured_image_height = $featured_image[2];
			$featured_image_meta = wp_get_attachment_metadata( get_post_thumbnail_id( $post_id ),  true );
			$featured_image_meta_alt = get_post_meta( get_post_thumbnail_id( $post_id ), '_wp_attachment_image_alt' );

			
			/*
			$output .= "<pre>";
			$output .= print_r($featured_image, true );
			$output .= "</pre>";
			*/
			

			$image_meta = $featured_image_meta['image_meta'];

			/*
			$image_meta['aperture']
			$image_meta['credit']
			$image_meta['camera']
			$image_meta['caption']
			$image_meta['created_timestamp']
			$image_meta['copyright']
			$image_meta['focal_length']
			$image_meta['iso']
			$image_meta['shutter_speed']
			$image_meta['title']
			$image_meta['orientation']
			$image_meta['keywords']
			*/

			if( $atts['return'] == 'image' ) {
				$output .= '<img class="'.$atts['class'].'" '.$id_attribute.' src="'.$featured_image_url.'" alt="'.$featured_image_meta_alt.'" width="'.$featured_image_width.'"/>';
			} elseif( $atts['return'] == 'url' ) {
				$output .= $featured_image_url;
			} elseif( $atts['return'] == 'width' ) {
				$output .= $featured_image_width;
			} elseif( $atts['return'] == 'height' ) {
				$output .= $featured_image_height;
			} elseif( $atts['return'] == 'alt' ) {
				$output .= $featured_image_meta_alt;
			}
		} else {

			if( $atts['placeholder'] == 'true' && !empty( $atts['placeholder_image'] ) ) {

				$output .= '<img class="'.$atts['class'].'" '.$id_attribute.' src="'.$atts['placeholder_image'].'" />';
				
			}

		}

		return $output;

	}

	function aione_app_builder_post_attached_media_shortcode($atts ) {
		global $post;
		$atts = shortcode_atts( array(
				'class' => '',
				'id' => '',
				'width' => '',
				'height' => '',
				'caption' => 'true',
			), $atts, 'attached_media' 
		 );
		$output = "";
		$class = $atts['class'];
		$id = $atts['id'];
		$width = $atts['width'];
		$height = $atts['height'];
		$caption = $atts['caption'];
		$type = get_post_mime_type( $post->ID );
		$media = wp_get_attachment_url( $post->ID );
		$caption_text = wp_get_attachment_caption( $post->ID );
		$id_attribute ='';
		if( !empty($id) ) {
			$id_attribute = 'id="'.$id.'"';
		}
		
		switch ($type) {
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
			$output .= '<img src="'.$media.'" width="'.$width.'" height="'.$height.'" class="'.$class.'" '.$id_attribute.'/>';
			if( $caption == 'true' ) {				
				$output .= '<div class="caption">'.$caption_text.'</div>';
			}
			break;
			case 'video/mpeg':
			case 'video/mp4': 
			case 'video/quicktime':
			$output .= '<video width="'.$width.'" height="'.$height.'" controls class="'.$class.'" '.$id_attribute.'>
			<source src="'.$media.'" type="'.$type.'">
			</video>'; 
			if( $caption == 'true' ) {	
				$output .= '<div class="caption">'.$caption_text.'</div>';
			}
			break;
			case 'text/csv':
			case 'text/plain': 
			case 'text/xml':
			$output .= 'CSV/Plain/XML view not supported'; 
			default:
			$output .= 'Unknown File type';
		}
		return $output;
	}

	function aione_app_builder_post_tags_shortcode($atts ) {
		global $post;
		$atts = shortcode_atts( array(
			'post_id' => $post->ID,
			'texonomy' => 'post_tag',
			'style' => 'list',
			'class' => 'aione-tags',
			'id' => 'aione_tags_'.$post->ID,
		), $atts, 'tags' );

		$output = "";
		$output .= $this->aione_app_builder_get_post_terms( $atts['post_id'], $atts['texonomy'], $atts['style'], $atts['class'], $atts['id'] );
		return $output;
	}

	function aione_app_builder_post_categories_shortcode($atts ) {
		global $post;
		$atts = shortcode_atts( array(
			'post_id' => $post->ID,
			'texonomy' => 'category',
			'style' => 'list',
			'class' => 'aione-categories',
			'id' => 'aione_categories_'.$post->ID,
		), $atts, 'categories' );

		$output = "";
		$output .= $this->aione_app_builder_get_post_terms( $atts['post_id'], $atts['texonomy'], $atts['style'], $atts['class'], $atts['id'] );
		return $output;
	}

	function aione_app_builder_get_post_terms($post_id, $texonomy, $style, $class, $id ) {
		$output = "";

		$args = array(
			'orderby' => 'name', 
			'order' => 'ASC', 
			'fields' => 'all'
		 );
		$post_terms = wp_get_post_terms( $post_id, $texonomy, $args ); 
		$id_attribute ='';
		if( !empty($id) ) {
			$id_attribute = 'id="'.$id.'"';
		}
		if ( $post_terms ) {
			if( $style == 'list' ) {
				$parent_element = "ul";
				$child_element = "li";
			} else {
				$parent_element = "div";
				$child_element = "span";
			}
			$output .= '<'.$parent_element.' class="'.$class.'" '.$id_attribute.'>';
			foreach( $post_terms as $term ) {
				$term_link = get_term_link( $term );
				$output .= '<'.$child_element.'><a href="' . esc_url( $term_link ) . '">' . $term->name . '</a></'.$child_element.'>';
			}
			$output .= '</'.$parent_element.'>';
		}
		return $output;
	}


	function aione_app_builder_variable_shortcode( $atts ) {

		$atts = shortcode_atts( array(
			'parameter' => '',
			'type' => 'request', //get/post/request
		), $atts, 'variable' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$variable = $_REQUEST[$atts['parameter']];
		$variable = trim( $variable );

		return $variable;

	}

	function aione_app_builder_if_shortcode( $atts, $content ) {

		$atts = shortcode_atts( array(
			'condition' => ''
		), $atts, 'if' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$condition = $atts['condition'];
		$condition = trim( $condition );

		if( $condition ) {
			return do_shortcode( $content );
		}

	}



	function aione_app_builder_fields_shortcode($atts ) {

		$atts = shortcode_atts( array(
			'post_id' 			=> '',
			'field_groups' 		=> '',
			'include_fields' 	=> '',
			'exclude_fields' 	=> '',
			'check_conditions' 	=> 'yes',
			'show_label' 		=> "no",
			'class' 			=> '',
			'id' 				=> '',
			'style' 			=> 'div', // table/div/list
		), $atts, 'fields' );


		$output = "";

		global $post;

		if( empty( $atts['post_id'] ) ) {
			$atts['post_id'] = $post->ID;
		}

		$atts = $this->clean_shortcode_parameters( $atts );

		if( function_exists( 'acf_get_field_groups' ) ) {

			$fieldGroup 	= acf_get_field_group( $atts['field_groups'] );
			$fields 		= acf_get_fields_by_id( $atts['field_groups'] );

			foreach ( $fields as $key => $field ) {

				if( $atts['check_conditions'] == 'yes' ) {
					$skip_field = $this->check_field_conditions( $field, $atts['post_id'] );
				}

				if( !$skip_field ) {
					$output .= do_shortcode('[post_meta post_id="' . $atts['post_id'] . '" field="' . $field['key'] . '" show_label="' . $atts['show_label'] . '" class="' . $atts['class'] . '" style="' . $atts['style'] . '"]' );
				}
			}
			

		}

		return $output;
	}

	function aione_app_builder_post_meta_shortcode( $atts ) {

		global $post;

		$atts = shortcode_atts( array(
			'post_id' 			=> $post->ID,
			'field'				=> '', //field name(slug) or field_key
			'subfields'			=> '',// field names(slugs) to be displayed
			'check_conditions' 	=> 'no',
			'show_label'		=> 'no',
			'style'				=> 'div', // table/div/list/ Leave empty for no html
			'class'				=> '',
			'stripslashes'		=> 'no' //yes,no
		), $atts, 'post_meta' );

		$atts = $this->clean_shortcode_parameters( $atts );
		$output = '';

		if( !empty( $atts['post_id'] ) ) {


			$field = get_field_object( $atts['field'], $atts['post_id'] );
			if( $atts['stripslashes'] == 'yes') {
				// $field = addslashes($field );
				$field = htmlspecialchars($field, ENT_QUOTES, 'UTF-8' );
			}

			if( empty( $field) ) {
				$output .= get_post_meta( $atts['post_id'], $atts['field'], true );
				return $output;	
			} 

			if( $atts['check_conditions'] == 'yes' ) {
				// $output = '<br>Field Name : '.$field['name'];

				$skip_field = $this->check_field_conditions( $field, $atts['post_id'] );
				if( $skip_field ) {
					return $output;
				}
			}

			$field_class = 'field_'.$field['name'];

			if( empty( $field_class ) ) {
				$field_class = $field['key'];
			}

			$field_id = $field['wrapper']['id'];

			if( empty( $field_id ) ) {
				$field_id = $field['key'];
			}

			$field_classes = array(
				'field',
				$atts['class'],
				$field_class,
				$field['wrapper']['class'],
				'field_type_' . $field['type'],
			 );

			$field_classes = implode( ' ', $field_classes );

			if( $atts['style'] == "div" ) {
				$output .= '<div id="' . $field_id . '" class="' . $field_classes . '">';
			}

			if( $atts['show_label'] == "yes" ) {
				$output .= '<label class="field-label">' . $field['label'] . '</label>';
			}

			if( $field['type'] == 'repeater' ) {

				$repeater 	= true;
				$layout 	= $field['layout'];
				$style 		= $atts['style'];
				$sub_fields = $field['sub_fields'];
				$subfields 	= $atts['subfields'];

				if( !empty( trim( $subfields ) ) ) {
					$subfields 	= explode( ',', $subfields );
				}

				$repeater_output 	= '';
				$start_html 		= '';
				$end_html 			= '';
				$row_start_html 	= '';
				$row_end_html 		= '';
				$column_html_tag 	= '';

				$aione_data_table_headers 	= array( );
				$aione_data_table_data 		= array( );


				if( have_rows( $field['key'], $atts['post_id'] ) ) {

					

					if( $style == 'aione_data_table' ) {
						foreach ( $sub_fields as $sub_field_key => $sub_field_value ) {
							/*
							if( !empty( $subfields ) ) {
								if ( !in_array( $sub_field_value['name'], $subfields ) ) {
									continue;
								}
							}
							*/
							$aione_data_table_headers[] = $sub_field_value['label'];
						}
					} elseif( $layout == 'table' ) {
						if( !empty( $style ) ) {
							$start_html 		.= '<div class="repeater-layout-'.$layout.' aione-table">';
							$start_html 		.= '<table>';

							if( $atts['show_label'] == "yes" ) {
								$start_html 		.= '<thead>';
								$start_html 		.= '<tr>';
								foreach ( $sub_fields as $sub_field_key => $sub_field_value ) {
									if( !empty( $subfields ) ) {
										if ( !in_array( $sub_field_value['name'], $subfields ) ) {
											continue;
										}
									}
									$start_html 		.= '<th>';
									$start_html 		.= $sub_field_value['label'];
									$start_html 		.= '</th>';
								}
								$start_html 		.= '<tr>';
								$start_html 		.= '</thead>';
								$atts['show_label'] = "no";
							}

							$start_html 		.= '<tbody>';
							$end_html 			.= '</tbody>';
							$end_html 			.= '</table>';
							$end_html 			.= '</div>';
							$row_start_html 	.= '<tr>';
							$row_end_html 		.= '</tr>';
							$column_html_tag 	.= 'td';
						}
					} elseif( $layout == 'block' ) {
						if( !empty( $style ) ) {
							$start_html 		.= '<div class="repeater-layout-'.$layout.' field-rows">';
							$end_html 			.= '</div>';
							$row_start_html 	.= '<div class="field-row subfields">';
							$row_end_html 		.= '</div>';
							$column_html_tag 	.= 'div';
						}
					} elseif( $layout == 'row' ) {
						if( !empty( $style ) ) {
							$start_html 		.= '<ul class="repeater-layout-'.$layout.' field-rows">';
							$end_html 			.= '</ul>';
							$row_start_html 	.= '<li class="field-row">';
							$row_start_html 	.= '<ul class="subfields">';
							$row_end_html 		.= '</ul>';
							$row_end_html 		.= '</li>';
							$column_html_tag 	.= 'li';
						}
					}

					$repeater_output .= $start_html;

					while( have_rows($field['key'], $atts['post_id'] ) ) {

						the_row( );
						$repeater_output .= $row_start_html;

						foreach ( $field['sub_fields'] as $sub_fields_key => $sub_field_array ) {


							$subfields = $atts['subfields'];
							$subfield_operator = $atts['subfield_operator'];

							/*
							if( !empty( $subfields ) ) {
								if ( !in_array( $sub_field_array['name'], $subfields ) ) {
									continue;
								}
							}
							*/

							$field_class = 'subfield-' . $sub_field_array['name'];

							if( empty( $field_class ) ) {
								$field_class = $sub_field_array['key'];
							}

							$field_id = $sub_field_array['wrapper']['id'];

							if( empty( $field_id ) ) {
								$field_id = $sub_field_array['key'];
							}

							$sub_field_value = $this->get_data_callback( $sub_field_array, $atts['post_id'], $repeater, $atts );


							$sub_field_value_class = str_replace( ' ', '-', $sub_field_value ); // Replaces all spaces with hyphens.
							$sub_field_value_class = preg_replace( '/[^A-Za-z0-9\-]/', '', $sub_field_value_class ); // Removes special chars.
							$sub_field_value_class = preg_replace( '/-+/', '-', $sub_field_value_class ); // Replaces multiple hyphens with single one.
							$sub_field_value_class = trim( $sub_field_value_class, '-' ); // Remove first or last -
							$sub_field_value_class = strtolower( $sub_field_value_class ); // lowercase

							$sub_field_classes = array(
								'subfield',
								$field_class,
								$sub_field_array['wrapper']['class'],
								'subfield-type-' . $sub_field_array['type'],
								'subfield-value-' . $sub_field_value_class,
							 );

							$sub_field_classes = implode(' ', $sub_field_classes );

							$repeater_output .= '<'.$column_html_tag.' class="'.$sub_field_classes.'">';
							if( $atts['show_label'] == "yes" ) {
								$repeater_output .= '<label class="sub-field-label">';
								$repeater_output .= $sub_field_array['label'];
								$repeater_output .= '</label>';
							}
							$repeater_output .= '<div class="sub-field-value">';
							$repeater_output .= $sub_field_value;
							$repeater_output .= '</div>';
							$repeater_output .= '</'.$column_html_tag.'>';
							// $repeater_output .=$subfield_operator;


							if( $style == 'aione_data_table' ) {
								$aione_data_table_data[$sub_fields_key][] = $sub_field_value;
							} 


						}

						$repeater_output .= $row_end_html;
					}
					$repeater_output .= $end_html;
				}
				if( $style == 'aione_data_table' ) {
					$output .= aione_data_table( $aione_data_table_headers, $aione_data_table_data );
				} else{
					$output .= $repeater_output;
				}
			} else {
				$repeater = false;
				$output .= $this->get_data_callback( $field, $atts['post_id'], $repeater, $atts );
			}
			if( $atts['style'] == "div" ) {
				$output .= '</div>';
			}

		}

		return $output;	
		
	} // END aione_app_builder_post_meta_shortcode

	//Check Field Conditions
	function check_field_conditions( $field, $post_id ) {

		$field_conditions = $field['conditional_logic'];

		$post_id;

		$skip_field = 0;

		if( !empty( $field_conditions ) ) {
			$skip_field++;

			$is_or_true = 0;
			foreach ( $field_conditions as $field_condition_key => $field_condition ) { 

				$is_and_true = 0;
				foreach ( $field_condition as $field_sub_condition_key => $field_sub_condition ) {

					$field_value = get_field( $field_sub_condition['field'], $post_id );
					return get_sub_field( $key, $post_id );

					if( $field_sub_condition['operator'] == '!=empty' ) {
						if( !empty( $field_value ) ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '==empty' ) {
						if( !empty( $field_value ) ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '==' ) {
						if( $field_value == $field_sub_condition['value'] ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '!=' ) {
						if( $field_value != $field_sub_condition['value'] ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '==contains' ) {
						if( strpos( $field_value, $field_sub_condition['value'] ) !== false ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '==pattern ') {
						if( preg_match( $field_sub_condition['value'], $field_value ) ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '>' ) {
						if( $field_value > $field_sub_condition['value'] ) {
							$is_and_true++;
						}
					} elseif( $field_sub_condition['operator'] == '<' ) {
						if( $field_value < $field_sub_condition['value'] ) {
							$is_and_true++;
						}
					}
				}

				if( $is_and_true == count( $field_condition ) ) {
					$is_or_true++;
				}
				
			}

			if( $is_or_true > 0 ) {
				$skip_field = 0;
			}
		}

		return $skip_field;

	}



	function get_data_callback( $field , $post_id , $repeater, $atts ) {

		$data = $this->get_field_data( $field['key'], $post_id, $repeater, $atts );
		
		$output = '';
			
		switch ( $field['type'] ) { 

			case "message":

				$output .= $field['message'];
				break;

			case "text":

				$output .= $data;
				break;


			case "textarea":

				$output .= $data;
				break;


			case "number":

				$output .= $data;
				break;


			case "range":

				$output .= $data;
				break;


			case "email":

				$output .= $data;
				break;


			case "url":

				$output .= '<a href="'.$data.'">'.$data.'</a>';
				break;


			case "password":

				$output .= $data;
				break;


			case "date_picker":
			
				$return_format = $field['return_format'];

				if( $return_format == 'd/m/Y' ) {
					// $output .= 'Return format i.e. "d/m/Y" is not supported';
					$output .= $data;
				} else{
					if( strpos( $field['wrapper']['class'], 'human') !== false ) {
						$output .= $this->human_readable_date( $data, $return_format );
					} else{
						$output .= date( $return_format, strtotime( $data ) );
					}
				}

				break;


			case "time_picker":

				$format = $field['display_format'];
				$output .= date($format,strtotime($data) );
				break;


			case "date_time_picker":

				$return_format = $field['return_format'];

				if( $return_format == 'd/m/Y' ) {
					$output .= 'Return format i.e. "d/m/Y" is not supported';
				} else{
					if( strpos( $field['wrapper']['class'], 'human') !== false ) {
						$output .= $this->human_readable_date( $data, $return_format );
					} else{
						$output .= date( $return_format, strtotime( $data ) );
					}
				}
				break;


			case "color_picker":

				$output .= $data;
				break;	


			case "image":

				if( $field['return_format'] == "array" ) {
					$src =  $data['url'];
				} elseif( $field['return_format'] == "url" ) {
					$src =  $data;
				} else {
					$image_url = wp_get_attachment_url( $data );
					$src =  $image_url;
				}
				$output .= '<img src="'.$src.'"/>';
				break; 


			case "file":

				if( $field['return_format'] == "array" ) {
					if( $data['type'] == 'audio' ) {
						$file_type = $data['subtype'];
						if( $file_type == 'mpeg' ) {
							$file_type = 'mp3';
						}
						$output .= do_shortcode('[audio '.$file_type.'="'.$data['url'].'"][/audio]' );
					} elseif( $data['type'] == 'video' ) {
						$output .= do_shortcode('[video width="'.$data['width'].'" height="'.$data['height'].'" '.$data['subtype'].'="'.$data['url'].'"][/video]' );
					} else {
						$output .= '<img src="'.$data['icon'].'"/><div><a href="'.$data['url'].'">'.$data['filename'].'</a></div>';
					}
				} elseif( $field['return_format'] == "url" ) {
					$field = get_field_object($key );
					$output .= $data;
				} else {
					$file_url = wp_get_attachment_url( $data );
					$field = get_field_object($key );
					$output .= '<a href="'.$file_url.'"><button class="aione-button">View</button></a>';
				}
				break;


			case "wysiwyg":

				$output .= $data;
				break; 


			case "oembed":

				$output .= $data;
				break;


			case "gallery":

				$field_classes = explode(' ', trim( $field['wrapper']['class'] ) );

				if( is_array(  $field_classes ) && in_array( 'aione-images' , $field_classes ) ) {
					foreach ($data as  $gallery) {
						$output .= '<a href="'.$gallery['link'].'">';
						$output .= '<img src="'.$gallery['url'].'"/>';
						$output .= '</a>';
							// $output .= $gallery['caption'];
							// $output .= $gallery['description'];
					}
				} if( is_array(  $field_classes ) && in_array( 'show-lightbox' , $field_classes ) ) {
					foreach ($data as  $gallery) {
						$output .= '<a data-lightbox="image" href="'.$gallery['url'].'">';
						$output .= '<img src="'.$gallery['url'].'"/>';
						$output .= '</a>';
					}
				} else {
					foreach ($data as  $gallery) {
						$output .= ' <div class="gallery-item">';
						$output .= '<img src="'.$gallery['url'].'"/>';
						$output .= '</div>';
					}
				}
				
				break;


			case "select":

				if( $field['multiple'] == '1' ) { 
					if( $field['return_format'] == "value" || $field['return_format'] == "label" ) {
						$output .= implode(",", $data );
					} else {
						foreach( $data as $value ) {	
							$output .= $value['label']."(".$value['value'].") ";
						}
					}
				} else {
					if( $field['return_format'] == "array" ) { 
						$output .= $data['label']."(".$data['value'].") ";
					} else {						
						$output .= $data;
					}
				}

				break; 


			case "checkbox":

				if( $field['return_format'] == "value" || $field['return_format'] == "label" ) {
					$output .= implode(",", $data );
				} else {
					foreach ( $data as $value ) {	
						$output .= $value['label']."(".$value['value'].") ";
					}
				}

				break;


			case "radio":

				if( $field['return_format'] == "array" ) {
					$output .= $data['label']."(".$data['value'].")";
				} else {
					$output .= $data;
				}

				break;


			case "button_group":

				if( $field['return_format'] == "array" ) {
					$output .= $data['label'];
				} else {
					$output .= $data;
				}

				break;


			case "true_false":

				if( $data == "1" ) {			    		
					$output .= "true";
				} else{
					$output .= "false";
				}

				break;


			case "link":

				if( $field['return_format'] == "array" ) {			    		
					$output .= '<a href="'.$data['url'].'" target="'.$data['target'].'">'.$data['title'].'</a>';
				} else {
					$output .= '<a href="'.$data.'" target="">'.$data.'</a>';
				}
				break; 


			case "post_object":

				$post_template = $atts['template'];

				$aione_templates 		= get_option( 'aione-templates' );
				$post_template_array 	= $aione_templates[$post_template];

				if( $field['multiple'] == '1' ) {				
					
					if( $field['return_format'] == "object" ) {

						foreach ( $data as $key => $value ) {

							if( !empty( $post_template ) && $post_template_array ) {

								global $post;
								$post = get_post( $value->ID );
								setup_postdata( $post ); 
								$output .= do_shortcode( $post_template_array['content'] );
								wp_reset_postdata( );

							} else {
								
								$output .= '<div class="post-title">'.$value->post_title.'</div>';
								$output .= '<div class="post-content">'.do_shortcode( $value->post_content ).'</div>';
							}	

						}		

					} else {

						$post_ids = array( );

						foreach ( $data as $key => $value ) {

							if( !empty( $post_template ) && $post_template_array ) {

								global $post;
								$post = get_post($value );
								setup_postdata( $post ); 
								$output .= do_shortcode( $post_template_array['content'] );
								wp_reset_postdata( );

							} else {

								$post_ids[] = $value;

							}
							
						}

						$output .= implode( ',', $post_ids );

					}

				} else {

					if( $field['return_format'] == "object" ) {

						if( !empty( $post_template ) && $post_template_array ) {

							global $post;
							$post = get_post($data );
							setup_postdata( $post ); 
							$output .= do_shortcode( $post_template_array['content'] );
							wp_reset_postdata( );

						} else {

							$output .= '<div class="post-title">'.$data->post_title.'</div>';
							$output .= '<div class="post-content">'.do_shortcode( $data->post_content ).'</div>';

						}	// end  else part if( !empty( $post_template ) && $post_template_array )

					} else {

						// $field['return_format'] is post ID

						if( !empty( $post_template ) && $post_template_array ) {

							// $field['return_format'] is post ID

							global $post;
							$post = get_post( $data );
							setup_postdata( $post ); 
							$output .= do_shortcode( $post_template_array['content'] );
							wp_reset_postdata( );

						} else {

							$output .= $data;

						}

					} // end  else part if( $field['return_format'] == "object" ) 

				} // end  else part if( $field['multiple'] == '1' ) 
				break;


			case "page_link":

				if( $field['multiple'] == '1' ) {
					foreach ($data as $key => $value) {
						$output .= '<a href="'.$value.'" target="">'.$value.'</a>';
					}			    		
				} else {
					$output .= '<a href="'.$data.'" target="">'.$data.'</a>';
				}			    	
				break;


			case "relationship":

				if( $field['return_format'] == "object" ) {
					$output .= '<div class="">Title : '.$data[0]->post_title.'</div>';
					$output .= '<div class="">Content : '.$data[0]->post_content.'</div>';
				} else { 
					$data = get_post($data[0] );
					$output .= '<div class="">Title : '.$data->post_title.'</div>';
					$output .= '<div class="">Content : '.$data->post_content.'</div>';
				}		    	
				break; 


			case "taxonomy":

				if( $field['return_format'] == "object" ) {			    		
					if( $field['field_type']=="radio" || $field['field_type']=="select" ) {
						$output .= $data->name;
					} else {
						foreach ($data as $value) {
							$output .= $value->name." ";
						}
					}
				} else {
					if( $field['field_type']=="radio" || $field['field_type']=="select" ) {
						$term = get_term( $data );
						$output .= $term->name;
					} else {
						foreach ($data as $value) {
							$term = get_term( $value );
							$output .= $term->name." ";
						}
					}
				}	    	
				break; 


			case "user": 

				if( $field['multiple'] == '1' ) {
					$users = array( );
					foreach ($data as $key => $value) {
						if( $field['return_format'] == "object" ) {
							$users[] = $value->ID;
						} else if ($field['return_format'] == "array" ) {
							$users[] = $value['ID'];
						} else {
							$users[] =  $value;
						}
					}

					$output .= implode( $users, ',' );
				} else {
					if( $field['return_format'] == "object" ) {
						$output .= $data->ID;
					} else if ( $field['return_format'] == "array" ) {
						$output .= $data['ID'];
					} else {
						$output .= $data;
					}
				}
				break;


			case "repeater":  

		    	$repeater_field_key 	= $field['key'];
		    	$repeater_field_label 	= $atts['show_label'];
		    	$repeater_field_style 	= $atts['style'];

		    	$output .= do_shortcode( '[post_meta field="'.$repeater_field_key.'" show_label="'.$repeater_field_label.'" style="'.$repeater_field_style.'"]' );
		    	break;  


		    default:
		    	$output .= "Unknown field Type"; 

		} // switch( $data )
		

		return $output;
	}
	

	function get_field_data( $key , $post_id, $repeater = false ) {

		if( $repeater == true ) { 
			return get_sub_field( $key, $post_id );
		} else {			
			return  get_field( $key, $post_id );
		}

	}


	function clean_shortcode_parameters( $atts ) {
		
		foreach ($atts as $parameter_key => $parameter_value) {

			$parameter =  $parameter_value;

			if( $parameter_key == 'meta_query' ) {
				$parameter = str_replace("<%","[", $parameter );
				$parameter = str_replace("%>","]", $parameter );

				$parameter = do_shortcode( $parameter );
			} else{
				$parameter = str_replace("<%","[", $parameter );
				$parameter = str_replace("%>","]", $parameter );

				$parameter = do_shortcode( $parameter );

				$parameter = str_replace("{{","[", $parameter );
				$parameter = str_replace("}}","]", $parameter );

				$parameter = do_shortcode( $parameter );
			}

			$atts[$parameter_key] = $parameter;
		}

		return $atts;
	}


	function human_readable_date( $date, $format='' ) {

		//New Function on WP human_readable_duration()

		$output = "";
		$date = strtotime( $date );
		$now = current_time( 'timestamp', 1 );
		
		if( $date < $now ) {
			$output .= human_time_diff( $date,  $now) . ' ago';
		} else{
			$output .= 'after ' . human_time_diff( $date,  $now );
		}

		return $output;
	}

	function aione_app_builder_upcoming_tag_shortcode( $atts ) {
		
		$atts = shortcode_atts(
			array(
				'text'           => "Upcoming",
			), $atts,'upcoming_tag' );

		global $post;
		$html = '';
		$status = get_post_status( $post->ID );

		if( $status == 'future' ) {
			$html .= '<div class="upcoming-tag">'.$atts['text'].'</div>';
			return $html;
		} else {
			return false;
		}

	}

	function aione_app_builder_social_icon_shortcode($atts ) {
		$atts = shortcode_atts(
			array(
				'size'       => "small",
				'theme'           => "dark",
				'style'           => "square",
				'direction'       => "horizontal",
				'labels'          => "false",
				'facebook'        => "",
				'twitter'         => "",
				'youtube'         => "",
				'linkedin'        => "",
				'instagram'       => "",
				'flickr'          => "",
				'github'          => "",
				'pinterest'       => "",
				'rss'             => "",
				'tumblr'          => "",
				'vimeo'           => "",
				'wordpress'       => "",
			), $atts,'social_icon' );

		$html = '';
		$size = $atts['size'];
		$theme = $atts['theme'];
		$style = $atts['style'];
		$direction = $atts['direction'];
		$labels = $atts['labels'];
		$facebook = $atts['facebook'];
		$twitter = $atts['twitter'];
		$youtube = $atts['youtube'];
		$linkedin = $atts['linkedin'];
		$instagram = $atts['instagram'];
		$flickr = $atts['flickr'];
		$github = $atts['github'];
		$pinterest = $atts['pinterest'];
		$rss = $atts['rss'];
		$tumblr = $atts['tumblr'];
		$vimeo = $atts['vimeo'];
		$wordpress = $atts['wordpress'];

		$html .= '<ul class="aione-social-icons '.$size.' '.$theme.' '.$style.' '.$direction.' '.$labels.'">';
		if( $facebook != "" ) {
			$html .= '<li class="facebook"><a href='.$facebook.' aria-label="Facebook" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Facebook</span></a></li>';
		}
		if( $twitter != "" ) {
			$html .= '<li class="twitter"><a href='.$twitter.' aria-label="Twitter" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Twitter</span></a></li>';
		}
		if( $youtube != "" ) {
			$html .= '<li class="youtube"><a href='.$youtube.' aria-label="Youtube" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Youtube</span></a></li>';
		}
		
		if( $linkedin != "" ) {
			$html .= '<li class="linkedin"><a href='.$linkedin.' aria-label="Linkedin" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Linkedin</span></a></li>';
		}
		if( $instagram != "" ) {
			$html .= '<li class="instagram"><a href='.$instagram.' aria-label="Instagram" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Instagram</span></a></li>';
		}
		if( $flickr != "" ) {
			$html .= '<li class="flickr"><a href='.$flickr.' aria-label="Flickr" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Flickr</span></a></li>';
		}
		if( $github != "" ) {
			$html .= '<li class="github"><a href='.$github.' aria-label="Github" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Github</span></a></li>';
		}
		if( $pinterest != "" ) {
			$html .= '<li class="pinterest"><a href='.$pinterest.' aria-label="Pinterest" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Pinterest</span></a></li>';
		}
		if( $rss != "" ) {
			$html .= '<li class="rss"><a href='.$rss.' aria-label="RSS" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">RSS</span></a></li>';
		}
		if( $tumblr != "" ) {
			$html .= '<li class="tumblr"><a href='.$tumblr.' aria-label="Tumblr" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Tumblr</span></a></li>';
		}
		if( $vimeo != "" ) {
			$html .= '<li class="vimeo"><a href='.$vimeo.' aria-label="Vimeo" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Vimeo</span></a></li>';
		}
		if( $wordpress != "" ) {
			$html .= '<li class="wordpress"><a href='.$wordpress.' aria-label="WordPress" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">WordPress</span></a></li>';
		}
		
		$html .= '</ul>';
		return $html;
	}

	function aione_app_builder_social_share_shortcode($atts ) {
		$atts = shortcode_atts(
			array(
				'size'       => "small",
				'theme'           => "dark",
				'style'           => "square",
				'direction'       => "horizontal",
				'labels'          => "false",
				'facebook'        => "",
				'twitter'         => "",	
				'linkedin'        => "",				
				'pinterest'       => "",
				'reddit'             => "",
				'tumblr'          => "",
			), $atts,'social_share' );

		$html = '';
		$size = $atts['size'];
		$theme = $atts['theme'];
		$style = $atts['style'];
		$direction = $atts['direction'];
		$labels = $atts['labels'];
		$facebook = $atts['facebook'];
		$twitter = $atts['twitter'];
		$linkedin = $atts['linkedin'];
		$pinterest = $atts['pinterest'];
		$reddit = $atts['reddit'];
		$tumblr = $atts['tumblr'];
		$html .= '<ul class="aione-social-icons '.$size.' '.$theme.' '.$style.' '.$direction.' '.$labels.'">';
		if( $facebook == "true" ) {
			$facebook_url = '
			https://www.facebook.com/sharer/sharer.php?u='.rawurlencode( get_the_permalink() ).'&title='.get_the_title( );
			$html .= '<li class="facebook"><a href='.$facebook_url.' aria-label="Facebook" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Facebook</span></a></li>';
		}
		if( $twitter == "true" ) {
			$twitter_url = '
			http://twitter.com/share?text=' . get_the_title() . '&url=' . rawurlencode( get_the_permalink() );
			$html .= '<li class="twitter"><a href="'.$twitter_url.'" aria-label="Twitter" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Twitter</span></a></li>';
		}
		
		
		if( $linkedin == "true" ) {
			$linkedin_url = '
			https://www.linkedin.com/shareArticle?mini=true&url=' . rawurlencode( get_the_permalink() ) . '&title=' . get_the_title( );
			$html .= '<li class="linkedin"><a href='.$linkedin_url.' aria-label="Linkedin" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Linkedin</span></a></li>';
		}
		
		if( $pinterest == "true" ) {
			if ( has_post_thumbnail() ) {
				$thumbnail_id = get_post_thumbnail_id( $post->ID );
				$thumbnail    = $thumbnail_id ? current( wp_get_attachment_image_src( $thumbnail_id, 'large', true ) ) : '';
			} else {
				$thumbnail = null;
			}

				// Generate the Pinterest URL.
			$pinterest_url = '
			https://pinterest.com/pin/create/button/?&url=' . rawurlencode( get_the_permalink() ) . '&description=' . get_the_title() . '&media=' . esc_url( $thumbnail );
			$html .= '<li class="pinterest"><a href='.$pinterest_url.' aria-label="Pinterest" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Pinterest</span></a></li>';
		}
		if( $reddit == "true" ) {
			$reddit_url = '
			http://www.reddit.com/submit?url=' . rawurlencode( get_the_permalink() ) . '&title=' . get_the_title( );
			$html .= '<li class="reddit"><a href='.$reddit_url.' aria-label="Reddit" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Reddit</span></a></li>';
		}
		if( $tumblr == "true" ) {
			$tumblr_url = '
			https://www.tumblr.com/widgets/share/tool?canonicalUrl=' . rawurlencode( get_the_permalink() ) . '&title=' . get_the_title( );
			$html .= '<li class="tumblr"><a href='.$tumblr_url.' aria-label="Tumblr" target="_blank" rel="noopener noreferrer"><span class="icon"></span><span class="label">Tumblr</span></a></li>';
		}
		
		
		$html .= '</ul>';
		return $html;
	}

	/**************Login Form Captcha **************************/
	function aione_login_form_captcha( ) {
		$enabled_login_captcha = get_option("enable_login_page_captcha" );
		if( $enabled_login_captcha == "yes" ) {
			if( class_exists('ReallySimpleCaptcha'))
			  {
			    $captcha_instance = new ReallySimpleCaptcha( );
			    $word = $captcha_instance->generate_random_word( );
			    $prefix = mt_rand( );
			    $captchaimg = $captcha_instance->generate_image( $prefix, $word );
			    $imgpath = plugin_dir_url(dirname(__FILE__))."tmp/".$captchaimg;
			    ?>

			    <input type="hidden" name="aione_captcha_prefix" value="<?php echo $prefix; ?>"/>
			    <label for="aione_captcha_answer">Captcha</label>
			    <div><img src="<?php echo $imgpath; ?>" /></div>  <input name="aione_captcha_answer" type="text" />
			    <?php
			}
		}
	}
	function aione_login_form_captcha_custom( ) {
		$output = "";
		$enabled_login_captcha = get_option("enable_login_page_captcha" );
		if( $enabled_login_captcha == "yes" ) {
			if( class_exists('ReallySimpleCaptcha'))
			  {
			    $captcha_instance = new ReallySimpleCaptcha( );
			    $word = $captcha_instance->generate_random_word( );
			    $prefix = mt_rand( );
			    $captchaimg = $captcha_instance->generate_image( $prefix, $word );
			    $imgpath = plugin_dir_url(dirname(__FILE__))."tmp/".$captchaimg;
			    
			    $output .='<input type="hidden" name="aione_captcha_prefix" value="'.$prefix.'"/>
			    <label for="aione_captcha_answer">Captcha</label>
			    <div><img src="'.$imgpath.'" /></div>  <input name="aione_captcha_answer" type="text" />
			    ';
			}
		}
		return $output;
	}
	function aione_validate_login_captcha($user, $password) {
		$return_value = $user;
		$enabled_login_captcha = get_option("enable_login_page_captcha" );
		if( $enabled_login_captcha == "yes" ) {
			if( class_exists('ReallySimpleCaptcha'))
			{
				$captcha_instance = new ReallySimpleCaptcha( );
				$prefix = $_POST['aione_captcha_prefix'];
				if( !$captcha_instance->check( $prefix, $_POST['aione_captcha_answer'] ))
					{
					  // if there is a mis-match
					  $return_value = new WP_Error( 'loginCaptchaError', 'Captcha Error. Please try again.' );
					}

				// remember to remove the prefix
				$captcha_instance->remove( $prefix );
			}
		}
		return $return_value;
	}

	/**************Login Form Captcha End**************************/


	/**
	* Add New Post Shortcode
	*/
	function aione_app_builder_add_new_shortcode(  $atts, $content = null ) {
		// Attributes
		$atts = shortcode_atts(
			array(
				'post_type'				=> 'post',
				'title'					=> true,
				'content'				=> true,
				'status'				=> 'publish',
				'field_groups'			=> false,
				'fields'				=> false,
				'fields'				=> false,
				'return'				=> false,
				'uploader'				=> 'basic',
				'label_placement' 		=> 'top', // top/left
				'instruction_placement' => 'label', // label/field
				'submit_value' 			=> 'Submit',
				'updated_message' 		=> 'Post updated',
				'class'					=> 'add-new-form',
				'id'					=> 'add_new_form',
			), $atts, 'add_new' );

		$atts = $this->clean_shortcode_parameters( $atts );

		if( !empty( $atts['field_groups'] ) ) {	
			$field_groups = explode( ',', $atts['field_groups'] );
		} else {
			$field_groups = false;
		}

		if( !empty( $atts['fields'] ) ) {	
			$fields = explode( ',', $atts['fields'] );
		} else {
			$fields = false;
		}

		$output = '';
		if( $atts['title'] == "false" ) {
			$atts['title'] = false;
		}
		if( $atts['title'] == "true" ) {
			$atts['title'] = true;
		}
		if( $atts['content'] == "false" ) {
			$atts['content'] = false;
		}
		if( $atts['content'] == "true" ) {
			$atts['content'] = true;
		}
		


		$options = array(
			'id'					=> $atts['id'],
			'post_id'				=> 'new_post',
			'new_post' 				=> array(
				'post_type'			=> $atts['post_type'],
				'post_status'		=> $atts['status']
			),
			'post_title'			=> $atts['title'],
			'post_content'			=> $atts['content'],
			'field_groups'			=> $field_groups,
			'fields'				=> $fields,
			'form'					=> true,
			'form_attributes' 		=> array(),
			'html_before_fields' 	=> '',
			'html_after_fields' 	=> '',
			'submit_value' 			=> $atts['submit_value'],
			'updated_message' 		=> $atts['updated_message'],
			'label_placement' 		=> $atts['label_placement'], // top/left
			'instruction_placement' => $atts['instruction_placement'], // label/field
			'field_el' 				=> 'div',
			'uploader' 				=> $atts['uploader'],
			'html_updated_message'	=> '<div id="message" class="post-added aione-message success">%s</div>',
			'html_submit_button'	=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',
			'html_submit_spinner'	=> '<span class="acf-spinner"></span>',
			'honeypot' 				=> true,
			'kses'					=> true,
		 );

		if( !empty( $atts['return'] ) ) {	
			$options['return'] = $atts['return'];
		}

		acf_form_head( ); 

		ob_start( );
		acf_form( $options );
		$output .= ob_get_contents( );
		ob_end_clean( );

		return $output;

	}


	/**
	* Edit Post Shortcode
	*/
	function aione_app_builder_edit_shortcode(  $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'post_id'			=> null,
			'title'				=> true,
			'content'			=> true,
			'status'			=> 'publish',
			'field_groups'		=> false,
			'fields'			=> false,
			'uploader'			=> 'basic',
			'return' 			=> '',
			'label_placement' 		=> 'top', // top/left
			'instruction_placement' => 'label', // label/field
			'class'				=> 'edit-post-form',
			'id'				=> 'edit_post_form'
		), $atts, 'edit' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = '';

		if( !empty( $atts['field_groups'] ) ) {	
			$field_groups = explode( ',', $atts['field_groups'] );
		} else {
			$field_groups = false;
		}

		if( !empty( $atts['fields'] ) ) {	
			$fields = explode( ',', $atts['fields'] );
		} else {
			$fields = false;
		}
		/*
		echo "<pre>";
		print_r( );
		echo "</pre>";
		*/

		if( $atts['title'] == "false" ) {
			$atts['title'] = false;
		}
		if( $atts['title'] == "true" ) {
			$atts['title'] = true;
		}
		if( $atts['content'] == "false" ) {
			$atts['content'] = false;
		}
		if( $atts['content'] == "true" ) {
			$atts['content'] = true;
		}

		$options = array(
			'id'					=> $atts['id'],
			'post_id'				=> $atts['post_id'],
			'post_title'			=> $atts['title'],
			'post_content'			=> $atts['content'],
			'field_groups'			=> $field_groups,
			'fields'				=> $fields,
			'form'					=> true,
			'form_attributes' 		=> array(),
			'return' 				=> $atts['return'],
			'html_before_fields' 	=> '',
			'html_after_fields' 	=> '',
			'submit_value' 			=> __("Submit", 'aione-app-builder'),
			'updated_message' 		=> __("Post updated", 'aione-app-builder'),
			'label_placement' 		=> $atts['label_placement'], // top/left
			'instruction_placement' => $atts['instruction_placement'], // label/field
			'field_el' 				=> 'div',
			'uploader' 				=> $atts['uploader'],
			'html_updated_message'	=> '<div id="message" class="updated"><p>%s</p></div>',
			'html_submit_button'	=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',
			'html_submit_spinner'	=> '<span class="acf-spinner"></span>',
			'honeypot' 				=> true,
			'kses'					=> true,
		 );

		acf_form_head( ); 

		ob_start( );
		acf_form( $options );
		$output .= ob_get_contents( );
		ob_end_clean( );

		return $output;
	}


	/**
	* Form Shortcode
	*/
	function aione_app_builder_form_shortcode(  $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'field_groups'		=> false,
			'fields'			=> false,
			'form'				=> true,
			'action'			=> '',
			'method'			=> 'post',
			'submit'			=> 'Submit',
			'uploader'			=> 'basic',
			'label_placement' 		=> 'top', // top/left
			'instruction_placement' => 'label', // label/field
			'class'				=> 'aione-form',
			'id'				=> 'aione_form'
		), $atts, 'form' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = '';

		
		if( !empty( $atts['field_groups'] ) ) {	
			$field_groups = explode( ',', $atts['field_groups'] );
		} else {
			$field_groups = false;
		}

		if( !empty( $atts['fields'] ) ) {	
			$fields = explode( ',', $atts['fields'] );
		} else {
			$fields = false;
		}

		$html_before_fields = '';
		$html_after_fields 	= '';

		if( $atts['form'] ) {
			$html_before_fields = '<form id="'.$atts['id'].'" class="'.$atts['class'].'" action="'.$atts['action'].'" method="'.$atts['method'].'">';
			$html_after_fields = '<div><input name="submit" type="submit" class="acf-button button button-primary button-large" value="'.$atts['submit'].'" /></div></form>';
		}

		$options = array(
			'id'					=> $atts['id'],
			'post_id'				=> false,
			'new_post'				=> false,
			'post_title'			=> false,
			'post_content'			=> false,
			'field_groups'			=> $field_groups,
			'fields'				=> $fields,
			'form'					=> false,
			'form_attributes' 		=> array(),
			'return' 				=> '',
			'html_before_fields' 	=> $html_before_fields,
			'html_after_fields' 	=> $html_after_fields,
			'submit_value' 			=> __("Submit", 'aione-app-builder'),
			'updated_message' 		=> __("Post updated", 'aione-app-builder'),
			'label_placement' 		=> $atts['label_placement'], // top/left
			'instruction_placement' => $atts['instruction_placement'], // label/field
			'field_el' 				=> 'div',
			'uploader' 				=> $atts['uploader'],
			'html_updated_message'	=> '<div id="message" class="updated"><p>%s</p></div>',
			'html_submit_button'	=> '<input type="submit" class="acf-button button button-primary button-large" value="%s" />',
			'html_submit_spinner'	=> '<span class="acf-spinner"></span>',
			'honeypot' 				=> true,
			'kses'					=> true,
		 );

		acf_form_head( );  

		ob_start( );
		acf_form( $options );
		$output .= ob_get_contents( );
		ob_end_clean( );

		return $output;
	}

	/**
	* Form Shortcode
	*/
	function aione_app_builder_visit_counter_shortcode(  $atts, $content = null ) {

		// Attributes
		$atts = shortcode_atts( array(
			'class' 	=> 'aione-visit-counter',
			'id' 		=> 'aione_visit_counter',
			'key' 		=> 'visit_counter',
			'table' 	=> 'options',
			'text' 		=> '',
			'start' 	=> '1',
			'style' 	=> 'html',
			'theme' 	=> 'dark',
		), $atts, 'visit_counter' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output = '';

		$option_name = $atts['key'];


		if( $atts['table'] == 'options' ) {
			if( get_option( $option_name ) !== false ) {
	 
			    // The option already exists, so update it.

			    $visits = get_option( $option_name );
				$visits = $visits + 1;

			    update_option( $option_name, $visits );
			 
			} else {
			 
			    update_option( $option_name, $atts['start'] );
			}
		} elseif( $atts['table'] == 'post_meta' ) {

			if( !empty( get_post_meta( get_the_ID(), $option_name, true ) ) ) {
	 
			    // The option already exists, so update it.

			    $visits = get_post_meta( get_the_ID(), $option_name, true );
				$visits = $visits + 1;

			    update_post_meta( get_the_ID(), $option_name, $visits );
			 
			} else {
			 
			    update_post_meta( get_the_ID(), $option_name, $atts['start'] );
			}
		}


		

		if( $atts['style'] == 'html' ) {
			$output .= '<div id="' . $atts['id'] . '" class="' . $atts['class'] . ' ' . $atts['theme'] . '">';
		}

		if( !empty( $atts['style'] ) ) {

			if( !empty( $atts['text'] ) ) {
				$output .=  '<span class="message">' . $atts['text'] . '</span>';
			}

			if( $atts['table'] == 'options' ) {
				$output .=  '<span class="counter">' . get_option( $option_name ) . '</span>';
			} elseif( $atts['table'] == 'post_meta' ) {
				$output .=  '<span class="counter">' . get_post_meta( get_the_ID(), $option_name, true ) . '</span>';
			}

		}

		if( $atts['style'] == 'html' ) {
			$output .= '</div>';
		}

		return $output;

	}

	/**
	* Export Shortcode
	*/

	function aione_app_builder_export_shortcode($atts, $content = null ) {
		// Attributes
		$atts = shortcode_atts( array(
			'post_type' 	=> 'post',
			'id' 			=> '',
		), $atts, 'export' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$export_id 	= $atts['id'];

		if( empty( $export_id ) ) {
			$export_id = 'export_'. $atts['post_type'];
		}


		$upload = wp_upload_dir( );			
	    $upload_dir = $upload['basedir'];
	    $upload_dir = $upload_dir . '/exports';

	    if ( !is_dir( $upload_dir ) ) {
	       wp_mkdir_p( $upload_dir,0777,true );
	    }

	    $url = $upload['baseurl']. '/exports/';
	    $path = $upload['basedir']. '/exports/';

		$filename 	= 'export_' . generate_filename() . '.csv';
		$file_url 	= $url .''. $filename;
		$file_path	= $path .''. $filename;


		$output = '';

		$output .= '<a id="' . $export_id . '" class="aione-button aione-export-button hover-white" href="#" data-post_type="' . $atts['post_type'] . '" data-offset="0" data-filename="' . $filename . '" data-filepath="'.$file_path.'" data-fileurl="'.$file_url.'" data-header="false">Export</a>';

		$output .= "<script>

			$( document ).ready( function() {
				$(document).on('click', '#" . $export_id . "', function(e) {
					e.preventDefault( );
					export_records( );
					$(this).text('Exporting...' );
				} );
			} );

			function export_records( ) {
				var offset = $( '#" . $export_id . "' ).data('offset' );
				console.log('offset')
				console.log(offset)
				var filename = $( '#" . $export_id . "' ).data('filename' );
				var filepath = $( '#" . $export_id . "' ).data('filepath' );
				var fileurl = $( '#" . $export_id . "' ).data('fileurl' );
				var header = $( '#" . $export_id . "' ).data('header' );
				var post_type = $( '#" . $export_id . "' ).data('post_type' );

				jQuery.ajax({
			        url: ajaxurl,
			        type: 'POST', 
			        data: {
			        	'filename': filename,
			        	'filepath': filepath,
			        	'fileurl': fileurl,
			        	'header': header,
			        	'post_type': post_type,
			        	'offset': offset,
			        	'action': 'export',
			        },
			        success: function(response) {
			        	var response = jQuery.parseJSON( response );
						console.log(response );
						if( response.result.complete == true ) {			            		
		            		window.open(response.request.fileurl );
		            		location.reload( );
		            	} else{
		            		if( response.success == true ) {
		            			var offset = $('#" . $export_id . "').data('offset') + 10;  
		            			$('#" . $export_id . "').data('offset', offset );
		            			var header = response.result.header;
		            			$('#" . $export_id . "').data('header', header );
				            	export_records( );				            	
				            }
		            	}

			        },

					error: function(errorThrown ) {
						
						console.log(errorThrown );

					}
			    } );


			}
		</script>"
		;

		return $output;

	}

	function export( ) {

		$response =  array( );

		$response['result'] 	= array( );
		$response['success'] 	= false;
		$response['errors'] 	= true;
		$response['messages'] 	= array( );
		$response['request'] 	= array( );

		if ( isset( $_REQUEST) ) {

			$filename 	= $_REQUEST['filename'];
			$filepath 	= $_REQUEST['filepath'];
			$fileurl 	= $_REQUEST['fileurl'];
			$header 	= $_REQUEST['header'];
			$post_type 	= $_REQUEST['post_type'];
			$offset 	= $_REQUEST['offset'];
			$action 	= $_REQUEST['action'];

			$response['request']['filename'] 	= $filename;
			$response['request']['filepath'] 	= $filepath;
			$response['request']['fileurl'] 	= $fileurl;
			$response['request']['header'] 		= $header;
			$response['request']['post_type'] 	= $post_type;
			$response['request']['offset'] 		= $offset;
			$response['request']['action'] 		= $action;
			$response['result']['complete']		= false;


			


			// WP_Query arguments
			$args = array(
				'post_type'              => array( $post_type ),
				'nopaging'               => false,
				'posts_per_page'         => '10',
				'offset'         		 => $offset,
			 );

			// The Query
			$posts = new WP_Query( $args );
			$data = array( );
			$header_array = array( );
			$header_default = false;
			// The Loop
			if ( $posts->have_posts() ) {
				foreach($posts->posts as $post ) {
					if( $header_default == false ) {						
						$header_array = array_keys((array)$post );
						$header_default = true;
					}
					/*ACF*/			
					$custom_fields = get_fields( $post->ID );					
					foreach ($custom_fields as $key => $value) {
						if( is_array($value) ) {
							$post->$key = implode(',', $value );
						}else{							
							$post->$key = $value;
						}
					}

					$data[] = $post;
				}
			} else {
				$response['result']['complete'] = true;
				$response['success'] 	= true;
				$response['errors'] 	= false;
				$response = json_encode( $response );

				echo $response;
				die( );
			}
			
			
			// Restore original Post Data
			wp_reset_postdata( );

			$response['posts'] = $data;
			//unset($data[0]->post_content );
			$groups = acf_get_field_groups(array('post_type' =>  $post_type ) );
			foreach ($groups as  $group) {
				$fields_array=acf_get_fields($group['key'] );
				$field[] = array_column($fields_array, 'name' );
			}
			if( is_array($field) && !empty($field) ) {
				foreach ($field as $value) {
					$header_array = array_merge($header_array,array_values($value) );
				}				
			}
			//$header_array= array_keys((array)$data[0] );

			if( $action == 'export' ) {
				
				$csv_file = fopen( $filepath, 'a' );
				if( $header == 'false' ) {
					fputcsv( $csv_file, $header_array );
					$header = 'true';
				}
				foreach($data as $row) {
					//unset($row->post_content );
					fputcsv( $csv_file, (array)$row, "," );
				}
				
				fclose( $csv_file );				
				$response['result']['header'] 	= $header;
				$response['result']['offset'] 	= $offset;
				$response['result']['added_record'] = $posts->found_posts;
				$response['success'] 	= true;
				$response['errors'] 	= false;
			}

		}

		$response = json_encode( $response );

		echo $response;
		die( );
	}

	/**
	* Import Shortcode
	*/

	function aione_app_builder_import_shortcode($atts, $content = null ) {
		// Attributes
		$atts = shortcode_atts( array(
			'post_type' 	=> 'post',
			'id' 			=> '',
		), $atts, 'import' );

		global $post;

		$atts = $this->clean_shortcode_parameters( $atts );

		$import_id 	= $atts['id'];

		if( empty( $import_id ) ) {
			$import_id = 'import_'. $atts['post_type'];
		}


		$upload = wp_upload_dir( );			
	    $upload_dir = $upload['basedir'] . '/imports/';

	    if ( !is_dir( $upload_dir ) ) {
	       wp_mkdir_p( $upload_dir,0777,true );
	    }

	    $filename 	= 'import_' . generate_filename() . '.csv';
	    $url = $upload['baseurl']. '/imports/';
	    $path = $upload['basedir']. '/imports/';
		

		$output = '';
		$output .= '<div class="statusMsg"></div>';
		$output .= '<form id="form_' . $import_id . '" method="post" enctype="multipart/form-data">
		<input type="file" id="file_' . $import_id . '" name="aione_import" value="" class="" />		
		<input type="submit" id="' . $import_id . '" class="aione-button aione-import-button hover-white" data-post_type="' . $atts['post_type'] . '" data-filename="'.$filename.'" data-filepath="'.$path.'" data-fileurl="'.$url.'" name="import" value="Import">
		
		</form>';

		$output .= "<script>			

			$('#form_" . $import_id . "').on('submit', function (event) {
			    event.preventDefault( );
			    var offset = 1;
			    var post_type = $( '#" . $import_id . "' ).data('post_type' );
			    var filename = $( '#" . $import_id . "' ).data('filename' );
			    file_data = $('#file_" . $import_id . "').prop('files')[0];
		        form_data = new FormData( );
		        form_data.append('file', file_data );
		        form_data.append('filename', filename );
		        form_data.append('action', 'upload' );
			    $('#" . $import_id . "').val('Importing...' );
			    jQuery.ajax({
		            url: ajaxurl,
		            type: 'POST',
		            data: form_data,
		            contentType: false,
        			processData: false,		            
		            beforeSend: function( ) {
		                $('#" . $import_id . "').attr('disabled','disabled' );
		                $('#form_" . $import_id . "').css('opacity','.5' );
		            },
		            success: function(response ) { 
		            	console.log('response' );
		            	console.log(response );
		            	var response = jQuery.parseJSON( response );
		                $('.statusMsg').html('' );
		                if( response.status == 1 ) {
		                	if( response.count > 0 ) {
		                		import_records(post_type,response.targetFilePath,offset,response.count,response.uploadedFile );
		                	}
		                    $('#form_" . $import_id . "')[0].reset( );
		                    $('.statusMsg').html('<p class=\"alert alert-success\">'+response.message+'</p>' );
		                }else{
		                    $('.statusMsg').html('<p class=\"alert alert-danger\">'+response.message+'</p>' );
		                }
		                $('#form_" . $import_id . "').css('opacity','' );
		                $('#" . $import_id . "').removeAttr('disabled' );
		                $('#" . $import_id . "').val('Import' );
		            }
		        } );

			} );

			function import_records(post_type,targetFilePath,offset,totalcount,uploadedFile ) {
				jQuery.ajax({
			        url: ajaxurl,
			        type: 'POST', 
			        data: {							        	
			        	'post_type': post_type,
			        	'offset': offset,
			        	'totalcount': totalcount,
			        	'filepath': targetFilePath,
			        	'uploadedfile': uploadedFile,
			        	'action': 'import',
			        },
			        success: function(response) {
			        	var response = jQuery.parseJSON( response );
						console.log(response );
						if( response.success ) {
							offset++;
							if( offset <= totalcount ) {
								console.log('offset after increase')
								console.log(offset)
								import_records(post_type,targetFilePath,offset,totalcount, uploadedFile );
							}
						}

			        },

					error: function(errorThrown ) {
						
						console.log(errorThrown );

					}
			    } );
			}
		</script>"
		;

		return $output;

	}

	function upload_callback( ) {

		$response =  array(
			'status' => 0, 
    		'message' => 'Form submission failed, please try again.' 
    	 );

		if ( isset( $_REQUEST) ) {
			
			$upload = wp_upload_dir( );			
		    $upload_dir = $upload['basedir']. '/imports/';
		    $upload_url = $upload['baseurl']. '/imports/';

			//if( isset($_REQUEST['file']) ) { $response['2'] = "is set post file";
				if( !empty($_FILES["file"]["name"]) ) {  
					$fileName = $_REQUEST['filename'];
					$name_array = explode('.csv', $fileName );
					$name = $name_array[0];
					$aione_import_history = get_option('aione_import_history' );
					if( $aione_import_history == null) {
						$history[$name] = array( );
						$history[$name]['actual_name'] = $_FILES["file"]["name"];
						$history[$name]['new_name'] = $fileName;
						$history[$name]['date'] = date('Y-m-d' );
						$history[$name]['post_ids'] = array( );
						add_option('aione_import_history', $history );
					}
					else {
						$aione_import_history[$name] = array( );
						$aione_import_history[$name]['actual_name'] = $_FILES["file"]["name"];
						$aione_import_history[$name]['new_name'] = $fileName;
						$aione_import_history[$name]['date'] = date('Y-m-d' );
						$aione_import_history[$name]['post_ids'] = array( );
						update_option('aione_import_history', $aione_import_history );
					}
					// $fileName = basename($_FILES["file"]["name"] ); 
					$targetFilePath = $upload_dir . $fileName; 
                	$fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION );
                	$allowTypes = array('csv' ); 
                	if( in_array($fileType, $allowTypes) ) {  
	                    if( move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath) ) { 
	                        $uploadedFile = $fileName; 
	                        $uploadStatus = 1; 
	                        $response['targetFilePath'] = $targetFilePath;
	                        $response['uploadedFile'] = $uploadedFile;
	                        $response['message'] = 'File upload successfully.';
	                        $response['history'] = $aione_import_history;

	                        $csv = new ParseCsv\Csv( );
							$csv->load_data($targetFilePath );
							$count = $csv->getTotalDataRowCount( );
							$response['count'] = $count;
	                    }else{ 
	                    	$uploadStatus = 0; 
	                        $response['message'] = 'Sorry, there was an error uploading your file.'; 
	                    } 
	                }else{ 
	                    $uploadStatus = 0; 
	                    $response['message'] = 'Sorry, only CSV files are allowed to upload.'; 
	                } 
	            } 
			//}
	        if( $uploadStatus == 1 ) { 
	         	$response['status'] = 1; 
                $response['message'] = 'File upload successfully.';
	        }    

		}

		

		$response = json_encode( $response );

		echo $response;
		die( );
	}

	function import( ) {
		$response =  array( );
		global $wpdb;
		if ( isset( $_REQUEST) ) {
			$post_type 	= $_REQUEST['post_type'];
			$offset 	= $_REQUEST['offset'];
			$totalcount = $_REQUEST['totalcount'];
			$filepath 	= $_REQUEST['filepath'];
			$filename 	= $_REQUEST['uploadedfile'];
			$name_array = explode('.csv', $filename );
			$filename 	= $name_array[0];
			$action 	= $_REQUEST['action'];
			$table_name = $wpdb->prefix . 'posts';
			$post_column = $wpdb->get_col( "DESC " . $table_name, 0 );

			if( $action == 'import' ) {
				$csv = new ParseCsv\Csv( );
				$csv->offset = $offset;
				$csv->limit = 1;
				$csv->parse($filepath );
				$row = $csv->data;
				$postdata  = array( );
				$postmeta  = array( );
				foreach ($row[0] as $key => $value) {
					if( in_array($key, $post_column) ) {
						$postdata[$key] = $value;
					}else {
						$postmeta[$key] = $value;
					}
				}
				$post_id = wp_insert_post( $postdata );
				$import_history = get_option('aione_import_history' );
				array_push($import_history[$filename]['post_ids'], $post_id );
				update_option('aione_import_history', $import_history );
				if( !empty($postmeta) ) {
					foreach ( $postmeta as $metakey => $metavalue ) {
						update_post_meta( $post_id, $metakey, $metavalue );
					}
				}
				$response['success'] = true;
				$response['post_id'] = $post_id;
				$response['postdata'] = $postdata;
				$response['postmeta'] = $postmeta;
			}
		}
		$response = json_encode( $response );
		echo $response;
		die( );
	}


	/**
	* Delete Post Shortcode
	*/
	/*
	function aione_app_builder_delete_post_shortcode(  $atts, $content = null ) {


		// Attributes
		$atts = shortcode_atts( array(
			'post_id'			=> null,
			'status'			=> 'trash',
			'class'				=> 'delete-post-form',
			'id'				=> 'delete_post_form'
		), $atts, 'delete_post' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$output 	= '';

		if ( defined('DOING_AJAX') && DOING_AJAX ) { 
			return;
		}
		if ( is_admin() ) { 
			return;
		}

		$post_id 	= $atts['post_id'];
		$status 	= $atts['status'];

		if( !empty( $post_id ) ) { 

			$delete_post_allowed 		= current_user_can( 'delete_post', $post_id );
			$post_type 					= get_post_type( $post_id );
			$post_type_object 			= get_post_type_object( $post_type );
			$post_type_singular_name	= $post_type_object->labels->singular_name;

			if( $delete_post_allowed ) {

				$output .= '<p>You are going to delete '.$post_type_singular_name . '</p>';
				$output .= '<p>Are you sure?</p>';
				$output .= '';


				if( $status == 'trash' ) {

					$trashed = wp_trash_post( $post_id );

					if( $trashed ) {
						$output .= 'The '.$post_type_singular_name.' has been trashed successfully';
					}

				} elseif( $status == 'delete' ) {

					$deleted = wp_delete_post( $post_id );

					if( $deleted ) {
						$output .= 'The '.$post_type_singular_name.' has been deleted successfully';
					}

				} else{

					$args = array(
						'ID' 			=> $post_id ,
						'post_status' 	=> $status,
					 );

					$updated = wp_update_post( $args );

					if( $updated ) {
						$output .= 'The '.$post_type_singular_name.' updated to '.$status.' successfully';
					}

				} // if( $status == 'trash' ) 
			} // if( $delete_post_allowed ) 
		} // if( !empty( $post_id ) ) 

		return $output;
	}
	*/

	function aione_app_builder_youtube_shortcode( $atts, $content = null ) {
		// Attributes
		$atts = shortcode_atts( array(
			'url'				=> '',
			'width'				=> '',
			'height'			=> '',
		), $atts, 'youtube' );

		$atts = $this->clean_shortcode_parameters( $atts );

		$url = $atts['url'];
		$width = $atts['width'];
		$height = $atts['height'];

		$output 	= '';

		$output .= '<iframe loading="lazy" width="'.$width.'" height="'.$height.'" src="'.$url.'" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';

		return $output;
	}

	function aione_app_builder_copyright_shortcode( $atts, $content = null ) {
		$output = '';
		$output .= '<p>© ' . date('Y') . ' <a href="' . home_url() . '">' . get_bloginfo( 'name' ) . '</a>. All rights reserved.</p>';

		return $output;
	}

}