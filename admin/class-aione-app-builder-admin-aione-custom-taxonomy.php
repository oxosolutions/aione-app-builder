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
class Aione_App_Builder_Admin_Aione_Custom_Taxonomy {
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
		add_action( 'admin_menu', array( $this, 'aione_taxonomy_register_menu' ) );
		add_action( 'init', array( $this, 'aione_taxonomy_init' ) );
		add_action('wp_ajax_aione_cpt_delete_taxonomy', array($this, 'aione_cpt_delete_taxonomy'));

		$this->plugin_admin_aione_taxonomy_list = new Aione_App_Builder_Admin_Custom_Taxonomies_List_Table( $this->plugin_name, $this->version );

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

	function aione_taxonomy_register_menu(){
		add_submenu_page( 'aione_app_builder', 'Aione Taxonomies', 'Aione Taxonomies', 'manage_options', 'aione-taxonomies', array( $this,'aione_admin_menu_summary_taxonomies') );
		add_submenu_page( 'aione_app_builder', 'Add Aione Taxonomy', 'Add Aione Taxonomy', 'manage_options', 'aione-edit-taxonomy', array( $this,'aione_edit_taxonomy') );
	}

	function aione_taxonomy_init(){
		$taxonomies = $this->get_taxonomies_list();
		$taxonomies = $this->object_to_array_deep($taxonomies);
		//echo "<pre>";print_r($taxonomies);echo "</pre>";
		foreach ($taxonomies as $tax_key => $tax_value) {
			$taxonomies[$tax_key]['supports'] = array($tax_value['object_type'][0]  => 1);

		}

		//echo "<pre>";print_r($taxonomies);echo "</pre>";
		if ( get_option( 'aione-custom-taxonomies' ) == false ) {
			$deprecated = null;
    		$autoload = 'yes';
    		add_option( 'aione-custom-taxonomies', $taxonomies, $deprecated, $autoload );
		}

		$aione_registered_taxonomies = get_option('aione-custom-taxonomies');
		if ( !empty( $aione_registered_taxonomies ) ) {
	        foreach ( $aione_registered_taxonomies as $taxonomy => $data ) {
	            if ( empty($data) ) {
	                continue;
	            }
	            if (( isset($data['_builtin']) && $data['_builtin'] )) {
	                continue;
	            }
	            $this->aione_taxonomy_register( $taxonomy, $data );
	        }
	    }
	}

	function aione_taxonomy_register($taxonomy, $data){
		/*$default = $this->aione_taxonomy_default();
		$default['name'] = $data['slug'];
		$default['label'] = $data['labels']['name'];

		$default['labels'] = array_merge($default['labels'],$data['labels']);
	    foreach ($default['labels'] as $labels_key => $labels_value) {
	    	if(strpos($labels_value,"%s") !== false ){
	    		$labels_value = sprintf( $labels_value, $data['labels']['singular_name'] );
	    		$default['labels'][$labels_key] = $labels_value;
	    	}
	    	if($labels_key == 'menu_name' && $labels_value == "Enter title here"){
	    		$default['labels'][$labels_key] = $data['labels']['name'];
	    	}
	    }

	    $default['slug'] = $data['slug'];
	    $default['description'] = !empty( $data['description'] ) ? htmlspecialchars( stripslashes( $data['description'] ),
	                    ENT_QUOTES ) : '';

	   if(!empty($data['supports'])){
	   	$support_array = array_keys($data['supports']);
	   } else{
	   	$support_array = array();
	   }
	    
	    $default['object_type'] = $support_array;

	    $default['hierarchical'] = $data['hierarchical'];*/

	    register_taxonomy( $data['slug'], $data['object_type'], $data );
	}

	function aione_taxonomy_default(){
		return array(
	    	'name' => '',
	    	'label' => '',
	        'labels' => array(
	            'name' => '',
	            'singular_name' => '',
	            'search_items' => 'Search %s',
	            'popular_items' => 'Popular %s',
	            'all_items' => 'All %s',
	            'parent_item' => 'Parent %s',
	            'parent_item_colon' => 'Parent %s:',
	            'edit_item' => 'Edit %s',
	            'view_item' => 'View %s',
	            'update_item' => 'Update %s',
	            'add_new_item' => 'Add New %s',
	            'new_item_name' => 'New %s Name',
	            'separate_items_with_commas' => 'Separate %s with commas',
	            'add_or_remove_items' => 'Add or remove %s',
	            'choose_from_most_used' => 'Choose from the most used %s',
	            'not_found' => 'No %s found',
	            'no_terms' => 'No %s',
	            'items_list_navigation' => '%s list navigation',
	            'items_list' => '%s list',
	            'menu_name' => '%s',
	            'name_admin_bar' => '%s',
	            
	        ),
	        'slug' => '',
	        'description' => '',
	        'public' => true,
	        'rewrite' => array(
	            'with_front' => true,
	            'hierarchical' => true,
	            'ep_mask' => '',
	            'slug' => '',
	        ),
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'publicly_queryable' => true,
	        'hierarchical' => true,
	        'query_var' => '',
	        'show_rest' => true,
	        'rest_base' => '',
	        'show_in_nav_menus' => true,
	        'show_tagcloud' => true,
	        'show_in_quick_edit' => true,
	        'show_admin_column' => true,
	        'meta_box_cb' => '',
	        'object_type'=> array(),
	        'supports'=> array(),
	        'update_count_callback'=> '',
	        'rest_controller_class'=> '',
	        '_builtin' => false,
	    );
	}

	function get_taxonomies_list(){
		$allowed_taxonomies = get_taxonomies( array( 'public' => true, '_builtin' => true ), 'names' );
		$excluded_taxonomies = array( 'nav_menu', 'link_category', 'post_format' );
		$allowed_taxonomies = array_diff( $allowed_taxonomies, $excluded_taxonomies );
		$taxonomies = get_taxonomies( '', 'objects' );
		$taxonomies = (array)$taxonomies;
		foreach( $taxonomies as $taxonomy_slug => $taxonomy ) {
			if( ! in_array( $taxonomy_slug, $allowed_taxonomies ) ) {
				unset( $taxonomies[ $taxonomy_slug ] );
			}
		}
		return $taxonomies;
	}

	function object_to_array_deep( $object ) {
		if ( is_array( $object ) || is_object( $object ) ) {
			$result = array();
			foreach ( $object as $key => $value ) {
				$result[ $key ] = $this->object_to_array_deep( $value );
			}

			return $result;
		}

		return $object;
	}

	function aione_admin_menu_summary_taxonomies(){
		$this->aione_add_taxonomy_admin_header(
	        __( 'Taxonomies', 'aione' ),
	        array('page'=>'aione-edit-taxonomy'),
	        __('Add New', 'aione')
	    );
	    $listTable = $this->plugin_admin_aione_taxonomy_list;
        $listTable->prepare_items();
        ?>
        <form id="cpt-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $listTable->search_box(__('Search Taxonomies', 'wpcf'), 'search_id'); ?>
            <!-- Now we can render the completed list table -->
            <?php $listTable->display(); ?>
        </form>
    <?php
	}

	function aione_add_taxonomy_admin_header($title, $add_new = false, $add_new_title = false)
	{
	    echo '<div class="wrap">';
	    echo '<h1>', $title;
	    if ( !$add_new_title ) {
	    	$add_new_title = __('Add New', 'aione');  
	    }
	    if ( is_array($add_new) && isset($add_new['page']) ) {
	            printf(
	                ' <a href="%s" class="add-new-h2">%s</a>',
	                esc_url(add_query_arg( $add_new, admin_url('admin.php'))),
	                $add_new_title
	            );
	    }
	    echo '</h1>';
	}

	function aione_edit_taxonomy(){
		echo '<div class="wrap">';
		if (isset($_GET['aione-taxonomy']) && !empty($_GET['aione-taxonomy'])){
			echo '<h1>Edit Post Type <a href="'.admin_url('admin.php').'?page=aione-edit-taxonomy" class="add-new-h2">Add New</a></h1>';
		} else {
			echo '<h1>Add New Taxonomy</h1>';
		}

		echo '<form method="post" action="" class="wpcf-types-form wpcf-form-validate js-types-do-not-show-modal"><div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">';
		$this->taxonomy_metabox_main();
		echo '<div id="postbox-container-2" class="postbox-container">';
		echo '<div id="normal-sortables" class="meta-box-sortables ui-sortable">';
		$this->taxonomy_metabox_label();
		$this->taxonomy_metabox_type();
		$this->taxonomy_metabox_taxonomies();
		$this->taxonomy_metabox_options();
		echo '</div>';
		echo '</div>';
		echo '<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>';
		echo '</div></div></form>';
		echo '</div>';

	}

	function taxonomy_metabox_main(){ 
		wp_nonce_field( plugin_basename( __FILE__ ), 'taxonomy_noncename' );
		wp_nonce_field( plugin_basename( __FILE__ ), 'taxonomy_noncesave' );
		$aione_registered_taxonomies = get_option('aione-custom-taxonomies');
		if(isset($_POST['taxonomy_noncesave'])){
			$default  = $this->aione_taxonomy_default();
			$taxonomy_data = $_POST['taxonomy'];
			$builtin = false;
			$error = "";
			$default['_builtin'] = $builtin;
			$default['_toolset_edit_last'] = time();
			$default['_wpcf_author_id'] = get_current_user_id();
			$default['label'] = $taxonomy_data['labels']['name'];
			$default['name'] = $taxonomy_data['slug'];
			
		

			$default['labels'] = array_merge($default['labels'],$taxonomy_data['labels']);
		    foreach ($default['labels'] as $labels_key => $labels_value) {
		    	if(strpos($labels_value,"%s") !== false ){
		    		$labels_value = sprintf( $labels_value, $taxonomy_data['labels']['singular_name'] );
		    		$default['labels'][$labels_key] = $labels_value;
		    	}
		    	
		    }

	    	$default['slug'] = $taxonomy_data['slug'];
	    	$default['description'] = !empty( $taxonomy_data['description'] ) ? htmlspecialchars( stripslashes( $taxonomy_data['description'] ),
	                    ENT_QUOTES ) : '';

		   if(!empty($taxonomy_data['supports'])){
		   	$support_array = array_keys($taxonomy_data['supports']);
		   	$default['object_type'] = $support_array;
	    	$default['supports'] = $taxonomy_data['supports'];
		   } else{
		   	$support_array = array();
		   	$default['object_type'] = $support_array;
	    	$default['supports'] = $support_array;
		   }
	    
	    	

	    	$default['hierarchical'] = $data['hierarchical'];


			//echo "<pre>";print_r($default);echo "</pre>";

			$taxonomy_data['labels']['name'] = isset( $taxonomy_data['labels']['name'] )
	            ? sanitize_text_field( $taxonomy_data['labels']['name'])
	            : '';

	        $taxonomy_data['labels']['singular_name'] = isset( $taxonomy_data['labels']['singular_name'] )
	            ? sanitize_text_field( $taxonomy_data['labels']['singular_name'] )
	            : '';

	        if (empty( $taxonomy_data['labels']['name'] )|| empty( $taxonomy_data['labels']['singular_name'] )) {
	            $error = 'Please set taxonomy name';
	        } 

	        if (!isset($_GET['aione-taxonomy']) && empty($_GET['aione-taxonomy'])){
	        	if ( $this->aione_is_builtin_taxonomy($taxonomy_data['slug']) ) {
		            $builtin = true;
		            $error = "Slug is already used";
		        } else {
		        	if($aione_registered_taxonomies == false || $aione_registered_taxonomies == ""){
		        		$aione_registered_taxonomies = array();
		        	}
		        	if ( array_key_exists( $taxonomy_data['slug'], $aione_registered_taxonomies ) ) {
		                $error = "Slug is already used";
		            }
		        }
	        }

	        if($error == ""){
				if (isset($_GET['aione-taxonomy']) && !empty($_GET['aione-taxonomy'])){
					$aione_registered_taxonomies[$default['slug']] = array_replace($aione_registered_taxonomies[$default['slug']],$default);
					$aione_new_custom_taxonomies = $aione_registered_taxonomies;
				} else {
					if($aione_registered_taxonomies == false || $aione_registered_taxonomies == ""){
						$aione_new_custom_taxonomies = array($default['slug']=>$default);
					} else {
						$aione_registered_taxonomies[$default['slug']] = $default;
						$aione_new_custom_taxonomies = $aione_registered_taxonomies;
					}			
				}
				
				update_option('aione-custom-taxonomies', $aione_new_custom_taxonomies); 
				 if (!isset($_GET['aione-taxonomy']) && empty($_GET['aione-taxonomy'])){
				 	show_message("Taxonomy registered succesfully");
				 } else {
				 	show_message("Taxonomy updated succesfully");
				 }
				
			} else {
				show_message( $error );
			}
			//echo "<pre>";print_r($taxonomy_data);echo "</pre>";
		}



		if (isset($_GET['aione-taxonomy']) && !empty($_GET['aione-taxonomy'])){
			$taxonomy = $_GET['aione-taxonomy'];
			?>
			<input type="hidden" name="aione-taxonomy" value="<?php echo $taxonomy; ?>" class="wpcf-form-hidden form-hidden hidden">
			<?php
			$taxonomies = get_option('aione-custom-taxonomies', array());
			if (isset($taxonomies[$taxonomy]) && isset($taxonomies[$taxonomy]['slug'])) {
        		$taxonomy_meta =  $taxonomies[$taxonomy];
    		}
    	}	
    		?>
    		<div id="post-body-content" class="">
    			<table id="wpcf-types-form-name-table" class="wpcf-types-form-table widefat js-wpcf-slugize-container">
    				<thead><tr><th colspan="2">Name and description</th></tr></thead>
    				<tbody>
    				<tr>
    					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-plural">Name plural (<strong>required</strong>)</label></td>
    					<td><input type="text" id="taxonomy-plural" name="taxonomy[labels][name]" value="<?php echo $taxonomy_meta["labels"]["name"]; ?>" placeholder="Enter Taxonomy name plural" class="widefat wpcf-form-textfield form-textfield textfield js-types-validate"></td>
    				</tr>
					<tr>
						<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-singular">Name singular (<strong>required</strong>)</label></td>
						<td><input type="text" id="taxonomy-singular" name="taxonomy[labels][singular_name]" value="<?php echo $taxonomy_meta["labels"]["singular_name"]; ?>" placeholder="Enter Taxonomy name singular" class="widefat js-wpcf-slugize-source wpcf-form-textfield form-textfield textfield js-types-validate"></td>
					</tr>
					<tr>
						<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-slug">Slug (<strong>required</strong>)</label></td>
						<td><input type="text" id="taxonomy-slug" name="taxonomy[slug]" value="<?php echo $taxonomy_meta["slug"]; ?>" maxlength="30" placeholder="Enter Taxonomy slug" class="widefat js-wpcf-slugize wpcf-form-textfield form-textfield textfield js-types-validate"></td>
					</tr>
					<tr>
						<td><label class="wpcf-form-label wpcf-form-textarea-label" for="taxonomy-description">Description</label></td>
						<td>
						<textarea id="taxonomy-description" name="taxonomy[description]" rows="4" cols="60" placeholder="Enter Taxonomy description" class="js-wpcf-description wpcf-form-textarea form-textarea textarea"><?php echo $taxonomy_meta["description"]; ?></textarea>
						</td>
					</tr>
				</tbody>
				</table>
			</div>

			<div id="postbox-container-1" class="postbox-container">
	            <div id="side-sortables" class="meta-box-sortables ui-sortable">
	            	<div id="submitdiv" class="postbox ">
						<h2 class="hndle ui-sortable-handle"><span>Save</span></h2>
						<div class="inside">
	 						<div class="submitbox" id="submitpost">
	 						<div id="major-publishing-actions">
	 						<div id="publishing-action"><span class="spinner"></span>
	 						<?php if (isset($_GET['aione-taxonomy']) && !empty($_GET['aione-taxonomy'])){
	 							echo '<input type="submit" id="aione-submit" name="wpcf-submit" value="Update Taxonomy" class="button-primary wpcf-disabled-on-submit wpcf-form-submit form-submit submit">';
	 						} else {
	 							echo '<input type="submit" id="aione-submit" name="wpcf-submit" value="Save Taxonomy" class="button-primary wpcf-disabled-on-submit wpcf-form-submit form-submit submit">';
	 						}
	 						?>
	 						
	 						</div>
	 						<div class="clear"></div>
	 						</div>
	 						</div>
	 					</div>
					</div>
				</div>        
			</div>
    		<?php
		
	}

	function taxonomy_metabox_label(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_taxonomy_labels' );
		if (isset($_GET['aione-taxonomy']) && !empty($_GET['aione-taxonomy'])){
			$taxonomy = $_GET['aione-taxonomy'];
			$taxonomies = get_option('aione-custom-taxonomies', array());
			if (isset($taxonomies[$taxonomy]) && isset($taxonomies[$taxonomy]['slug'])) {
        		$taxonomy_meta =  $taxonomies[$taxonomy];
    		} else {
    			$taxonomy_meta = $this->aione_taxonomy_default();
    		}
		} else {
			$taxonomy_meta = $this->aione_taxonomy_default();
		}
		?>
		<div id="taxonomy_labels" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Labels</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Labels</span></h2>
		<div class="inside">
			<table class="wpcf-types-form-table widefat striped fixed">
			<tbody>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-search">Search Items</label></td>
					<td><input type="text" id="taxonomy-search" name="taxonomy[labels][search_items]" value="<?php echo $taxonomy_meta["labels"]["search_items"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The search items text. Default is __( 'Search Tags' ) or __( 'Search Categories' ).</p></td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-popular">Popular Items</label></td>
					<td><input type="text" id="taxonomy-popular" name="taxonomy[labels][popular_items]" value="<?php echo $taxonomy_meta["labels"]["popular_items"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The popular items text. Default is __( 'Popular Tags' ) or null.</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-all-items">All Items</label></td>
					<td><input type="text" id="taxonomy-all-items" name="taxonomy[labels][all_items]" value="<?php echo $taxonomy_meta["labels"]["all_items"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The all items text. Default is __( 'All Tags' ) or __( 'All Categories' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-parent-item">Parent Item</label></td>
					<td><input type="text" id="taxonomy-parent-item" name="taxonomy[labels][parent_item]" value="<?php echo $taxonomy_meta["labels"]["parent_item"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The parent item text. This string is not used on non-hierarchical taxonomies such as post tags. Default is null or __( 'Parent Category' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-parent-item-colon">Parent Item with colon</label></td>
					<td><input type="text" id="taxonomy-parent-item-colon" name="taxonomy[labels][parent_item_colon]" value="<?php echo $taxonomy_meta["labels"]["parent_item_colon"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The same as parent_item, but with colon : in the end null, __( 'Parent Category:' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-edit-item">Edit Item</label></td>
					<td><input type="text" id="taxonomy-edit-item" name="taxonomy[labels][edit_item]" value="<?php echo $taxonomy_meta["labels"]["edit_item"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The edit item text. Default is __( 'Edit Tag' ) or __( 'Edit Category' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-update-item">Update Item</label></td>
					<td><input type="text" id="taxonomy-update-item" name="taxonomy[labels][update_item]" value="<?php echo $taxonomy_meta["labels"]["update_item"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The update item text. Default is __( 'Update Tag' ) or __( 'Update Category' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-add-new-item">Add New Item</label></td>
					<td><input type="text" id="taxonomy-add-new-item" name="taxonomy[labels][add_new_item]" value="<?php echo $taxonomy_meta["labels"]["add_new_item"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The add new item text. Default is __( 'Add New Tag' ) or __( 'Add New Category' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-new-item-name">New Item Name</label></td>
					<td><input type="text" id="taxonomy-new-item-name" name="taxonomy[labels][new_item_name]" value="<?php echo $taxonomy_meta["labels"]["new_item_name"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The new item name text. Default is __( 'New Tag Name' ) or __( 'New Category Name' ).</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-separate-items-with-commas">Separate Items</label></td>
					<td><input type="text" id="taxonomy-separate-items-with-commas" name="taxonomy[labels][separate_items_with_commas]" value="<?php echo $taxonomy_meta["labels"]["separate_items_with_commas"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The separate item with commas text used in the taxonomy meta box. This string isn't used on hierarchical taxonomies. Default is __( 'Separate tags with commas' ), or null.</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-add-or-remove-items">Add or remove</label></td><td><input type="text" id="taxonomy-add-or-remove-items" name="taxonomy[labels][add_or_remove_items]" value="<?php echo $taxonomy_meta["labels"]["add_or_remove_items"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">the add or remove items text used in the meta box when JavaScript is disabled. This string isn't used on hierarchical taxonomies. Default is __( 'Add or remove tags' ) or null.</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-choose-from-most-used">Most Used</label></td>
					<td><input type="text" id="taxonomy-choose-from-most-used" name="taxonomy[labels][choose_from_most_used]" value="<?php echo $taxonomy_meta["labels"]["choose_from_most_used"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The choose from most used text used in the taxonomy meta box. This string isn't used on hierarchical taxonomies. Default is __( 'Choose from the most used tags' ) or null.</p>
					</td>
				</tr>
				<tr>
					<td><label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy-menu-name">Menu Name</label></td>
					<td><input type="text" id="taxonomy-menu-name" name="taxonomy[labels][menu_name]" value="<?php echo $taxonomy_meta["labels"]["menu_name"]; ?>" class="widefat wpcf-form-textfield form-textfield textfield">
					<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">The menu name text. This string is the name to give menu items. Defaults to value of name.</p>
					</td>
				</tr>
			</tbody>
			</table>
			</div>
		</div>
		<?php
	}

	function taxonomy_metabox_type(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_taxonomy_type' );
		?>
		<div id="aione_taxonomy_type" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Taxonomy type</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Taxonomy type</span></h2>
		<div class="inside">

			<input type="radio" id="taxonomy-hierarchical1" name="taxonomy[hierarchical]" value="1" class="wpcf-form-radio form-radio radio" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-radio-label" for="taxonomy-hierarchical1"><b>Hierarchical</b> - like post categories, with parent / children relationship and checkboxes to select taxonomy</label>
			<br>

			<input type="radio" id="taxonomy-hierarchical2" name="taxonomy[hierarchical]" value="0" class="wpcf-form-radio form-radio radio">&nbsp;<label class="wpcf-form-label wpcf-form-radio-label" for="taxonomy-hierarchical2"><b>Flat</b> - like post tags, with a text input to enter terms</label>
			<br>
		</div>
		</div>
		<?php
	}

	function taxonomy_metabox_taxonomies(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_taxonomy' );
		$post_types = get_post_types( '', 'objects' );
		$options = array();
		foreach ( $post_types as $post_type_slug => $post_type ){
			if ( !$post_type->show_ui ) {
                continue;
            }
			$options[$post_type_slug] = array(
                'name' => 'taxonomy[supports][' . $post_type_slug . ']',
                'title' => $post_type->labels->name,
            );
		}

		if (isset($_GET['aione-taxonomy']) && !empty($_GET['aione-taxonomy'])){
			$taxonomy = $_GET['aione-taxonomy'];
			$taxonomies = get_option('aione-custom-taxonomies', array());
			if (isset($taxonomies[$taxonomy]) && isset($taxonomies[$taxonomy]['slug'])) {
        		$taxonomy_meta =  $taxonomies[$taxonomy];
    		} else {
    			$taxonomy_meta = $this->aione_taxonomy_default();
    		}
		} else {
			$taxonomy_meta = $this->aione_taxonomy_default();
		}
        
        ?>
        <div id="types_taxonomies" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Post Types to be used with this Taxonomy</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Post Types to be used with this Taxonomy</span></h2>
		<div class="inside">
		<ul class="wpcf-list">
		<?php 
			foreach ($options as $option_key => $option_value) {
				if(!empty($taxonomy_meta['supports'])){
					if(array_key_exists($option_key, $taxonomy_meta['supports'])){
					$checked = "checked";
					} else {
						$checked = "";
					}
				} else {
						$checked = "";
					}
				
				echo "<li>";
				echo '<input type="checkbox" id="'.$option_value['name'].'" name="'.$option_value['name'].'" value="1" class="wpcf-form-checkbox form-checkbox checkbox" '.$checked.'>';
				echo '<label class="wpcf-form-label wpcf-form-checkbox-label" for="'.$option_value['name'].'">'.$option_value['title'].'</label>';
				echo "</li>";
			}
		?>
		</ul>
		</div>
		</div>
        <?php

		
	}
	

	function taxonomy_metabox_options(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_taxonomy_options' );
		?>
		<div id="taxonomy_options" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Options</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Options</span></h2>
		<div class="inside">
		<input type="checkbox" id="taxonomy_rewrite_enabled" name="taxonomy[rewrite][enabled]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_rewrite_enabled">Rewrite</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Rewrite permalinks with this format. Default will use $taxonomy as query var.</p>

		<div id="wpcf-types-form-rewrite-toggle">
		<label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy_rewrite_slug">Replace taxonomy slug with this</label>
		<input type="text" id="taxonomy_rewrite_slug" name="taxonomy[rewrite][slug]" value="" class="regular-text wpcf-form-textfield form-textfield textfield js-types-validate">
		<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">Optional. Replace taxonomy slug with this - defaults to taxonomy slug.</p>
		</div>

		<input type="checkbox" id="taxonomy_rewrite_with_front" name="taxonomy[rewrite][with_front]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_rewrite_with_front">Allow permalinks to be prepended with front base</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Defaults to true.</p>

		<input type="checkbox" id="taxonomy_rewrite_hierarchical" name="taxonomy[rewrite][hierarchical]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_rewrite_hierarchical">Hierarchical URLs</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">True or false allow hierarchical urls (implemented in <a href="http://codex.wordpress.org/Version_3.1" title="Version 3.1" target="_blank">Version 3.1</a>).</p>

		<input type="checkbox" id="taxonomy_show_ui" name="taxonomy[show_ui]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_show_ui">show_ui</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Whether to generate a default UI for managing this taxonomy.<br>Default: if not set, defaults to value of public argument.</p>

		<input type="checkbox" id="taxonomy_show_in_nav_menus" name="taxonomy[show_in_nav_menus]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_show_in_nav_menus">show_in_nav_menus</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">True makes this taxonomy available for selection in navigation menus.<br>Default: if not set, defaults to value of public argument.</p>

		<input type="checkbox" id="taxonomy_show_tagcloud" name="taxonomy[show_tagcloud]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_show_tagcloud">show_tagcloud</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Whether to allow the Tag Cloud widget to use this taxonomy.<br>Default: if not set, defaults to value of show_ui argument.</p>

		<input type="checkbox" id="taxonomy_show_admin_column" name="taxonomy[show_admin_column]" value="1" class="wpcf-form-checkbox form-checkbox checkbox">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_show_admin_column">show_admin_column</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Whether to allow automatic creation of taxonomy columns on associated post-types.<br>Default: false.</p>

		<input type="checkbox" id="taxonomy_query_var_enabled" name="taxonomy[query_var_enabled]" value="1" class="wpcf-form-checkbox form-checkbox checkbox" checked="checked">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_query_var_enabled">query_var</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Disable to prevent queries like "mysite.com/?taxonomy=example". Enable to use queries like "mysite.com/?taxonomy=example". Enable and set a value to use queries like "mysite.com/?query_var_value=example"<br>Default: true - set to $taxonomy.</p>
		<div id="wpcf-types-form-queryvar-toggle"><input type="text" name="taxonomy[query_var]" value="" class="regular-text wpcf-form-textfield form-textfield textfield"><div class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">Optional. String to customize query var</div></div>

		<label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy_update_count_callback">update_count_callback</label>
		<input type="text" id="taxonomy_update_count_callback" name="taxonomy[update_count_callback]" value="" class="regular-text wpcf-form-textfield form-textfield textfield">
		<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">Function name that will be called to update the count of an associated $object_type, such as post, is updated.<br>Default: None.</p>

		<h3>Meta box callback function</h3><input type="checkbox" id="taxonomy_meta_box_cb_disabled" name="taxonomy[meta_box_cb][disabled]" value="1" class="wpcf-form-checkbox form-checkbox checkbox">&nbsp;<label class="wpcf-form-label wpcf-form-checkbox-label" for="taxonomy_meta_box_cb_disabled">Hide taxonomy meta box.</label>

		<p class="description wpcf-form-description wpcf-form-description-checkbox description-checkbox">If you disable this, there will be no metabox on entry edit screen.</p>

		<div id="wpcf-types-form-meta_box_cb-toggle">
		<label class="wpcf-form-label wpcf-form-textfield-label" for="taxonomy_meta_box_cb_callback">meta_box_cb</label>
		<input type="text" id="taxonomy_meta_box_cb_callback" name="taxonomy[meta_box_cb][callback]" value="" class="regular-text wpcf-form-textfield form-textfield textfield">
		<p class="description wpcf-form-description wpcf-form-description-textfield description-textfield">Provide a callback function name for the meta box display.<br>Default: None.</p>
		</div>
		</div>
		</div>

		<?php
	}
	
	function aione_is_builtin_taxonomy($taxonomy){
	    switch($taxonomy) {
	    case 'post_tag':
	    case 'category':
	        return true;
	    }
	    $taxonomies = $this->aione_get_builtin_in_taxonomies();
	    return in_array($taxonomy, $taxonomies);
	}

	function aione_get_builtin_in_taxonomies($output = 'names'){
	    static $taxonomies = array();
	    if ( empty( $taxonomies ) ) {
	        $taxonomies = get_taxonomies(array('public' => true, '_builtin' => true), $output);
	    }
	    /**
	     * remove post_format
	     */
	    if ( isset( $taxonomies['post_format'] ) ) {
	        unset($taxonomies['post_format']);
	    }
	    return $taxonomies;
	}

	function aione_cpt_delete_taxonomy(){
    	$taxonomy = $this->wpcf_ajax_helper_get_taxonomy();
        if ( empty($taxonomy) ) {
            $this->wpcf_ajax_helper_print_error_and_die();
        }
        $taxonomies = get_option('aione-custom-taxonomies', array());
        unset($taxonomies[$taxonomy]);
        update_option('aione-custom-taxonomies', $taxonomies);
        echo json_encode(
            array(
                'output' => '',
                'execute' => 'reload',
                'wpcf_nonce_ajax_callback' => wp_create_nonce('execute'),
            )
        );
        die();
    }



    function wpcf_ajax_helper_print_error_and_die()
	{
	    echo json_encode(array(
	        'output' => __('Missing required data.', 'wpcf'),
	    ));
	    die;
	}
	function wpcf_ajax_helper_get_taxonomy()
	{
	    if (!isset($_REQUEST['wpcf-taxonomy']) || empty($_REQUEST['wpcf-taxonomy'])) {
	        return false;
	    }
	    $taxonomies = get_option('aione-custom-taxonomies', array());
	    if (
	        isset($taxonomies[$_REQUEST['wpcf-taxonomy']])
	        && isset($taxonomies[$_REQUEST['wpcf-taxonomy']]['slug'])
	    ) {
	        return $taxonomies[$_REQUEST['wpcf-taxonomy']]['slug'];
	    }
	    return false;
	}
		
} //Class