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

		// Add custom columns to listing page of custom post type created with Aione App Builder 
		add_action( 'admin_init',array( $this, 'aione_add_admin_custom_column' ), 10 );

		// TO DO: To add Members Plugin Menu in Aione App Builder
		//add_action( 'admin_init',array( $this, 'aione_register_members_menu' ), 1000 );
		add_filter( 'aione_filter_register_menu_pages', array( $this, 'register_page_dashboard_in_menu' ), 1000 );
		add_filter( 'aione_filter_register_menu_pages',array( $this, 'aione_register_menu_pages' ), 10 );
		//add_action('wp_head', array( $this, 'aione_ajaxurl'));
		add_action( 'init', array($this,'aione_init_components_taxonomies'), apply_filters('aione_init_components_taxonomies', 10));

		if(!class_exists('acf')){ 
            add_action( 'admin_notices', array( $this, 'aione_acf_admin_notice' ));
        }

		

	}

	function aione_init(){
		global $aione;

		aione_show_admin_messages();

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

	public function aione_acf_admin_notice() {
        ?>
        <div class="notice error my-acf-notice is-dismissible" >
            <p><?php _e( 'ACF Plugin is necessary for Aione App Builder to work properly, install it now! <a href="https://wordpress.org/plugins/advanced-custom-fields/" target="_blank">Click Here!</a>', 'aione' ); ?></p>
        </div>
        <?php
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

		

		$pages['aione-reset-all'] = array(
			'slug'				=> 'aione-reset-all',
	        'menu_title'		=> __( 'Reset All', 'aione-app-builder' ),
	        'page_title'		=> __( 'Reset All', 'aione-app-builder' ),
	        'callback'  		=> 'aione_admin_reset_all',
	        'capability'		=> 'manage_options',
	    );

	    $pages['aione-tools'] = array(
			'slug'				=> 'aione-tools',
	        'menu_title'		=> __( 'Import/Export', 'aione-app-builder' ),
	        'page_title'		=> __( 'Import/Export', 'aione-app-builder' ),
	        'callback'  		=> 'aione_tools',
	        'capability'		=> 'manage_options',
	        'load_hook'			=> 'aione_tools_hook'
	    );
		$pages['aione-tools']['load_hook'] = aione_admin_calculate_menu_page_load_hook( $pages['aione-tools'] );
		//add_action('load-' . $page, array($this, 'load'));
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

	function aione_tools(){
		/*aione_add_admin_header(
	        __( 'Import/Export', 'aione-app-builder' )
	    );
	    aione_admin_import_export_components_boxes();
	    aione_add_admin_footer();*/

	    $post_type = current_filter();
        aione_add_admin_header(
	        __( 'Import/Export', 'aione-app-builder' )
	    );
        $form = aione_form( 'aione_form_tools' );
        echo '<form method="post" action="" class="aione-fields-form aione-form-validate js-types-show-modal" enctype="multipart/form-data">';
        aione_admin_screen($post_type, $form->renderForm());
        echo '</form>';
        aione_add_admin_footer();
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

	function aione_tools_hook(){
		//require_once dirname( __FILE__ ).'/class-component-edit.php';
	    $aione_admin = new Aione_Admin_Tools();
	    $aione_admin->init_admin();
	    $form = $aione_admin->form();
	    aione_form( 'aione_form_tools', $form );
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
		global $wpdb;
		if ( isset( $_POST['aione_reset_confirm'] ) && 'reset' !== $_POST['aione_reset_confirm'] ) {
			echo '<div class="error fade"><p><strong>' . esc_html__( 'Invalid confirmation word. Please type the word "reset" in the confirmation field.', 'aione-app-builder' ) . '</strong></p></div>';
		} elseif ( isset( $_POST['_wpnonce'] ) ) {
			echo '<div class="error fade"><p><strong>' . esc_html__( 'Invalid nonce. Please try again.', 'aione-app-builder' ) . '</strong></p></div>';
		}
		/*$blogname    = get_option( 'blogname' );
		$admin_email = get_option( 'admin_email' );
		$blog_public = get_option( 'blog_public' );
		$siteurl = get_option( 'siteurl' );
		echo "<pre>";
		print_r(admin_url());
		echo "</pre>";*/
		
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

			global $wpdb, $reactivate_wp_reset_additional;

			$get_user_meta    = function_exists( 'get_user_meta' ) ? 'get_user_meta' : 'get_usermeta';
			$update_user_meta = function_exists( 'update_user_meta' ) ? 'update_user_meta' : 'update_usermeta';

			$blogname    = get_option( 'blogname' );
			$admin_email = get_option( 'admin_email' );
			$blog_public = get_option( 'blog_public' );
			$siteurl = get_option( 'siteurl' );
			$users_list = get_users();
			$usermetadata = array();

			foreach ( $users_list as $user ) {
				$user_level = $get_user_meta( $user->ID, $wpdb->prefix . 'user_level',true );
				$capabilities = $get_user_meta( $user->ID, $wpdb->prefix . 'capabilities' ,true);
				$usermetadata[$user->ID] = array(
					"ID" => $user->ID,
					"user_email" => $user->user_email,
					$wpdb->prefix . 'user_level'=>$user_level,
					$wpdb->prefix . 'capabilities'=>$capabilities
				) ;
			}

			/*echo "<div class='width:70%;float:right;'><pre>";
			print_r($usermetadata);
			echo "</pre></div>";
			exit;*/

			if ( 'admin' !== $current_user->user_login ) {
				$user = get_user_by( 'login', 'admin' );
				$user_id = $user->ID;
			}

			if ( empty( $user->user_level ) || $user->user_level < 10 ) {
				$user = $current_user;
				$user_id = $user->ID;
			}

			

			$prefix = str_replace( '_', '\_', $wpdb->prefix );
			$tables = $wpdb->get_col( "SHOW TABLES LIKE '{$prefix}%'" );
			$ignore = array($wpdb->prefix.'users',$wpdb->prefix.'usermeta');
			$tables = array_diff($tables, $ignore);
			foreach ( $tables as $table ) {
				$wpdb->query( "DROP TABLE $table" );
			}

			$result = wp_install( $blogname, $user->user_login, $user->user_email, $blog_public );
			extract( $result, EXTR_SKIP );

			$query = $wpdb->prepare( "UPDATE $wpdb->users SET user_pass = %s, user_activation_key = '' WHERE ID = %d", $user->user_pass, $user_id );
			$wpdb->query( $query );

			if ( $get_user_meta( $user_id, 'default_password_nag' ) ) {
				$update_user_meta( $user_id, 'default_password_nag', false );
			}

			if ( $get_user_meta( $user_id, $wpdb->prefix . 'default_password_nag' ) ) {
				$update_user_meta( $user_id, $wpdb->prefix . 'default_password_nag', false );
			}

			if ( is_multisite() ) {
				foreach ( $users_list as $user ) {
					$update_user_meta( $user->ID, $wpdb->prefix.'user_level', $usermetadata[$user->ID][$wpdb->prefix .'user_level'] );
					$update_user_meta( $user->ID, $wpdb->prefix.'capabilities', $usermetadata[$user->ID][$wpdb->prefix .'capabilities'] );
				}
			}

			wp_clear_auth_cookie();
			wp_set_auth_cookie( $user_id );

			wp_redirect( admin_url() );
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

	public function aione_add_admin_custom_column(){
		$post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $custom_types = $post_type_option->get_components();
        //echo "<pre>";print_r($custom_types);echo "</pre>";
		if ( !empty( $custom_types ) ){
			foreach( $custom_types as $post_type => $data ){
				if(isset( $data['_builtin'] ) && $data['_builtin']){
					unset($custom_types[$post_type]);
				}
				if(!empty($data['admin_custom_columns'])){	
					add_filter ( 'manage_posts_columns', array( $this, 'add_acf_columns' ),10,2 );
					add_action('manage_posts_custom_column', array( $this, 'add_acf_columns_content' ), 10, 2);
					add_filter('manage_edit-'.$post_type.'_sortable_columns', array( $this, 'acf_custom_column_sortable' ), 10);   

				}
			}
		}
		
	}
	public function add_acf_columns($column,$post_type){
		$aione_components = get_option(AIONE_OPTION_NAME_COMPONENTS);
		if ( !empty( $aione_components ) ){
			$custom_columns = $aione_components[$post_type]['admin_custom_columns'];
			$extra_columns = array();
			if(!empty($custom_columns)){
				foreach ($custom_columns as $key => $custom_column) {
					$field = get_field_object($key);
					$extra_columns[$field['name']]=$field['label'];
				}
			}			
		}		
		return array_merge($column,$extra_columns);    	
	}
	public function add_acf_columns_content($column,$post_id){
		$data = get_post_meta ( $post_id, $column, true );	
		if(is_array($data)){
			echo implode(',', $data);
		} else{
			echo $data;	  	
		}
			
	}
	public function acf_custom_column_sortable($columns){
		$aione_components = get_option(AIONE_OPTION_NAME_COMPONENTS);
		$extra_columns = array();
		if ( !empty( $aione_components ) ){
			foreach ($aione_components as $post_type => $aione_component) {
				$custom_columns = $aione_component['admin_custom_columns'];	
				if(!empty($custom_columns)){
					foreach ($custom_columns as $key => $custom_column) {
						$field = get_field_object($key);
						$extra_columns[$field['name']]=$field['label'];
					}
				}
			}			
		}
		
		return array_merge($columns,$extra_columns);  
	}

}
