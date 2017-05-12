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
class Aione_App_Builder_Admin_Aione_Custom_Post_Type {
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
		add_action( 'init', array( $this, 'register_custom_post_type' ) );
		add_action('admin_init', array( $this, 'register_custom_post_type_metabox'));
		add_action('save_post', array($this, 'cpt_save_post_metabox'));
		//add_action( 'save_post', array( $this,'template_save_meta_box' ));
		
		
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
	function register_custom_post_type() {
		
		register_post_type( 'aione-cpt',
			array(
					'labels' => array(
							'name' => __( 'Aione Custom Post Type' ),
							'singular_name' => __( 'Aione Custom Post Type' ),
							'menu_name'             => __( 'Aione Custom Post Type', 'text_domain' ),
							'name_admin_bar'        => __( 'Aione Custom Post Type', 'text_domain' ),
							'archives'              => __( 'Item Archives', 'text_domain' ),
							'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
							'all_items'             => __( 'All Custom Post Types', 'text_domain' ),
							'add_new_item'          => __( 'Add New Custom Post Type', 'text_domain' ),
							'add_new'               => __( 'Add New', 'text_domain' ),
							'new_item'              => __( 'New Custom Post Type', 'text_domain' ),
							'edit_item'             => __( 'Edit Custom Post Type', 'text_domain' ),
							'update_item'           => __( 'Update Custom Post Type', 'text_domain' ),
							'view_item'             => __( 'View Custom Post Type', 'text_domain' ),
							'search_items'          => __( 'Search Custom Post Type', 'text_domain' ),
							'not_found'             => __( 'Not found', 'text_domain' ),
							'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
							'featured_image'        => __( 'Featured Image', 'text_domain' ),
							'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
							'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
							'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
							'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
							'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
							'items_list'            => __( 'Custom Post Types list', 'text_domain' ),
							'items_list_navigation' => __( 'Custom Post Types list navigation', 'text_domain' ),
							'filter_items_list'     => __( 'Filter Custom Post Types list', 'text_domain' ),
					),
			'public' => true,
			'has_archive' => true,
			'supports' => false,
			'show_in_menu' => 'aione_app_builder'
			)
		); 
		

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
	
	function register_custom_post_type_metabox() {
        add_meta_box('cpt_metabox_main', __('Name and description') , array($this,'cpt_metabox_main'), 'aione-cpt', 'normal', 'high');
    }
	function cpt_metabox_main(){
		wp_nonce_field( plugin_basename( __FILE__ ), 'cpt_noncename' );
		?>
		<table id="" class="">
			<tbody>
				<tr>
					<td><label class="" for="name-plural">Name plural (<strong>required</strong>)</label></td>
					<td><input type="text" id="name-plural" name="cpt_name_plural" value=""  placeholder="Enter Post Type name plural" class="large-text  form-textfield textfield"></td>
				</tr>
				<tr>
					<td><label class="" for="name-singular">Name singular (<strong>required</strong>)</label></td>
					<td><input type="text" id="name-singular" name="cpt_name_singular" value="" placeholder="Enter Post Type name singular" class="large-text form-textfield textfield" ></td>
				</tr>
				<tr>
					<td><label class="" for="slug">Slug (<strong>required</strong>)</label></td>
					<td><input type="text" id="slug" name="cpt_slug" value="" maxlength="20" placeholder="Enter Post Type slug" class="large-text form-textfield textfield"></td>
				</tr>
				<tr>
					<td><label class="" for="description">Description</label></td>
					<td><textarea id="description" name="cpt_description" rows="4" cols="60" placeholder="Enter Post Type description" class="form-textarea textarea"></textarea>
					</td>
				</tr>
				<tr>
					<td><label class="" for="icon">Icon</label></td>
					<td><div class="dashicons-before dashicons-admin-post"><br></div>
					<button type="button" id="icon" name="cpt_icon"  class="form-button button">Change icon</button>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}
	
	function cpt_save_post_metabox(){
		if ( !wp_verify_nonce( $_POST['cpt_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		}
		if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ){
			return $post_id;
		} 
		if ( !current_user_can( 'edit_page', $post_id ) || !current_user_can( 'edit_post', $post_id )){
			return $post_id;
		}
		$cpt_name_plural = $_POST['cpt_name_plural'];
		$cpt_name_singular = $_POST['cpt_name_singular'];
		$cpt_slug = $_POST['cpt_slug'];
		$cpt_description = $_POST['cpt_description'];
		$cpt_icon = $_POST['cpt_icon'];
		//update_post_meta($post_id, '_backlink_url', $blc); 
		
		/* register_post_type( $cpt_slug,
			array(
				'labels' => array(
						'name' => __( 'Aione Custom Post Type' ),
						'singular_name' => __( 'Aione Custom Post Type' ),
						'menu_name'             => __( 'Aione Custom Post Type', 'text_domain' ),
						'name_admin_bar'        => __( 'Aione Custom Post Type', 'text_domain' ),
						'archives'              => __( 'Item Archives', 'text_domain' ),
						'parent_item_colon'     => __( 'Parent Item:', 'text_domain' ),
						'all_items'             => __( 'All Custom Post Types', 'text_domain' ),
						'add_new_item'          => __( 'Add New Custom Post Type', 'text_domain' ),
						'add_new'               => __( 'Add New', 'text_domain' ),
						'new_item'              => __( 'New Custom Post Type', 'text_domain' ),
						'edit_item'             => __( 'Edit Custom Post Type', 'text_domain' ),
						'update_item'           => __( 'Update Custom Post Type', 'text_domain' ),
						'view_item'             => __( 'View Custom Post Type', 'text_domain' ),
						'search_items'          => __( 'Search Custom Post Type', 'text_domain' ),
						'not_found'             => __( 'Not found', 'text_domain' ),
						'not_found_in_trash'    => __( 'Not found in Trash', 'text_domain' ),
						'featured_image'        => __( 'Featured Image', 'text_domain' ),
						'set_featured_image'    => __( 'Set featured image', 'text_domain' ),
						'remove_featured_image' => __( 'Remove featured image', 'text_domain' ),
						'use_featured_image'    => __( 'Use as featured image', 'text_domain' ),
						'insert_into_item'      => __( 'Insert into item', 'text_domain' ),
						'uploaded_to_this_item' => __( 'Uploaded to this item', 'text_domain' ),
						'items_list'            => __( 'Custom Post Types list', 'text_domain' ),
						'items_list_navigation' => __( 'Custom Post Types list navigation', 'text_domain' ),
						'filter_items_list'     => __( 'Filter Custom Post Types list', 'text_domain' ),
				),
			'public' => true,
			'has_archive' => true,
			'supports' => false,
			'show_in_menu' => 'aione_app_builder'
			)
		); */ 
        
	}
}