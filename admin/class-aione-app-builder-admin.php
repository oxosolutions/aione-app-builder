<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.sgssandhu.com
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
 * @author     SGS Sandhu <contact@oxosolutions.com>
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

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_menu', array( $this, 'aione_app_builder_admin_menu_hook' ) );
		
		$this->plugin_admin_shortcodes = new Aione_App_Builder_Admin_Shortcodes( $this->plugin_name, $this->version );
		$this->plugin_admin_templates = new Aione_App_Builder_Admin_Templates( $this->plugin_name, $this->version );
		$this->plugin_admin_aione_cpt = new Aione_App_Builder_Admin_Aione_Custom_Post_Type( $this->plugin_name, $this->version );
		$this->plugin_admin_aione_taxonomy = new Aione_App_Builder_Admin_Aione_Custom_Taxonomy( $this->plugin_name, $this->version );
		$this->plugin_admin_backup_restore = new Aione_App_Builder_Admin_Backup_Restore( $this->plugin_name, $this->version );
		
		
		if(!class_exists('Types_Main')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/types/wpcf.php';
			add_action( 'admin_menu', array($this,'remove_types_main_menus'), 9999 );
		}
		if(!class_exists('acf')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/advanced-custom-fields/acf.php';
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/acf-repeater/acf-repeater.php' ;
			add_action( 'admin_menu', array($this,'remove_acf_main_menus'), 9999 );
		}
		if(!class_exists('Members_Plugin')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/members/members.php';
		}
		if(!class_exists('Page_Template_Plugin')){
			require_once plugin_dir_path( dirname( __FILE__ ) ) . 'library/aione-templates/aione-templates.php';
		}
		
		
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
/**
	 * admin_menu hook.
	 */
	
	function aione_app_builder_admin_menu_hook(){  
		 add_menu_page( 
			__('App Builder', 'aione_app_builder'),
			__('App Builder', 'aione_app_builder'),
			__('manage_options', 'aione_app_builder'),
			'aione_app_builder', 
			false,
			'dashicons-image-filter',
			29 
		); 
		
		$page = 'add_submenu_page';
		 // Set Pages
            $page(
				__('aione_app_builder', 'aione_app_builder'),
				__('Set Pages', 'aione_app_builder'),
				__('Set Pages', 'aione_app_builder'),
				__('manage_options', 'aione_app_builder'),
				__('aione_app_builder', 'aione_app_builder'),
				array($this,'aione_app_builder_set_pages')
			);
		 // Shortcodes
            $page(
				__('aione_app_builder', 'aione_app_builder'),
				__('Shortcodes', 'aione_app_builder'),
				__('Shortcodes', 'aione_app_builder'),
				__('manage_options', 'aione_app_builder'),
				__('aione_app_builder_shortcodes', 'aione_app_builder'),
				array($this,'aione_app_builder_shortcodes')
			);
		 // Custom Post Type
            $page(
				__('aione_app_builder', 'aione_app_builder'),
				__('Custom Post Type', 'aione_app_builder'),
				__('Custom Post Type', 'aione_app_builder'),
				__('manage_options', 'aione_app_builder'),
				__('admin.php?page=wpcf-cpt', 'aione_app_builder'),
				false
				
			);
		// Custom Taxonomies
            $page(
				__('aione_app_builder', 'aione_app_builder'),
				__('Custom Taxonomies', 'aione_app_builder'),
				__('Custom Taxonomies', 'aione_app_builder'),
				__('manage_options', 'aione_app_builder'),
				__('admin.php?page=wpcf-ctt', 'aione_app_builder'),
				false
				
			);	
		 // Custom Fields
            $page(
				__('aione_app_builder', 'aione_app_builder'),
				__('Custom Fields', 'aione_app_builder'),
				__('Custom Fields', 'aione_app_builder'),
				__('manage_options', 'aione_app_builder'),
				__('edit.php?post_type=acf', 'aione_app_builder'),
				false
			);	
			
		 //  Template
            $page(
				__('aione_app_builder', 'aione_app_builder'),
				__('Set View', 'aione_app_builder'),
				__('Set View', 'aione_app_builder'),
				__('manage_options', 'aione_app_builder'),
				__('aione_app_builder_set_view', 'aione_app_builder'),
				array($this,'aione_app_builder_set_view')
			); 
			
		
	      
	    //remove_submenu_page('aione_app_builder', 'aione_app_builder' );
		
	}
	
	/**
	 * aione_app_builder_set_pages
	 */
	 
	 function aione_app_builder_set_pages() {
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
			require_once plugin_dir_path( __FILE__ ) .'views/register-tab.php';
		} 
		else if(isset ( $_GET['tab'] ) && $_GET['tab'] == 'forgot_Password'){
			require_once plugin_dir_path( __FILE__ ) .'views/lost-password-tab.php';
		}
		else if(isset ( $_GET['tab'] ) && $_GET['tab'] == 'logout'){
			require_once plugin_dir_path( __FILE__ ) .'views/logout-tab.php';
		}
		else {
			require_once plugin_dir_path( __FILE__ ) .'views/login-tab.php';
		}
		
	 }
	 
	 public function tabs(){
		 if(isset ( $_GET['page'] ) && $_GET['page'] == 'aione_app_builder'){
			 $selected_tab = 'login';
		 }
         ?>
		
			<h2 class="nav-tab-wrapper">
                <a class="nav-tab <?php echo $selected_tab == 'login' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'login' ), 'admin.php?page=aione_app_builder' ) ) ); ?>">
                    <?php esc_attr_e( "Login", 'aione_app_builder' ); ?> </a> 
				
				<a class="nav-tab <?php echo $selected == 'register' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'register' ), 'admin.php?page=aione_app_builder' ) ) ); ?>">
                    <?php esc_attr_e( 'Register', 'aione_app_builder' ); ?> </a>
				
				<a class="nav-tab <?php echo $selected == 'forgot_Password' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'forgot_Password' ), 'admin.php?page=aione_app_builder' ) ) ); ?>">
                    <?php esc_attr_e( 'Forgot Password', 'aione_app_builder' ); ?> </a> 
					
				<a class="nav-tab <?php echo $selected == 'logout' ? 'nav-tab-active' : ''; ?>"
                    href="<?php echo esc_url( admin_url( add_query_arg( array( 'tab' => 'logout' ), 'admin.php?page=aione_app_builder' ) ) ); ?>">
                    <?php _e( 'Log Out', 'aione_app_builder' ); ?> </a>  
					
            </h2>
			
        <?php
		
        }
		
	/**
	 * aione_app_builder_shortcodes
	 */
	 function aione_app_builder_shortcodes(){ 
		 $this->plugin_admin_shortcodes->aione_app_builder_shortcodes_list();
	 }
	 
	 // remove Types Main Menu
	function remove_types_main_menus (){
		global $menu;
		global $submenu;
		remove_menu_page( 'toolset-dashboard' ); 
	}
	// remove ACF Main Menu
	function remove_acf_main_menus (){
		global $menu;
		global $submenu;
		remove_menu_page( 'edit.php?post_type=acf' );
	}
	
	function aione_app_builder_set_view(){
		$option_name = "aione_app_builder_template_setting";
		//delete_option( $option_name );
		if(isset($_POST['set-view-submit'])){
			unset($_POST['set-view-submit']);
			$settings = $_POST;
			$settings_serailized = serialize($settings);
			//echo "<pre>";print_r($settings);echo "</pre>";
			
			if ( get_option( $option_name ) !== false ) {
				update_option( $option_name, $settings_serailized );
			} else {
				add_option( $option_name, $settings_serailized );
			}
		}
		
		$output = "<h1>SET VIEW</h1>";
		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

		$name = 'names'; // names or objects, note names is the default
		$operator = 'or'; // 'and' or 'or'

		$post_types = get_post_types( $args, $name, $operator );
		//echo "<pre>";print_r($post_types);echo "</pre>";
		
		
		$cpts_raw = ! isset( $_GET['toolset-dashboard-simulate-no-custom-post-types'] )
					? get_option( WPCF_OPTION_NAME_CUSTOM_TYPES, array() )
					: array();
		
		// remove buildin types
		$cpts_raw = array_diff_key( $cpts_raw, $this->get_types_by_wordpress() );
		
		$cpts = array();

		foreach( $cpts_raw as $cpt_raw ) {
			$post_type = new Types_Post_Type( $cpt_raw['slug'] );
			// only use active post types
			if( isset( $post_type->name ) )
				$cpts[$cpt_raw['slug']] = $post_type;
		}
		
		$args = array(
			'post_type'  => 'templates',
			'post_status'  => 'publish',
			'posts_per_page'   => -1
		);
		$query = get_posts( $args ); //echo "<pre>";print_r($query);echo "</pre>";
		
		$output .= "<form method='post' action=''>";
		$output .= "<table style='border:1px solid #e8e8e8;border-collapse:collapse;'>";
		$output .= "<tr style='border:1px solid #e8e8e8;'>";
		$output .= "<th>Post Type</th>";
		$output .= "<th>Single</th>";
		$output .= "<th>Archive</th>";
		$output .= "</tr>";
		foreach ( $cpts as $cpt_key => $cpt_value ) {
			$output .= "<tr style='border:1px solid #e8e8e8;'>";
			$output .="<td style='border:1px solid #e8e8e8;padding:1%;'>".$cpt_key."</td>";
			$output .="<td style='border:1px solid #e8e8e8;padding:1%;'>";
			$output .="<select name='".$cpt_key."[template_single]'>";
			$output .="<option value=''>Select Single View Template</option>";
			
			foreach($query as $templates){
				if ( get_option( $option_name ) !== false ) {
					$tem_settings = get_option( $option_name ); 
					$tem_settings = unserialize($tem_settings); 
					if(array_key_exists($cpt_key,$tem_settings ) ){
						if($tem_settings[$cpt_key]['template_single'] ==  $templates->ID){
							$single_selected = "selected";
						} else {$single_selected = "";}
						
					}
				}
				$output .="<option value='".$templates->ID."' ".$single_selected.">".$templates->post_title."</option>";
			}
			$output .="</select>";
			$output .="</td>";
			$output .="<td style='border:1px solid #e8e8e8;padding:1%;'>";
			$output .="<select name='".$cpt_key."[template_archive]'>";
			$output .="<option value=''>Select Archive View Template</option>";
			foreach($query as $templates){
				if ( get_option( $option_name ) !== false ) {
					$tem_settings = get_option( $option_name );
					$tem_settings = unserialize($tem_settings); 
					if(array_key_exists($cpt_key,$tem_settings ) ){
						if($tem_settings[$cpt_key]['template_archive'] ==  $templates->ID){
							$archive_selected = "selected";
						} else {$archive_selected = "";}
						
					}
				}
				$output .="<option value='".$templates->ID."' ".$archive_selected.">".$templates->post_title."</option>";
			}
			$output .="</select>";
			$output .="</td>";
			$output .= "</tr>";
		}
		$output .= "</table>";
		$output .='<input type="submit" name="set-view-submit" value="Save View" class="button-primary form-submit submit">';
		$output .= "</form>";
		echo $output;
	}
	
	function get_types_by_wordpress() {
		if( $this->types_by_wordpress !== null )
			return $this->types_by_wordpress;

		$cpts_raw = array(
			'post' => array(
				'slug'      => 'post',
				'_buildin'  => 1
			),
			'page' => array(
				'slug'      => 'page',
				'_buildin'  => 1
			),
			'attachment' => array(
				'slug'      => 'attachment',
				'_buildin'  => 1
			),
		);

		$cpts = array();
		foreach( $cpts_raw as $cpt_raw ) {
			$post_type = new Types_Post_Type( $cpt_raw['slug'] );
			// only use active post types
			if( isset( $post_type->name ) )
				$cpts[$cpt_raw['slug']] = $post_type;
		}

		$this->types_by_wordpress = $cpts;

		return $this->types_by_wordpress;
	}
	
	
	
}
