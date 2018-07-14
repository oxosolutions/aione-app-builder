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
 * @author     OXO Solutions <contact@oxosolutions.com>
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
		if ( defined( 'PLUGIN_NAME_VERSION' ) ) {
			$this->version = PLUGIN_NAME_VERSION;
		} else {
			$this->version = '1.0.1';
		}
		$this->plugin_name = 'aione-app-builder';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-components-utils.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aione-admin-page.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aione-admin-component.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-component-edit.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-components-list-table.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/common-functions.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/component-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-taxonomies-list-table.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-aione-admin-taxonomy.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-taxonomy-edit.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/taxonomy-functions.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-template-edit.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-templates-list-table.php';

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
		$plugin_admin_component = new Aione_Admin_Edit_Component();
		$plugin_admin_taxonomy = new Aione_Admin_Edit_Taxonomy();
		$plugin_admin_template = new Aione_Admin_Edit_Template();
		

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

		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		//$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		//Redirect to a login page instead of wp-login.php if login is failed
        $this->loader->add_action('wp_login_failed', $plugin_public, 'aione_app_builder_login_fail_redirect_filter');

        //Set Page Filters from Setting Menu
        $this->loader->add_filter( 'login_url', $plugin_public, 'aione_app_builder_login_url', 10, 2 );
        $this->loader->add_filter( 'login_redirect',$plugin_public, 'admin_login_redirect', 10, 3 );
        $this->loader->add_filter( 'register_url',$plugin_public, 'aione_app_builder_register_url',10 ,1);
        $this->loader->add_filter( 'lostpassword_url',$plugin_public, 'aione_app_builder_lostpassword_url', 10, 2 );
        $this->loader->add_action( 'wp_logout',$plugin_public, 'logout_redirect' );

        //Add Shortcode
		$this->loader->add_shortcode( 'login-link', $plugin_public, 'aione_app_builder_login_link_shortcode' );
		$this->loader->add_shortcode( 'register-link', $plugin_public, 'aione_app_builder_register_link_shortcode' );
		$this->loader->add_shortcode( 'reset-password-link', $plugin_public, 'aione_app_builder_reset_password_link_shortcode' );
		$this->loader->add_shortcode( 'logout-link', $plugin_public, 'aione_app_builder_logout_link_shortcode' );
		$this->loader->add_shortcode( 'is_user_logged_in', $plugin_public, 'aione_app_builder_is_user_logged_in_shortcode' );
		$this->loader->add_shortcode( 'user_not_logged_in', $plugin_public, 'aione_app_builder_user_not_logged_in_shortcode' );
		$this->loader->add_shortcode( 'user_not_logged_in_error', $plugin_public, 'aione_app_builder_user_not_logged_in_error_shortcode' );
		$this->loader->add_shortcode( 'access', $plugin_public, 'aione_app_builder_access_check_shortcode' );
		$this->loader->add_shortcode( 'id', $plugin_public, 'aione_app_builder_id_shortcode' );
		$this->loader->add_shortcode( 'login', $plugin_public, 'aione_app_builder_login_shortcode' );
		$this->loader->add_shortcode( 'register', $plugin_public, 'aione_app_builder_register_shortcode' );
		$this->loader->add_shortcode( 'reset-password', $plugin_public, 'aione_app_builder_reset_password_shortcode' );
		$this->loader->add_shortcode( 'account', $plugin_public, 'aione_app_builder_account_shortcode' );
		$this->loader->add_shortcode( 'account-menu', $plugin_public, 'aione_app_builder_account_menu_shortcode' );
		$this->loader->add_shortcode( 'account-content', $plugin_public, 'aione_app_builder_account_content_shortcode' );
		$this->loader->add_shortcode( 'profile', $plugin_public, 'aione_app_builder_profile_shortcode' );
		$this->loader->add_shortcode( 'edit-profile', $plugin_public, 'aione_app_builder_edit_profile_shortcode' );
		$this->loader->add_shortcode( 'change-password', $plugin_public, 'aione_app_builder_change_password_shortcode' );
		$this->loader->add_shortcode( 'home_url', $plugin_public, 'aione_app_builder_home_url_shortcode' );
		$this->loader->add_shortcode( 'url', $plugin_public, 'aione_app_builder_url_shortcode' );
		$this->loader->add_shortcode( 'list-posts', $plugin_public, 'aione_app_builder_list_post_shortcode' );
		$this->loader->add_shortcode( 'list-comments', $plugin_public, 'aione_app_builder_list_comments_shortcode' );
		$this->loader->add_shortcode( 'faq', $plugin_public, 'aione_app_builder_faq_shortcode' );
		$this->loader->add_shortcode( 'users', $plugin_public, 'aione_app_builder_users_shortcode' );
		$this->loader->add_shortcode( 'user', $plugin_public, 'aione_app_builder_user_shortcode' );

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

	

}
