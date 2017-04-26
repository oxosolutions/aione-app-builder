<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       www.sgssandhu.com
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
 * @author     SGS Sandhu <contact@oxosolutions.com>
 */
class Aione_App_Builder {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Aione_App_Builder_Loader    $loader    Maintains and registers all hooks for the plugin.
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

		$this->plugin_name = 'aione-app-builder';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	
		/**
		*
		*Set Pages Filters
		*
		*/
		//add_filter login URL
		$aione_app_builder_login_page_maincheck = get_option('aione_app_builder_login_page'); 
			if(isset($aione_app_builder_login_page_maincheck)){
				if(!empty($aione_app_builder_login_page_maincheck)){
						add_filter( 'login_url', array($this, 'aione_app_builder_login_url'), 10, 2 );
				}
			}
		//add_filter login_redirect
		$admin_login_redirect_page_maincheck = get_option('admin_login_redirect_page');
			if(isset($admin_login_redirect_page_maincheck)){
				if(!empty($admin_login_redirect_page_maincheck)){
					add_filter('login_redirect', array($this, 'admin_login_redirect'), 10, 3);
				} 
			}
		//add_filter aione_register_page
		   $aione_app_builder_register_page_maincheck = get_option('aione_app_builder_register_page');
				if(isset($aione_app_builder_register_page_maincheck)){
					if(!empty($aione_app_builder_register_page_maincheck)){
						add_filter( 'register_url', array($this, 'aione_app_builder_register_url') );
					}
				}
		
		//add_filter aione_app_builder_forgot_password_page
		$aione_app_builder_forgot_password_page_maincheck = get_option('aione_app_builder_forgot_password_page');
			if(isset($aione_app_builder_forgot_password_page_maincheck)){
				if(!empty($aione_app_builder_forgot_password_page_maincheck)){
					add_filter( 'lostpassword_url',  array($this, 'aione_app_builder_lostpassword_url'), 10, 0 );
				}
			}
		
		
		//add_filter logout_redirect_page
		$logout_redirect_page_maincheck = get_option('logout_redirect_page');
			if(isset($logout_redirect_page_maincheck)){
				if(!empty($logout_redirect_page_maincheck)){
					add_action('wp_logout',array($this, 'logout_redirect'));
				}
			}
			
		
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Aione_App_Builder_Loader. Orchestrates the hooks of the plugin.
	 * - Aione_App_Builder_i18n. Defines internationalization functionality.
	 * - Aione_App_Builder_Admin. Defines all hooks for the admin area.
	 * - Aione_App_Builder_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aione-app-builder-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-aione-app-builder-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aione-app-builder-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aione-app-builder-admin-shortcodes.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aione-app-builder-admin-templates.php';
		
		
		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-aione-app-builder-public.php';

		$this->loader = new Aione_App_Builder_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Aione_App_Builder_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Aione_App_Builder_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Aione_App_Builder_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Aione_App_Builder_Public( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Aione_App_Builder_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}
	
	//change default Login url 
	function aione_app_builder_login_url( $login_url, $redirect ) {
		$aione_app_builder_login_page = get_option('aione_app_builder_login_page');
		if(isset($aione_app_builder_login_page)){
			if(!empty($aione_app_builder_login_page)){
				return get_permalink($aione_app_builder_login_page);
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
	//change default Register url
	function aione_app_builder_register_url( $register_url ) {
		$aione_app_builder_register_page = get_option('aione_app_builder_register_page');
		if(isset($aione_app_builder_register_page)){
			if(!empty($aione_app_builder_register_page)){
				return get_permalink($aione_app_builder_register_page);
			}
		}
	}
	//change default lost password url
	function aione_app_builder_lostpassword_url() {
		$aione_app_builder_forgot_password_page = get_option('aione_app_builder_forgot_password_page');
		if(isset($aione_app_builder_forgot_password_page)){
			if(!empty($aione_app_builder_forgot_password_page)){
				return get_permalink($aione_app_builder_forgot_password_page);
			}
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
	
	

}
