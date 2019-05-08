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
 * @author     OXO Solutions <contact@oxosolutions.com>
 */

class Aione_App_Builder_Admin {

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
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $menifest;
	private $icon_sizes;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		/*if ( ! is_admin() || ! is_user_logged_in() ) {
			return;
		} *///Only for admin

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		define('AIONE_OPTION_NAME_COMPONENTS', 'aione-components');
		define('AIONE_OPTION_NAME_TAXONOMIES', 'aione-taxonomies');
		define('AIONE_OPTION_NAME_TEMPLATES', 'aione-templates');

		add_action( 'init', array( $this,'aione_init'), AIONE_INIT_PRIORITY );
		add_action( 'admin_notices', array( $this, 'show_admin_messages' ) );
		add_action( 'admin_menu',array( &$this, 'admin_menu' ) );
		add_action('admin_init', array( $this,'init_dialog_scripts'));		
		add_action( 'admin_init',array( $this, 'aione_register_acf_menu' ), 10 );
		add_action( 'admin_init',array( $this, 'aione_reset_all_submit' ), 10 );
		// TO DO: To add Members Plugin Menu in Aione App Builder
		//add_action( 'admin_init',array( $this, 'aione_register_members_menu' ), 1000 );
		add_filter( 'aione_filter_register_menu_pages', array( $this, 'register_page_dashboard_in_menu' ), 1000 );
		add_filter( 'aione_filter_register_menu_pages',array( $this, 'aione_register_menu_pages' ), 10 );
		//add_action('wp_head', array( $this, 'aione_ajaxurl'));
		add_action( 'init', array($this,'aione_init_components_taxonomies'), apply_filters('aione_init_components_taxonomies', 10));

		// Call Function to store PWA settings value into database.
		add_action('init', array($this, 'pwa_settings_store_in_database'));
		//add_action('wp_head', array($this,'pwa_manifest_include'));


		$this->icon_sizes = array('16','32','72','96','128','144','152','192','384','512');
		
		$this->menifest = array(
			"dir" => "ltr",
			"lang" => "en",
		    "name" => "Website",
		    "scope" => "/",
		    "display" => "standalone",
		    "start_url" => ".",
		    "short_name" => "Aione",
		    "theme_color" => "#1570a6",
		    "description" => "Website Description",
		    "orientation" => "any",
		    "background_color" => "#1570a6",
		    "related_applications" => [],
		    "prefer_related_applications" => false,
		    "icons" => array()
		);

	}

	function aione_init(){
		global $aione;
		// Set post object
	    $aione->post = new stdClass();

	    // Define exceptions - privileged plugins and their data
	    $aione->aione_post_types = array(
	        'view', 'view-template', 'cred-form', 'cred-user-form'
	    );
	    // 'attachment' = Media
	    //
	    $aione->excluded_post_types = array(
	        'cred-form',
	        'cred-user-form',
		    'custom_css',
		    'customize_changeset',
	        'dd_layouts',
	        'deprecated_log',
	        'mediapage',
	        'nav_menu_item',
	        'revision',
	        'view',
	        'view-template',
	        'wp-types-group',
	        'wp-types-user-group',
		    'wp-types-term-group',
		    'acf-field-group',
		    'acf'
	    );

	    /**
	     * Filter that allows to add own post types which will be not used in Toolset plugins.
	     *
	     * @param string[] $post_types array of post type slugs.
	     * @since 1.9
	     */
	    $aione->excluded_post_types = apply_filters( 'aione_filter_exclude_own_post_types', $aione->excluded_post_types );
	}

	function init_dialog_scripts(){
   
        wp_enqueue_script('jquery-ui-dialog'); 
        wp_enqueue_script('postbox');
        
    }

    function aione_ajaxurl() {
	   echo '<script>
	           var ajaxurl = "' . admin_url('admin-ajax.php') . '";
	         </script>';
	}

	/**
	 * Register the stylesheets for the admin area.
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

		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/aione-app-builder-admin.css', array(), $this->version, 'all' );
		global $pagenow, $typenow;
	}

	/**
	 * Register the JavaScript for the admin area.
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

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/aione-app-builder-admin.js', array( 'jquery' ), $this->version, false );

		$screen = get_current_screen();
		//echo "<pre>";print_r($screen);echo "</pre>";
		$aione_admin_pages = array(
			//'dashboard',
			'toplevel_page_aione-dashboard',
			'aione-app-builder_page_aione-shortcodes',
			'aione-app-builder_page_aione-settings'
		);

		if ( in_array($screen->id, $aione_admin_pages)) {
			wp_enqueue_style( 'aione', get_template_directory_uri() . '/assets/css/aione.min.css', array(), '' );
		}

	}

	public function show_admin_messages() {
		if ( ! empty( $this->admin_messages ) ) {
			$types = array( 'error', 'updated', 'notice' );
			foreach ( $this->admin_messages as $message ) {
				$class = in_array( $message['type'], $types ) ? $message['type'] : 'updated';
				?>
                <div class="<?php echo $class ?>">
                    <p>
						<?php echo $message['text'] ?>
                    </p>
                </div>
				<?php
			}
		}
	}

	public function admin_menu(){ 
		$registered_pages = apply_filters( 'aione_filter_register_menu_pages', array() );
		//echo "I am here<pre>";print_r($registered_pages);echo "</pre>";
		if ( count( $registered_pages ) > 0 ) {
            $top_level_page_registered = false;
            while (
                count( $registered_pages ) > 0
                && ! $top_level_page_registered
            ) {
                $top_level_page = array_shift( $registered_pages );
                $top_level_page['capability'] = isset( $top_level_page['capability'] ) ? $top_level_page['capability'] : 'manage_options';
                if ( current_user_can( $top_level_page['capability'] ) ) {
                    $hook = add_menu_page( $top_level_page['page_title'], 'Aione App Builder', $top_level_page['capability'], $top_level_page['slug'], array($this,$top_level_page['callback'] ), 'dashicons-grid-view', '62');
                    $this->add_menu_page_hooks( @$page, $hook );
                    $top_level_page_registered = true;
                }
            }

            if (
                $top_level_page_registered
                && is_array( $registered_pages )
            ) {
                $this->add_submenu_page( $top_level_page, $top_level_page );
                foreach ( $registered_pages as $page ) {
                    $this->add_submenu_page( $page, $top_level_page );
                }
            }
        }
	}

	public function add_submenu_page( $page, $top_level_page ) {
        $page['capability'] = isset( $page['capability'] ) ? $page['capability'] : 'manage_options';
        $callback = isset( $page['callback'] ) ? $page['callback'] : null;
        $hook = add_submenu_page( $top_level_page['slug'], $page['page_title'], $page['menu_title'], $page['capability'], $page['slug'], array($this,$callback ));
        $this->add_menu_page_hooks( $page, $hook );
    }

    public function add_menu_page_hooks( $page, $hook ) {
        global $wp_version;
        $load_action = sprintf(
            'load-%s',
            $hook
        );
        if (! empty( $page['load_hook'] ) && is_callable( array($this,$page['load_hook'] ) )
            ) { 
                add_action( $load_action, array($this,$page['load_hook'] ));
            }
    }

    public function register_page_dashboard_in_menu( $pages ) {
		array_unshift( $pages, array(
			'slug'			=> 'aione-dashboard',
			'menu_title'	=> __( 'Dashboard', 'aione-app-builder' ),
			'page_title'	=> __( 'Aione Dashboard', 'aione-app-builder' ),
			'callback'		=> 'aione_admin_menu_summary_dashboard'
		) );

		return $pages;
	}

    public function aione_register_menu_pages( $pages ) {
		$current_page = '';
		if ( isset( $_GET['page'] ) ) {
		    $current_page = sanitize_text_field( $_GET['page'] );
		}
		$pages['aione-component'] = array(
			'slug'				=> 'aione-component',
	        'menu_title'		=> __( 'Components', 'aione-app-builder' ),
	        'page_title'		=> __( 'Components', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_menu_summary_component',
	        'capability'		=> 'manage_options',
	    );
		$pages['aione-component']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-component'] );

		if ( $current_page == 'aione-edit-component' ) {
			$pages['aione-edit-component'] = array(
				'slug'				=> 'aione-edit-component',
				'menu_title'		=> isset( $_GET['aione-component-slug'] ) ? __( 'Edit Component', 'aione-app-builder' ) : __( 'Add New Component', 'aione-app-builder' ),
				'page_title'		=> isset( $_GET['aione-component-slug'] ) ? __( 'Edit Component', 'aione-app-builder' ) : __( 'Add New Component', 'aione-app-builder' ),
				'callback'  		=> 'aione_admin_menu_edit_component',
				'capability'		=> 'manage_options',
				'load_hook'			=> 'aione_admin_menu_edit_component_hook'
			);
			
			$pages['aione-edit-component']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-edit-component'] );
		}
		$pages['aione-taxonomy'] = array(
			'slug'				=> 'aione-taxonomy',
	        'menu_title'		=> __( 'Taxonomies', 'aione-app-builder' ),
	        'page_title'		=> __( 'Taxonomies', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_menu_summary_taxonomy',
	        'capability'		=> 'manage_options',
	    );
		$pages['aione-taxonomy']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-taxonomy'] );

		if ( $current_page == 'aione-edit-taxonomy' ) {
			$pages['aione-edit-taxonomy'] = array(
				'slug'				=> 'aione-edit-taxonomy',
				'menu_title'		=> isset( $_GET['aione-taxonomy-slug'] ) ? __( 'Edit Taxonomy', 'aione-app-builder' ) : __( 'Add New Taxonomy', 'aione-app-builder' ),
				'page_title'		=> isset( $_GET['aione-taxonomy-slug'] ) ? __( 'Edit Taxonomy', 'aione-app-builder' ) : __( 'Add New Taxonomy', 'aione-app-builder' ),
				'callback'  		=> 'aione_admin_menu_edit_taxonomy',
				'capability'		=> 'manage_options',
				'load_hook'			=> 'aione_admin_menu_edit_taxonomy_hook'
			);
			
			$pages['aione-edit-taxonomy']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-edit-taxonomy'] );
		}

		$pages['aione-template'] = array(
			'slug'				=> 'aione-template',
	        'menu_title'		=> __( 'Templates', 'aione-app-builder' ),
	        'page_title'		=> __( 'Templates', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_menu_summary_template',
	        'capability'		=> 'manage_options',
	    );
		$pages['aione-template']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-template'] );

		if ( $current_page == 'aione-edit-template' ) {
			$pages['aione-edit-template'] = array(
				'slug'				=> 'aione-edit-template',
				'menu_title'		=> isset( $_GET['aione-template-slug'] ) ? __( 'Edit Template', 'aione-app-builder' ) : __( 'Add New Template', 'aione-app-builder' ),
				'page_title'		=> isset( $_GET['aione-template-slug'] ) ? __( 'Edit Template', 'aione-app-builder' ) : __( 'Add New Template', 'aione-app-builder' ),
				'callback'  		=> 'aione_admin_menu_edit_template',
				'capability'		=> 'manage_options',
				'load_hook'			=> 'aione_admin_menu_edit_template_hook'
			);
			
			$pages['aione-edit-template']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-edit-template'] );
		}

		$pages['aione-shortcodes'] = array(
			'slug'				=> 'aione-shortcodes',
	        'menu_title'		=> __( 'Shortcodes', 'aione-app-builder' ),
	        'page_title'		=> __( 'Aione Shortcodes', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_menu_summary_shortcodes',
	        'capability'		=> 'manage_options',
	    );
		$pages['aione-shortcodes']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-shortcodes'] );

		$pages['aione-settings'] = array(
			'slug'				=> 'aione-settings',
	        'menu_title'		=> __( 'Settings', 'aione-app-builder' ),
	        'page_title'		=> __( 'Aione Settings', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_menu_summary_settings',
	        'capability'		=> 'manage_options',
	    );
		$pages['aione-settings']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-settings'] );

		$pages['aione-pwa'] = array(
			'slug'				=> 'aione-pwa',
	        'menu_title'		=> __( 'Progressive Web App', 'aione-app-builder' ),
	        'page_title'		=> __( 'Aione Progressive Web App', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_menu_summary_pwa',
	        'capability'		=> 'manage_options',
	    );
		$pages['aione-pwa']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-pwa'] );

		$pages['aione-reset-all'] = array(
			'slug'				=> 'aione-reset-all',
	        'menu_title'		=> __( 'Reset All', 'aione-app-builder' ),
	        'page_title'		=> __( 'Reset All', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_reset_all',
	        'capability'		=> 'manage_options',
	    );
		//echo "<pre>";print_r($pages);echo "</pre>";
		return $pages;
		
	}

	/**
	 * ACF menu in Main Menu
	 */
	public function aione_register_acf_menu()	{
		if (class_exists('acf')){
			$acf_version = get_option('acf_version', false);
			if($acf_version < 5){
				remove_menu_page( 'edit.php?post_type=acf' );
				add_submenu_page( 'aione-dashboard', __( 'Field Groups', 'aione-app-builder' ), __( 'Custom Fields', 'aione-app-builder' ), 'manage_options', 'edit.php?post_type=acf' );
			} else {
				remove_menu_page( 'edit.php?post_type=acf-field-group' );
				add_submenu_page( 'aione-dashboard', __( 'Field Groups', 'aione-app-builder' ), __( 'Fields', 'aione-app-builder' ), 'manage_options', 'edit.php?post_type=acf-field-group' );
				add_submenu_page( 'aione-dashboard', __( 'Import/Export Field Groups', 'aione-app-builder' ), __( 'Import/Export Fields', 'aione-app-builder' ), 'manage_options', 'edit.php?post_type=acf-field-group&page=acf-tools' );
			}
			
			add_action('admin_head', array($this,'acf_page_style'));
		}
	}

	function acf_page_style(){
		?>
<style type="text/css"> 
	#acf-field-group-wrap .acf-columns-2 {margin-right: 0;}
	#acf-field-group-wrap .acf-column-2 {display: none;}
</style>
		<?php
	}

	/**
	 * Members menu in Main Menu
	 */
	public function aione_register_members_menu()	{
		if (class_exists('Members_Plugin')){
			remove_submenu_page( 'users.php', 'roles' );
			// Get the page title.
			$title = __( 'Roles', 'aione-app-builder' );
			if ( isset( $_GET['action'] ) && 'edit' === $_GET['action'] && isset( $_GET['role'] ) )
				$title = __( 'Edit Role', 'aione-app-builder' );
			$edit_roles_cap = 'list_roles';

			// If the current user can 'edit_roles'.
			if ( current_user_can( 'edit_roles' ) )
				$edit_roles_cap = 'edit_roles';

			// If the current user can 'delete_roles'.
			elseif ( current_user_can( 'delete_roles' ) )
				$edit_roles_cap = 'delete_roles';
			add_submenu_page( 'aione-dashboard', $title, __( 'Roles', 'aione-app-builder' ), 'manage_options', 'admin.php?page=roles' );
			
		}
	}

	 /**
	 * Menu page display.
	 */
	function aione_admin_menu_summary_dashboard(){
		aione_add_admin_header(
	        __( 'Aione Dashboard', 'aione-app-builder' )
	    );
	    aione_admin_dashboard_boxes();
	    aione_add_admin_footer();
	}
	function aione_admin_menu_summary_component(){
		aione_add_admin_header(
	        __( 'Components', 'aione-app-builder' ),
	        array('page'=>'aione-edit-component'),
	        __('Add New', 'aione-app-builder')
	    );
	    aione_admin_components_list();
    	aione_add_admin_footer();
	}
	function aione_admin_menu_summary_taxonomy(){
		aione_add_admin_header(
	        __( 'Taxonomies', 'aione-app-builder' ),
	        array('page'=>'aione-edit-taxonomy'),
	        __('Add New', 'aione-app-builder')
	    );
	    aione_admin_taxonomies_list();
    	aione_add_admin_footer();
	}
	function aione_admin_menu_summary_template(){
		aione_add_admin_header(
	        __( 'Templates', 'aione-app-builder' ),
	        array('page'=>'aione-edit-template'),
	        __('Add New', 'aione-app-builder')
	    );
	    aione_admin_templates_list();
    	aione_add_admin_footer();
	}

	function aione_admin_menu_summary_shortcodes(){
		include( plugin_dir_path( __FILE__ ) . 'partials/aione-app-builder-admin-shortcodes-table.php' );
	}

	function aione_admin_menu_summary_settings(){
		$this->tabs();
		if ( isset( $_POST['action'] ) && $_POST['action'] == "save" ){
			$validation_key = $_POST['save_set_page'];
			if ( ! isset( $validation_key )  || ! wp_verify_nonce( $validation_key, 'validation_key' ) ) {
				echo "Access denied.";
				exit;
			} else {
				if(isset( $_POST['filter'] ) && $_POST['filter'] == "login-tab"){
					$enable_login_page_captcha = $_POST['enable_login_page_captcha'];
					$aione_app_builder_login_page = $_POST['aione_app_builder_login_page'];
					$admin_login_redirect_page = $_POST['admin_login_redirect_page'];
					$enable_two_factor_auth = $_POST['enable_two_factor_auth'];
					$login_page_tfa_role = $_POST['login_page_tfa_role'];
					$two_factor_auth = $_POST['two_factor_auth'];
					$login_page_otp_mobile_field = $_POST['login_page_otp_mobile_field'];
					$sms_service_provider = $_POST['sms_service_provider'];
					$twillio_sms_service_provider_key = str_replace(' ', '',$_POST['twillio_sms_service_provider_key']);
					$twillio_sms_service_provider_secret = str_replace(' ', '',$_POST['twillio_sms_service_provider_secret']);
					$twillio_sms_service_provider_phone_number = $_POST['twillio_sms_service_provider_phone_number'];
					$msgclub_sms_service_provider_key = str_replace(' ', '', $_POST['msgclub_sms_service_provider_key']);

					update_option( 'enable_login_page_captcha', $enable_login_page_captcha );
					update_option( 'aione_app_builder_login_page', $aione_app_builder_login_page );
					update_option( 'admin_login_redirect_page', $admin_login_redirect_page );
					update_option( 'enable_two_factor_auth', $enable_two_factor_auth );
					update_option( 'login_page_tfa_role', $login_page_tfa_role );
					update_option( 'two_factor_auth', $two_factor_auth );
					update_option( 'login_page_otp_mobile_field', $login_page_otp_mobile_field );
					update_option( 'sms_service_provider', $sms_service_provider );
					update_option( 'twillio_sms_service_provider_key', $twillio_sms_service_provider_key );
					update_option( 'twillio_sms_service_provider_secret', $twillio_sms_service_provider_secret );
					update_option( 'twillio_sms_service_provider_phone_number', $twillio_sms_service_provider_phone_number );
					update_option( 'msgclub_sms_service_provider_key', $msgclub_sms_service_provider_key );
				}
				if(isset( $_POST['filter'] ) && $_POST['filter'] == "register-tab"){
					$aione_app_builder_register_page = $_POST['aione_app_builder_register_page'];
					$aione_app_builder_registration_custom_field_groups = $_POST['aione_app_builder_registration_custom_field_groups'];
					
					update_option( 'aione_app_builder_register_page', $aione_app_builder_register_page );
					update_option( 'aione_app_builder_registration_custom_field_groups', $aione_app_builder_registration_custom_field_groups );
				}
				if(isset( $_POST['filter'] ) && $_POST['filter'] == "lost-password-tab"){
					$aione_app_builder_forgot_password_page = $_POST['aione_app_builder_forgot_password_page'];
					update_option( 'aione_app_builder_forgot_password_page', $aione_app_builder_forgot_password_page );
				}
				if(isset( $_POST['filter'] ) && $_POST['filter'] == "logout-tab"){
					$logout_redirect_page = $_POST['logout_redirect_page'];
					update_option( 'logout_redirect_page', $logout_redirect_page );
				}
			}
		}
		if(isset ( $_GET['tab'] ) && $_GET['tab'] == 'register'){
			require_once plugin_dir_path( __FILE__ ) . 'partials/aione-app-builder-admin-register-tab.php';
		} 
		else if(isset ( $_GET['tab'] ) && $_GET['tab'] == 'forgot_Password'){
			require_once plugin_dir_path( __FILE__ ) . 'partials/aione-app-builder-admin-lost-password-tab.php';
		}
		else if(isset ( $_GET['tab'] ) && $_GET['tab'] == 'logout'){
			require_once plugin_dir_path( __FILE__ ) . 'partials/aione-app-builder-admin-logout-tab.php';
		}
		else {
			require_once plugin_dir_path( __FILE__ ) . 'partials/aione-app-builder-admin-login-tab.php';
		}
	}

	function aione_admin_menu_summary_pwa(){
		global $wpdb;
		global $post;
		$site_title = get_bloginfo( 'name' );
		$site_short_name = ( mb_strstr(get_bloginfo('name'), ' ', true, 'utf-8') ) ? mb_strstr(get_bloginfo('name'), ' ', true, 'utf-8') : get_bloginfo('name');
		$pwa_settings = unserialize(get_option('pwa_settings',true));
		//echo "<pre>";print_r($pwa_settings);echo "</pre>";

		$pwa_name = ($pwa_settings['pwa_app_name']) ? $pwa_settings['pwa_app_name'] : $site_title;
		$short_name = ($pwa_settings['pwa_short_name']) ? $pwa_settings['pwa_short_name'] : $site_short_name;
		$description = ($pwa_settings['pwa_description']) ? $pwa_settings['pwa_description'] : get_bloginfo('description');
		$theme_color = ($pwa_settings['pwa_theme_color']) ? $pwa_settings['pwa_theme_color'] : "#323a45";
		$background_color = ($pwa_settings['pwa_background_color']) ? $pwa_settings['pwa_background_color'] : "#2196F3";
		$scope = ($pwa_settings['pwa_scope']) ? $pwa_settings['pwa_scope'] : "";
		$start_url = ($pwa_settings['pwa_start_url']) ? $pwa_settings['pwa_start_url'] : trailingslashit( get_bloginfo('url') );
		$icon = @$pwa_settings['pwa_icon'];

		$upload_dir = wp_upload_dir();
		$target_dir = $upload_dir['baseurl']."/pwa/images";
		$icon_path = $target_dir."/".$icon;

		echo "<h1>Progressive Web Application</h1>";
		$form = '';
		$form .= '<div class="wrap">
				<form name="" class="" id="" method="post" action="" enctype="multipart/form-data">
				<table class="form-table">
				<tbody>
				<tr>
				<th scope="row"><label for="name">App Name</label></th>
				<td><input placeholder="" id="name" name="name" type="text" class="regular-text" value="'.$pwa_name.'"></td>
		       </tr>
				<tr>
				<th scope="row"><label for="short_name">Short Name</label></th>
				<td><input placeholder="" id="short_name" name="short_name" type="text" class="regular-text" value="'.$short_name.'"></td>
				</tr>
				<tr>
				<th scope="row"><label for="description">Description</label></th>
				<td><input placeholder="" id="description" name="description" type="text" class="regular-text" value="'.$description.'"></td>
				</tr>
				<tr>
				<th scope="row"><label for="theme_color">Theme Color</label></th>
				<td><input placeholder="" id="theme_color" name="theme_color" type="text" class="regular-text" value="'.$theme_color.'"></td>
				</tr>
				<tr>
				<th scope="row"><label for="background_color">Background Color</label></th>
				<td><input placeholder="" id="background_color" name="background_color" type="text" class="regular-text" value="'.$background_color.'"></td>
				</tr>
				<tr>
				<th scope="row"><label for="display">Display Mode</label></th>
				<td><select name="display" id="display">';

				
		        $form .= '<option value="standalone"';
		        if($pwa_settings['pwa_display'] == "standalone"){
					$form .= ' selected ';
				}
		        $form .= '>Standalone</option>';
		        $form .= '<option value="minimal-ui"';
		        if($pwa_settings['pwa_display'] == "minimal-ui"){
					$form .= ' selected ';
				}
		        $form .= '>Minimal UI</option>';
		        $form .= '<option value="browser"'; 
				if($pwa_settings['pwa_display'] == "browser"){
					$form .= ' selected ';
				}
				$form .= '>Browser</option>';
		        $form .= '<option value="fullscreen"';
		        if($pwa_settings['pwa_display'] == "fullscreen"){
					$form .= ' selected ';
				}
		        $form .= '>Fullscreen</option>';
		        $form .= '</select>';
				$form .= '</td>
				</tr>
				<tr>
				<th scope="row"><label for="orientation">Orientation</label></th>
				<td><select id="orientation" name="orientation" class="initialized">';
		            $form .= '<option value="any" ';
			        if($pwa_settings['pwa_orientation'] == "any"){
						$form .= ' selected ';
					}
		        	$form .= '>Any</option>';
		            $form .= '<option value="portrait"';
			        if($pwa_settings['pwa_orientation'] == "portrait"){
						$form .= ' selected ';
					}
			        $form .= '>Portrait</option>';
		            $form .= '<option value="landscape"';
			        if($pwa_settings['pwa_orientation'] == "landscape"){
						$form .= ' selected ';
					}
			        $form .= '>Landscape</option>';
		          $form .= '</select>
				</td>
				</tr>
				<tr>
				<th scope="row"><label for="scope">Application Scope</label></th>
				<td><input placeholder="" id="scope" name="scope" type="text" class="regular-text" value="'.$scope.'"></td>
				</tr>
				<tr>
				<th scope="row"><label for="start_url">Start URL</label></th>
				<td><input placeholder="" id="start_url" name="start_url" type="text" class="regular-text" value="'.$start_url.'" autocomplete="off"></td>
				</tr>';
				/*<tr>
				<th scope="row"><label for="icon">Icon</label></th>
				<td>';
				if( has_site_icon() ) :
								
					$form .= '<a href="'.esc_url( admin_url('customize.php?autofocus[section]=title_tagline') ).'" title="Change image" id="webmanifest_icons">
						<img src="'.get_site_icon_url(300).'" alt="Site icon preview" />
					</a>';
				else :
					
				$form .= '<p>No image selected</p>
					
					<a href="'.esc_url( admin_url('customize.php?autofocus[section]=title_tagline') ).'" title="Select image" id="webmanifest_icons" class="button">
						Select image
					</a>';
				endif; 
				$form .= '</td>
				</tr>*/
				$form .= '<tr>
				<th scope="row"><label for="icon">Icon</label></th>
				<td>';
				if($icon != ""){
					$form .= '<div class="img-wrap"><span class="close">&times;</span><img src="'.$icon_path.'" id="preview" width="72" /></div>';
				} else {
					$form .= '<div class="img-wrap hide"><span class="close">&times;</span><img id="preview" width="72" /></div>';
				}
				$form .= '<input accept="image/png" type="file" name="icon" onchange="loadFile(event)" style="display:block;">
				<input type="hidden" name="check_icon" value="">
				<p>Only .png is allowed</p></td>
				</tr>
				</table>
				<p class="submit"><input type="submit" id="submit_button" name="action" class="button button-primary" value="Save Settings">
				<input type="hidden" name="pwa_action" value="pwa_settings"></p>
				</form>
			</div>

			<style>
			.img-wrap {
			    position: relative;
			    display: inline-block;
			}
			.img-wrap .close {
			    position: absolute;
			    top: -6px;
			    right: -6px;
			    z-index: 100;
			    background-color: red;
			    padding: 5px;
			    color: white;
			    font-weight: bold;
			    cursor: pointer;
			    opacity: 0;
			    text-align: center;
			    font-size: 20px;
			    line-height: 10px;
			}
			.img-wrap:hover .close {
				opacity:1;
			}
			.hide{
				display:none;
			}
			.show {
				display:block;
			}
			</style>

			<script>
				var loadFile = function(event) {
					var image = document.getElementById("preview");
					image.src = URL.createObjectURL(event.target.files[0]);
					jQuery(".img-wrap").removeClass("hide");
				};
				jQuery(".img-wrap .close").on("click", function() {
				    var id = jQuery(this).closest(".img-wrap").find("img").attr("id");
				    jQuery("#"+id).removeAttr("src");
				    jQuery("input[name=check_icon]").val("no-image");
				    jQuery(".img-wrap").addClass("hide");
				});
			</script>';
		echo $form;		
	}

	public function pwa_settings_store_in_database(){
		$pwa_settings = get_option('pwa_settings',true);
		if($pwa_settings){			
			$pwa_settings = unserialize($pwa_settings);
		}
		if(isset($_POST['pwa_action']) && $_POST['pwa_action'] == "pwa_settings" ){
			$option_array = array();
			$option_array["pwa_app_name"] = $_POST['name'];
			$option_array["pwa_short_name"] = $_POST['short_name'];
			$option_array["pwa_description"] = $_POST['description'];
			$option_array["pwa_theme_color"] = $_POST['theme_color'];
			$option_array["pwa_background_color"] = $_POST['background_color'];
			$option_array["pwa_display"] = $_POST['display'];
			$option_array["pwa_orientation"] = $_POST['orientation'];
			$option_array["pwa_scope"] = $_POST['scope'];
			$option_array["pwa_start_url"] = $_POST['start_url'];
			if($_FILES["icon"]["name"]) {
				$new_icon_name = 'icon.png';
			    $option_array["pwa_icon"] = $new_icon_name;
			} else {
				if($_POST['check_icon'] == 'no-image'){
					$option_array["pwa_icon"] = '';
				} else {					
					$option_array["pwa_icon"] = @$pwa_settings['pwa_icon'];
				}
			}
			
			update_option('pwa_settings', serialize($option_array));

			$this->menifest['name'] = $option_array["pwa_app_name"];
			$this->menifest['short_name'] = $option_array["pwa_short_name"];
			$this->menifest['description'] = $option_array["pwa_description"];
			$this->menifest['theme_color'] = $option_array["pwa_theme_color"];
			$this->menifest['background_color'] = $option_array["pwa_background_color"];
			$this->menifest['display'] = $option_array["pwa_display"];
			$this->menifest['orientation'] = $option_array["pwa_orientation"];
			$this->menifest['scope'] = $option_array["pwa_scope"];
			$this->menifest['start_url'] = $option_array["pwa_start_url"];

			/** Create PWA folder **/
			$upload_dir = wp_upload_dir();
			$path = $upload_dir['basedir']."/pwa/";
			if (!is_dir($path)) {
				mkdir($path, 0755, true);
			}

			/** Copy Service Worker **/
		    $source = WP_CONTENT_DIR."/uploads/serviceworker.js";
		    $destination = $path."serviceworker.js";
		    copy($source, $destination);

			/** create Images folder **/
			$target_dir = $upload_dir['basedir']."/pwa/images";
			if (!is_dir($target_dir)) {
				mkdir($target_dir, 0755, true);
			}

			if($option_array["pwa_icon"] != ''){
				/** Move uploaded Image **/
				$new_icon_name = 'icon.png';
				$target_file = $target_dir."/" . $new_icon_name;
				move_uploaded_file($_FILES["icon"]["tmp_name"], $target_file);

				/** create Different Sizes icons **/
				$image = wp_get_image_editor( $target_file );
				if ( ! is_wp_error( $image ) ) {
				    foreach ($this->icon_sizes as $value) {
						$temp = array();
						$temp['src'] = "/images/icon-".$value."x".$value.".png";
						$temp['sizes'] = $value."x".$value;
						$temp['type'] = "image/png";
						array_push($this->menifest['icons'], $temp);

						$image->resize( $value, $value, true );
				    	$image->save( $target_dir.'/icon-'.$value.'x'.$value.'.png' );
					}			    
				}
			}

			/** Generate Menifest.json **/
			$filename = "manifest";
			$ext = '.json';
			$file = $path.$filename.$ext;
			if( file_exists ( $file ) ){
				unlink($file);
			}
			$output = fopen($file, "w"); 
			$saved = fwrite($output, json_encode($this->menifest,JSON_UNESCAPED_SLASHES));			
			fclose($output);

			
			if(@$saved){
				$status = "success";
				$message = "Request submitted successfully.";
			} else {
				$status = "error";
				$message = "Something went wrong";
			}

			//echo "<div style='width:80%;float:right;'>Status = ".$status."</div>";
			//echo "<div style='width:80%;float:right;'>Message = ".$message."</div>";
		}
	}

	function pwa_manifest_include(){
		$upload_dir = wp_upload_dir();
		$path = $upload_dir['basedir']."/pwa/";
		$filePath	=	$path . 'manifest.json' ;
		$pathurl = $upload_dir['baseurl']."/pwa/";
		$fileURL	=	$pathurl . 'manifest.json';
		
		if( file_exists($filePath) ){
			echo "\n" . '<!-- Web Manifest -->' . "\n";
			echo '<link rel="manifest" href="' . $fileURL . '" />' . "\n\n";
		}

	}

	function aione_admin_menu_edit_component(){
		$post_type = current_filter();
		if ( isset( $_GET['aione-component-slug'] ) ) {
            $title = __( 'Edit Component', 'aione-app-builder' );
            /**
             * add new Component link
             */
            $title .= sprintf(
                '<a href="%s" class="add-new-h2">%s</a>',
                esc_url(add_query_arg( 'page', 'aione-edit-component', admin_url('admin.php'))),
                __('Add New', 'aione-app-builder')
            );
        } else {
            $title = __( 'Add New Component', 'aione-app-builder' );
        }
        aione_add_admin_header($title);
        $form = aione_form( 'aione_form_components' );
        echo '<form method="post" action="" class="aione-fields-form aione-form-validate js-types-show-modal">';
        aione_admin_screen($post_type, $form->renderForm());
    	echo '</form>';
        aione_add_admin_footer();
	}
	function aione_admin_menu_edit_taxonomy(){
		$taxonomy = current_filter(); 
		if ( isset( $_GET['aione-taxonomy-slug'] ) ) {
            $title = __( 'Edit Taxonomy', 'aione-app-builder' );
            /**
             * add new taxonomy link
             */
            $title .= sprintf(
                '<a href="%s" class="add-new-h2">%s</a>',
                esc_url(add_query_arg( 'page', 'aione-edit-taxonomy', admin_url('admin.php'))),
                __('Add New', 'aione-app-builder')
            );
        } else {
            $title = __( 'Add New Taxonomy', 'aione-app-builder' );
        }
        aione_add_admin_header($title);
        $form = aione_form( 'aione_form_taxonomies' );
        echo '<form method="post" action="" class="aione-fields-form aione-form-validate js-types-show-modal">';
        aione_admin_screen($taxonomy, $form->renderForm());
    	echo '</form>';
        aione_add_admin_footer();
	}

	function aione_admin_menu_edit_template(){
		$template = current_filter(); 
		if ( isset( $_GET['aione-template-slug'] ) ) {
            $title = __( 'Edit Template', 'aione-app-builder' );
            /**
             * add new template link
             */
            $title .= sprintf(
                '<a href="%s" class="add-new-h2">%s</a>',
                esc_url(add_query_arg( 'page', 'aione-edit-template', admin_url('admin.php'))),
                __('Add New', 'aione-app-builder')
            );
        } else {
            $title = __( 'Add New Template', 'aione-app-builder' );
        }
        aione_add_admin_header($title);
        $form = aione_form( 'aione_form_templates' );
        echo '<form method="post" action="" class="aione-fields-form aione-form-validate js-types-show-modal">';
        aione_admin_screen($template, $form->renderForm());
    	echo '</form>';
        aione_add_admin_footer();
	}

	function aione_admin_menu_edit_component_hook(){
		//require_once dirname( __FILE__ ).'/class-component-edit.php';
	    $aione_admin = new Aione_Admin_Edit_Component();
	    $aione_admin->init_admin();
	    $form = $aione_admin->form();
	    aione_form( 'aione_form_components', $form );
	}
	function aione_admin_menu_edit_taxonomy_hook(){
		//require_once dirname( __FILE__ ).'/class-component-edit.php';
	    $aione_admin = new Aione_Admin_Edit_Taxonomy();
	    $aione_admin->init_admin();
	    $form = $aione_admin->form();
	    aione_form( 'aione_form_taxonomies', $form );
	}
	function aione_admin_menu_edit_template_hook(){
		//require_once dirname( __FILE__ ).'/class-component-edit.php';
	    $aione_admin = new Aione_Admin_Edit_Template();
	    $aione_admin->init_admin();
	    $form = $aione_admin->form();
	    aione_form( 'aione_form_templates', $form );
	}

	function aione_init_components_taxonomies(){ 
		$custom_taxonomies = get_option( AIONE_OPTION_NAME_TAXONOMIES, array() );
	    if ( !empty( $custom_taxonomies ) ) {
	        //require_once WPCF_EMBEDDED_INC_ABSPATH . '/custom-taxonomies.php';
	        aione_custom_taxonomies_init();
	    }
		
	    // register post types
		$post_type_option = new Aione_App_Builder_Admin_Components_Utils();
	    $custom_types = $post_type_option->get_components();
	    if ( !empty( $custom_types ) ) {
	        //require_once dirname( __FILE__ ). '/component-functions.php';
	        aione_custom_types_init();
	        aione_builtin_taxonomies_init();
	    }

	    
	}
	public function tabs(){
		if(isset ( $_GET['page'] ) && $_GET['page'] == 'aione-settings'){			
			if(isset ( $_GET['tab'] )){
				$selected_tab = $_GET['tab'];
			} else {
				$selected_tab = 'login';
			}		 
		}
		?>

		<h2 class="nav-tab-wrapper">
		    <a class="nav-tab <?php echo $selected_tab == 'login' ? 'nav-tab-active' : ''; ?>"
		        href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'login' ), 'admin.php?page=aione-settings' ) ) ); ?>">
		        <?php echo __( "Login", 'aione-app-builder' ); ?> </a> 
			
			<a class="nav-tab <?php echo $selected_tab == 'register' ? 'nav-tab-active' : ''; ?>"
		        href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'register' ), 'admin.php?page=aione-settings' ) ) ); ?>">
		        <?php echo __( 'Register', 'aione-app-builder' ); ?> </a>
			
			<a class="nav-tab <?php echo $selected_tab == 'forgot_Password' ? 'nav-tab-active' : ''; ?>"
		        href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'forgot_Password' ), 'admin.php?page=aione-settings' ) ) ); ?>">
		        <?php echo __( 'Forgot Password', 'aione-app-builder' ); ?> </a> 
				
			<a class="nav-tab <?php echo $selected_tab == 'logout' ? 'nav-tab-active' : ''; ?>"
		        href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'logout' ), 'admin.php?page=aione-settings' ) ) ); ?>">
		        <?php echo __( 'Log Out', 'aione-app-builder' ); ?> </a>  
				
		</h2>
		<?php
	}

	public function aione_admin_reset_all() {
		global $current_user;
		if ( isset( $_POST['aione_reset_confirm'] ) && 'reset' !== $_POST['aione_reset_confirm'] ) {
			echo '<div class="error fade"><p><strong>' . esc_html__( 'Invalid confirmation word. Please type the word "reset" in the confirmation field.', 'aione-app-builder' ) . '</strong></p></div>';
		} elseif ( isset( $_POST['_wpnonce'] ) ) {
			echo '<div class="error fade"><p><strong>' . esc_html__( 'Invalid nonce. Please try again.', 'aione-app-builder' ) . '</strong></p></div>';
		}
		
		?>
		<div class="wrap">
			<div id="icon-tools" class="icon32"><br /></div>
			<h1><?php esc_html_e( 'Reset', 'aione-app-builder' ); ?></h1>
			<h2><?php esc_html_e( 'Details about the reset', 'aione-app-builder' ); ?></h2>
			<p><strong><?php esc_html_e( 'After completing this reset you will be taken to the dashboard.', 'aione-app-builder' ); ?></strong></p>
			<?php $admin = get_user_by( 'login', 'admin' ); ?>
			<?php if ( ! isset( $admin->user_login ) || $admin->user_level < 10 ) : ?>
				<?php $user = $current_user; ?>
				<?php /* translators: The username. */ ?>
				<p><?php printf( esc_html__( 'The "admin" user does not exist. The user %s will be recreated using its current password with user level 10.', 'wp-reset' ), '<strong>' . esc_html( $user->user_login ) . '</strong>' ); ?></p>
			<?php else : ?>
				<p><?php esc_html_e( 'The "admin" user exists and will be recreated with its current password.', 'wp-reset' ); ?></p>
			<?php endif; ?>
			
			
			<h3><?php esc_html_e( 'Reset', 'aione-app-builder' ); ?></h3>
			<?php /* translators: reset. */ ?>
			<p><?php printf( esc_html__( 'Type %s in the confirmation field to confirm the reset and then click the reset button:', 'aione-app-builder' ), '<strong>reset</strong>' ); ?></p>
			<form id="aione_reset_form" action="" method="post">
				<?php wp_nonce_field( 'aione_reset' ); ?>
				<input id="aione_reset" type="hidden" name="aione_reset" value="true" />
				<input id="aione_reset_confirm" type="text" name="aione_reset_confirm" value="" />
				<p class="submit">
					<input id="aione_reset_submit" style="width: 80px;" type="submit" name="Submit" class="button-primary" value="<?php esc_html_e( 'Reset' ); ?>" />
				</p>
			</form>
		</div>
		<?php
	}

	public function aione_reset_all_submit() {
		global $current_user;

		$wordpress_reset         = ( isset( $_POST['aione_reset'] ) && 'true' == $_POST['aione_reset'] );
		$wordpress_reset_confirm = ( isset( $_POST['aione_reset_confirm'] ) && 'reset' == $_POST['aione_reset_confirm'] );
		$valid_nonce             = ( isset( $_POST['_wpnonce'] ) && wp_verify_nonce( $_POST['_wpnonce'], 'aione_reset' ) );

		if ( $wordpress_reset && $wordpress_reset_confirm && $valid_nonce ) {
			require_once ABSPATH . '/wp-admin/includes/upgrade.php';

			$blogname    = get_option( 'blogname' );
			$admin_email = get_option( 'admin_email' );
			$blog_public = get_option( 'blog_public' );

			if ( 'admin' !== $current_user->user_login ) {
				$user = get_user_by( 'login', 'admin' );
			}

			if ( empty( $user->user_level ) || $user->user_level < 10 ) {
				$user = $current_user;
			}

			global $wpdb, $reactivate_wp_reset_additional;

			$prefix = str_replace( '_', '\_', $wpdb->prefix );
			$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
			foreach ( $tables as $table ) {
				$wpdb->query( "DROP TABLE $table" );
			}

			$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );
			extract( $result, EXTR_SKIP );

			$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
			$wpdb->query( $query );

			$get_user_meta    = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
			$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';

			if ( $get_user_meta( $user_id, 'default_password_nag' ) ) {
				$update_user_meta( $user_id, 'default_password_nag', false );
			}

			if ( $get_user_meta( $user_id, $wpdb->prefix . 'default_password_nag' ) ) {
				$update_user_meta( $user_id, $wpdb->prefix . 'default_password_nag', false );
			}


			wp_clear_auth_cookie();
			wp_set_auth_cookie( $user_id );

			wp_redirect( admin_url() . '?reset' );
			exit();
		}

		if ( array_key_exists( 'reset', $_GET ) && stristr( $_SERVER['HTTP_REFERER'], 'wordpress-reset' ) ) {
			add_action( 'admin_notices', array( &$this, 'reset_notice' ) );
		}
	}

	public function reset_notice() {
		$user = get_user_by( 'id', 1 );
		printf(
			/* translators: The username. */
			'<div id="message" class="updated fade"><p><strong>' . esc_html__( 'WordPress has been reset back to defaults. The user "%s" was recreated with its previous password.', 'aione-app-builder' ) . '</strong></p></div>',
			esc_html( $user->user_login )
		);
		do_action( 'wordpress_reset_post', $user );
	}

}
