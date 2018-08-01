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
		// TO DO: To add Members Plugin Menu in Aione App Builder
		//add_action( 'admin_init',array( $this, 'aione_register_members_menu' ), 1000 );
		add_filter( 'aione_filter_register_menu_pages', array( $this, 'register_page_dashboard_in_menu' ), 1000 );
		add_filter( 'aione_filter_register_menu_pages',array( $this, 'aione_register_menu_pages' ), 10 );
		//add_action('wp_head', array( $this, 'aione_ajaxurl'));
		add_action( 'init', array($this,'aione_init_components_taxonomies'), apply_filters('aione_init_components_taxonomies', 10));

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

		if(is_admin() && $pagenow == 'admin.php' && $_GET['page'] == 'aione-dashboard'){
			wp_register_style( 'aione-framework-css',  plugin_dir_url( __FILE__ ) .'/css/aione.min.css', array(), $this->version, 'all' );
			wp_enqueue_style('aione-framework-css');
		}
   

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
				add_submenu_page( 'aione-dashboard', __( 'Field Groups', 'aione-app-builder' ), __( 'Custom Fields', 'aione-app-builder' ), 'manage_options', 'edit.php?post_type=acf-field-group' );
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
					$aione_app_builder_login_page = $_POST['aione_app_builder_login_page'];
					$admin_login_redirect_page = $_POST['admin_login_redirect_page'];

					update_option( 'aione_app_builder_login_page', $aione_app_builder_login_page );
					update_option( 'admin_login_redirect_page', $admin_login_redirect_page );
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

}
