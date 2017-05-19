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
		add_action( 'init', array( $this, 'create_custom_post_type' ) );
		add_action( 'init', array( $this, 'get_taxonomies_list' ) );
		add_action('admin_init', array( $this, 'register_custom_post_name_metabox'));
		add_action('admin_init', array( $this, 'register_custom_post_label_metabox'));
		add_action('admin_init', array( $this, 'register_custom_post_taxonomy_metabox'));
		add_action('save_post', array($this, 'cpt_save_post_metabox'));
		
		
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

	function get_taxonomies_list(){
		$allowed_taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => true ), 'names' );
		$excluded_taxonomies = array( 'nav_menu', 'link_category', 'post_format' );
		$allowed_taxonomies = array_diff( $allowed_taxonomies, $excluded_taxonomies );
		$taxonomies = get_taxonomies( '', 'objects' );
		foreach( $taxonomies as $taxonomy_slug => $taxonomy ) {
			if( ! in_array( $taxonomy_slug, $allowed_taxonomies ) ) {
				unset( $taxonomies[ $taxonomy_slug ] );
			}
		}
		return $taxonomies;
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
				'show_in_menu' => 'aione_app_builder'
			)
		); 
		

	}
	
	
	function register_custom_post_name_metabox() {
        add_meta_box('cpt_metabox_main', __('Name and description') , array($this,'cpt_metabox_main'), 'aione-cpt', 'normal', 'high');
    }

    function register_custom_post_label_metabox(){
    	add_meta_box('cpt_metabox_label', __('Labels') , array($this,'cpt_metabox_label'), 'aione-cpt', 'normal', 'high');
    }

    function register_custom_post_taxonomy_metabox(){
    	add_meta_box('cpt_metabox_taxonomy', __('Taxonomies to be used') , array($this,'cpt_metabox_taxonomy'), 'aione-cpt', 'normal', 'high');
    }

	function cpt_metabox_main(){
		wp_nonce_field( plugin_basename( __FILE__ ), 'cpt_noncename' );
		$cpt_meta = get_post_meta( get_the_ID(), '_aione_cpt_meta', true );
		?>
		<table id="aione-cpt-form-name-table" class="aione-cpt-form-table widefat js-wpcf-slugize-container">
			<tbody>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="name-plural">Name plural (<strong>required</strong>)</label></td>
					<td><input type="text" id="name-plural" name="cpt_name_plural" value="<?php echo $cpt_meta["plural"]; ?>"  placeholder="Enter Post Type name plural" class="large-text aione-cpt-form-textfield form-textfield textfield js-types-validate"></td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="name-singular">Name singular (<strong>required</strong>)</label></td>
					<td><input type="text" id="name-singular" name="cpt_name_singular" value="<?php echo $cpt_meta["singular"]; ?>" placeholder="Enter Post Type name singular" class="large-text aione-cpt-form-textfield form-textfield textfield js-types-validate" ></td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="slug">Slug (<strong>required</strong>)</label></td>
					<td><input type="text" id="slug" name="cpt_slug" value="<?php echo $cpt_meta["slug"]; ?>" maxlength="20" placeholder="Enter Post Type slug" class="large-text aione-cpt-form-textfield form-textfield textfield js-types-validate"></td>
				</tr>
				<tr>
				<?php
					$menu_positions = array(
			            'menu-dashboard'    => 2,
			            'menu-posts'        => 5,
			            'menu-media'        => 10,
			            'menu-pages'        => 20,
			            'menu-comments'     => 25,
			            'menu-appearance'   => 60,
			            'menu-plugins'      => 65,
			            'menu-users'        => 70,
			            'menu-tools'        => 75,
			            'menu-settings'     => 80
			        );

			        $menu_position = 2;
			        $options = array(
			            __('--- not set ---') => ''
			        );

			        foreach( $GLOBALS['menu'] as $menu_item ) {
			            // skip menu separators
			            if( empty( $menu_item[0] ) || $menu_item[4] == 'wp-menu-separator' )
			                continue;

			            // update menu position
			            if( array_key_exists( $menu_item[5], $menu_positions ) )
			                $menu_position = $menu_positions[$menu_item[5]];

			            $option_name = strip_tags( preg_replace( '#<([a-z]+).*?>.*?</\\1>#uis', '', $menu_item[0] ) );

			            // don't show current cpt in list "Admin Menu position after:"
			            if( 'edit.php?post_type=' . $cpt_slug == $menu_item[2] )
			                continue;

			            // add menu item to options
			            $options[$option_name] = $menu_position . '--aione-cpt-add-menu-after--' . $menu_item[2];
			        }
				       
				?>
					<td><label class="aione-cpt-form-label aione-cpt-form-select-label" for="aione-cpt-menu-position">Admin Menu position after: </label></td>
					<td><select id="aione-cpt-menu-position" name="cpt_menu_position" class=" widefat aione-cpt-form-select form-select select">
						<?php
							foreach ($options as $key => $value) {
					        	echo '<option value="'.$value.'" class="aione-cpt-form-option form-option option">'.$key.'</option>';
					        }
						?>

					</select></td>

				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="description">Description</label></td>
					<td><textarea id="description" name="cpt_description" rows="4" cols="60" placeholder="Enter Post Type description" class="aione-cpt-form-textarea form-textarea textarea"><?php echo $cpt_meta["description"]; ?></textarea>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="icon">Icon</label></td>
					<td><div class="aione-cpt-menu-image dashicons-before dashicons-admin-post"><br></div>
					<button type="button" id="cpt_icon" name="cpt_icon"  class="js-wpcf-choose-icon aione-cpt-form-button form-button button">Change icon</button>
					</td>
				</tr>
			</tbody>
		</table>
		<?php
	}

	function cpt_metabox_label(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_cpt_labels' );
		?>
	 
		<div class="inside">
			<table class="aione-cpt-form-table widefat striped fixed">
			<tbody>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_add_new">Add New</label>
					</td>
					<td><input type="text" id="aione_cpt_add_new" name="aione_cpt_add_new" value="Add New" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description wpcf-form-description aione-cpt-form-description-textfield description-textfield">The add new text. The default is Add New for both hierarchical and non-hierarchical types.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_add_new_item">Add New Item</label>
					</td>
					<td>
						<input type="text" id="aione_cpt_add_new_item" name="aione_cpt_add_new_item" value="Add New %s" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The add new item text. Default is Add New Post/Add New Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_edit_item">Edit Item</label>
					</td>
					<td><input type="text" id="aione_cpt_edit_item" name="aione_cpt_edit_item" value="Edit %s" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The edit item text. Default is Edit Post/Edit Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_new_item">New Item</label>
					</td>
					<td><input type="text" id="aione_cpt_new_item" name="aione_cpt_new_item" value="New %s" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The new item text. Default is New Post/New Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_view_item">View Item</label>
					</td>
					<td><input type="text" id="aione_cpt_view_item" name="aione_cpt_view_item" value="View %s" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The view item text. Default is View Post/View Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_search_items">Search Items</label>
					</td>
					<td><input type="text" id="aione_cpt_search_items" name="aione_cpt_search_items" value="Search %s" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The search items text. Default is Search Posts/Search Pages.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_not_found">Not Found</label>
					</td>
					<td><input type="text" id="aione_cpt_not_found" name="aione_cpt_not_found" value="No %s found" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The not found text. Default is No posts found/No pages found.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_not_found_in_trash">Not Found In Trash</label>
					</td>
					<td><input type="text" id="aione_cpt_not_found_in_trash" name="aione_cpt_not_found_in_trash" value="No %s found in Trash" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The not found in trash text. Default is No posts found in Trash/No pages found in Trash.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_parent_item_colon">Parent Description</label>
					</td>
					<td><input type="text" id="aione_cpt_parent_item_colon" name="aione_cpt_parent_item_colon" value="Parent text" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The parent text. This string isn't used on non-hierarchical types. In hierarchical ones the default is Parent Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_all_items">All Items</label>
					</td>
					<td><input type="text" id="aione_cpt_all_items" name="aione_cpt_all_items" value="All items" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The all items text used in the menu. Default is the Name label.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_enter_title_here">Enter title here</label>
					</td>
					<td><input type="text" id="aione_cpt_enter_title_here" name="aione_cpt_enter_title_here" value="Enter title here" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The text used as placeholder of post title. Default is the "Enter title here".</p>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
      
      <?php
	}

	function cpt_metabox_taxonomy(){
		$taxonomies = $this->get_taxonomies_list();
		echo '<div class="inside">';
		echo '<ul class="wpcf-list">';
		echo '</ul>';
		echo '</div>';
		echo "<pre>";print_r($taxonomies);echo "</pre>";
	}
	
	function cpt_save_post_metabox($post_id){
		if ( !wp_verify_nonce( $_POST['cpt_noncename'], plugin_basename(__FILE__) )) {
			return $post_id;
		}
		if ( !wp_verify_nonce( $_POST['_wpnonce_aione_cpt_labels'], plugin_basename(__FILE__) )) {
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
		$cpt_menu_position = $_POST['cpt_menu_position'];
		$cpt_description = $_POST['cpt_description'];
		$cpt_icon = $_POST['cpt_icon'];
		$cpt_add_new = $_POST['aione_cpt_add_new'];
		$cpt_add_new_item = $_POST['aione_cpt_add_new_item'];
		$cpt_edit_item = $_POST['aione_cpt_edit_item'];
		$cpt_new_item = $_POST['aione_cpt_new_item'];
		$cpt_view_item = $_POST['aione_cpt_view_item'];
		$cpt_search_items = $_POST['aione_cpt_search_items'];
		$cpt_not_found = $_POST['aione_cpt_not_found'];
		$cpt_not_found_in_trash = $_POST['aione_cpt_not_found_in_trash'];
		$cpt_parent_item_colon = $_POST['aione_cpt_parent_item_colon'];
		$cpt_all_items = $_POST['aione_cpt_all_items'];
		$cpt_enter_title_here = $_POST['aione_cpt_enter_title_here'];

		$cpt_meta = array();

		$cpt_meta['icon'] = $cpt_icon;
		$cpt_meta['labels'] = array(
				'name'=>$cpt_name_plural,
				'singular_name'=>$cpt_name_singular,
				'add_new'=>$cpt_add_new,
				'add_new_item'=>$cpt_add_new_item,
				'edit_item'=>$cpt_edit_item,
				'new_item'=>$cpt_new_item,
				'view_item'=>$cpt_view_item,
				'search_items'=>$cpt_search_items,
				'not_found'=>$cpt_not_found,
				'not_found_in_trash'=>$cpt_not_found_in_trash,
				'parent_item_colon'=>$cpt_parent_item_colon,
				'all_items'=>$cpt_all_items,
				'enter_title_here'=>$cpt_enter_title_here,
			);

		$cpt_meta['slug'] = $cpt_slug;
		$cpt_meta['description'] = $cpt_description;
		$cpt_meta['status'] = get_post_status($post_id);
		$cpt_meta['menu_position'] = $cpt_menu_position;
		
		update_post_meta($post_id, '_aione_cpt_meta', $cpt_meta); 
		
		
		
	}
	function create_custom_post_type(){
		$posts_array = get_posts( array('post_type' => 'aione-cpt') );
		foreach($posts_array as $key => $post){
			$post_id = $post->ID;
			$cpt_meta = get_post_meta( $post_id, '_aione_cpt_meta', true );
			$cpt_slug = $cpt_meta['slug'];
			$cpt_name_plural = $cpt_meta['plural'];
			$cpt_name_singular = $cpt_meta['singular'];
			register_post_type( $cpt_slug,
				array(
					'labels' => array(
							'name' => __( $cpt_name_plural ),
							'singular_name' => __( $cpt_name_singular ),
							'menu_name'             => __( $cpt_name_plural , 'text_domain' )
					),
				'public' => true,
				'has_archive' => true,
				'show_ui'  => true,
				'show_in_menu'  => true,
				'has_archive'        => true,
				'menu_position'      => null,
				)
			);
		}
		
	}
}