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
class Aione_App_Builder_Admin_Templates {
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
		add_action( 'init', array( $this, 'register_template_post_type' ) );
		//add_action( 'add_meta_boxes', array( $this,'template_register_meta_box' ));
		add_action( 'save_post', array( $this,'template_save_meta_box' ));
		add_filter('single_template', array( $this,'custom_single_template'));
		add_filter( 'archive_template', array( $this,'custom_archive_template'));
		
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
	
	// Register Custom Post Type
	function register_template_post_type() {
		
		register_post_type( 'templates',
			array(
					'labels' => array(
							'name' => __( 'Templates' ),
							'singular_name' => __( 'Template' ),
							'menu_name'             => __( 'Templates', 'text_domain' ),
							'name_admin_bar'        => __( 'Templates', 'text_domain' ),
							'archives'              => __( 'Item Archives', 'text_domain' ),
							'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
							'all_items'             => __( 'All Templates', 'text_domain' ),
							'add_new_item'          => __( 'Add New Template', 'text_domain' ),
							'add_new'               => __( 'Add New', 'text_domain' ),
							'new_item'              => __( 'New Template', 'text_domain' ),
							'edit_item'             => __( 'Edit Template', 'text_domain' ),
							'update_item'           => __( 'Update Template', 'text_domain' ),
							'view_item'             => __( 'View Template', 'text_domain' ),
							'search_items'          => __( 'Search Template', 'text_domain' ),
							'not_found'             => __( 'Not found', 'text_domain' ),
							'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
							'featured_image'        => __( 'Featured Image', 'text_domain' ),
							'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
							'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
							'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
							'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
							'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
							'items_list'            => __( 'Templates list', 'text_domain' ),
							'items_list_navigation' => __( 'Templates list navigation', 'text_domain' ),
							'filter_items_list'     => __( 'Filter Templates list', 'text_domain' ),
					),
			'public' => true,
			'has_archive' => true,
			'show_in_menu' => 'aione_app_builder'
			)
		); 
		

	}
	
	
	function template_register_meta_box() {
		add_meta_box( 'custom_meta_box-1', __( 'Template Type', 'textdomain' ), array( $this,'template_register_meta_box_type_callback'), 'templates','side','high' );
		add_meta_box( 'custom_meta_box-2', __( 'Select Post', 'textdomain' ), array( $this,'template_register_meta_box_post_callback'), 'templates','side','high' );
	}
	function template_register_meta_box_type_callback(){
		global $post;
		$template_type = get_post_meta( $post->ID , '_template_type', true );
		?>
		<input type="hidden" name="template_type_noncename" id="template_type_noncename" value="<?php echo wp_create_nonce( 'template_type'.$post->ID );?>" />
		<input type="radio" name="template_type" value="single" <?php if ($template_type == 'single') echo "checked=checked";?>> Single<br/>
		<input type="radio" name="template_type" value="archive" <?php if ($template_type == 'archive') echo "checked=checked";?>> Archive<br/>
   
    <?php 
	}
	
	function template_register_meta_box_post_callback(){
		global $post;
		
		$postmeta =  get_post_meta( $post->ID, '_template_applied_to', true );
		$args = array(
		   'public'   => true,
		   '_builtin' => false
		);

		$output = 'names'; // names or objects, note names is the default
		$operator = 'or'; // 'and' or 'or'

		$post_types = get_post_types( $args, $output, $operator ); 
		?>
		<input type="hidden" name="post_type_noncename" id="post_type_noncename" value="<?php echo wp_create_nonce( 'post_type'.$post->ID );?>" />
		<?php 
		foreach ( $post_types  as $post_type ) {
			if ( is_array( $postmeta ) && in_array( $post_type, $postmeta ) ) {
				$checked = 'checked="checked"';
			} else {
				$checked = null;
			}
			?>
			
			<input  type="checkbox" name="selected_post[]" value="<?php echo $post_type;?>" <?php echo $checked; ?> />
            <?php echo $post_type."<br/>";
		}
	}
	
	
	
	function template_save_meta_box( $post_id ) {
		// verify this came from the our screen and with proper authorization.
		if ( !wp_verify_nonce( $_POST['template_type_noncename'], 'template_type'.$post_id )) {
			return $post_id;
		}
		$is_autosave = wp_is_post_autosave( $post_id );
		$is_revision = wp_is_post_revision( $post_id );
		$is_valid_nonce = ( isset( $_POST[ 'template_type_noncename' ] ) && wp_verify_nonce( $_POST['template_type_noncename'], 'template_type'.$post_id ) && isset( $_POST[ 'post_type_noncename' ] ) && wp_verify_nonce( $_POST['post_type_noncename'], 'selected_post'.$post_id ) ) ? 'true' : 'false';

		if ( $is_autosave || $is_revision || !$is_valid_nonce ) {
			return $post_id;
		}
		 
		// verify if this is an auto save routine. If it is our form has not been submitted, so we dont want to do anything
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) 
			return $post_id;
		 
		// Check permissions
		if ( !current_user_can( 'edit_post', $post_id ) )
			return $post_id;
			 
		 
		// OK, we're authenticated: we need to find and save the data   
		$post = get_post($post_id);
		if ($post->post_type == 'templates') { 
			if ( ! empty( $_POST['template_type'] ) ){
				update_post_meta($post_id, '_template_type', $_POST['template_type'] );
			}
			
			if ( ! empty( $_POST['selected_post'] ) ) {
				update_post_meta( $post_id, '_template_applied_to', $_POST['selected_post'] );

			// Otherwise just delete it if its blank value.
			}		
		}
		return $post_id;
	}
	function custom_single_template($single) {
		global $wp_query, $post;
		/* $args = array(
			'post_type'  => 'templates',
			'meta_key'   => '_template_type',
			'meta_value'       => 'single',
			'posts_per_page'   => -1
		);
		$query = get_posts( $args ); //echo "<pre>";print_r($query);echo "</pre>";
		foreach($query as $key=>$val){
			$post_id = $val->ID;
			$postmeta =  get_post_meta( $post_id, '_template_applied_to', true );
			foreach($postmeta as $postmeta_val){
				if ($post->post_type == $postmeta_val){
					return plugin_dir_path( dirname( __FILE__ ) ). 'library/aione-templates/templates/template-single.php';
				}
			}
			
		} */
		
		$option_name = "aione_app_builder_template_setting";
		if ( get_option( $option_name ) !== false ) {
			$tem_settings = get_option( $option_name ); 
			$tem_settings = unserialize($tem_settings);
			foreach($tem_settings as $key => $value){
				$template_id = $value['template_single'];
				if ($post->post_type == $key){
					return plugin_dir_path( dirname( __FILE__ ) ). 'library/aione-templates/templates/template-single.php';
				}
			}
		} 
		
		return $single;
	}
	
	function custom_archive_template($archive_template) {
		global $wp_query, $post;
		
		$option_name = "aione_app_builder_template_setting";
		if ( get_option( $option_name ) !== false ) {
			$tem_settings = get_option( $option_name ); 
			$tem_settings = unserialize($tem_settings);
			foreach($tem_settings as $key => $value){
				$template_id = $value['template_archive'];
				if ($post->post_type == $key){
					if ( is_post_type_archive ( $post->post_type ) ){
						return plugin_dir_path( dirname( __FILE__ ) ). 'library/aione-templates/templates/archive-template.php';
					}
					//return plugin_dir_path( dirname( __FILE__ ) ). 'library/aione-templates/templates/template-archive.php';
				}
			}
		} 
		
		return $archive_template;
	}
	
	
}