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
		add_action( 'admin_menu', array( $this, 'aione_cpt_register_menu' ) );
		add_action('admin_init', array( $this,'init_scripts_2'));
		add_action('wp_ajax_aione_cpt_get_icons_list', array($this, 'get_dashboard_icons_list'));
		add_action('wp_ajax_aione_cpt_delete_post_type', array($this, 'aione_cpt_delete_post_type'));
		add_action( 'init', array( $this, 'aione_custom_types_init' ) );

		$this->plugin_admin_aione_cpt_list = new Aione_App_Builder_Admin_Custom_Post_Types_List_Table( $this->plugin_name, $this->version );
	}

	function init_scripts_2(){
   
	    wp_enqueue_script('jquery-ui-dialog'); 
	    wp_register_style('jquery-ui-acpt', plugin_dir_url( __FILE__ ) . 'css/aione-app-builder-admin-acpt.css');
	    wp_enqueue_style('jquery-ui-acpt'); ///call the recently added style
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

	

	function aione_get_builtin_in_post_types()
	{
	    static $post_types = array();
	    if ( empty( $post_types ) ) {
	        $post_types = get_post_types(array('public' => true, '_builtin' => true));
	    }
	    return $post_types;
	}

	function aione_is_builtin_post_types($post_type)
	{
	    $post_types = $this->aione_get_builtin_in_post_types();
	    return in_array($post_type, $post_types);
	}

	function aione_cpt_register_menu(){
		add_submenu_page( 'aione_app_builder', 'Aione CPT', 'Aione CPT', 'manage_options', 'aione-cpt', array( $this,'aione_admin_menu_summary_cpt') );
		add_submenu_page( 'aione_app_builder', 'Add Aione CPT', 'Add Aione CPT', 'manage_options', 'aione-edit-cpt', array( $this,'aione_edit_cpt') );
	}


	function aione_admin_menu_summary_cpt()
	{
	    $this->aione_add_admin_header(
	        __( 'Post Types', 'aione' ),
	        array('page'=>'aione-edit-cpt'),
	        __('Add New', 'aione')
	    );

	    
	    $listTable = $this->plugin_admin_aione_cpt_list;
        $listTable->prepare_items();
        ?>
        <form id="cpt-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $listTable->search_box(__('Search Post Types', 'wpcf'), 'search_id'); ?>
            <!-- Now we can render the completed list table -->
            <?php $listTable->display(); ?>
        </form>
    <?php
    	    
	}

	function aione_add_admin_header($title, $add_new = false, $add_new_title = false)
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

	function aione_edit_cpt(){
		
		$aione_registered_post_types = get_option('aione_custom_post_types');
		
		if(isset($_POST['cpt_noncesave']) && isset($_POST['cpt_name_plural']) && isset($_POST['cpt_name_singular']) && isset($_POST['cpt_slug'])){
			
			$builtin = false;
			$error = "";
			$cpt_name_plural = $_POST['cpt_name_plural'];
			$cpt_name_singular = $_POST['cpt_name_singular'];
			$cpt_slug = $_POST['cpt_slug'];
			$cpt_menu_position = $_POST['cpt_menu_position'];
			$cpt_description = $_POST['cpt_description'];
			$cpt_icon = $_POST['cpt_menu_icon'];
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

			$cpt_supports_title = $_POST['aione_cpt_supports_title'];
			$cpt_supports_editor = $_POST['aione_cpt_supports_editor'];
			$cpt_supports_comments = $_POST['aione_cpt_supports_comments'];
			$cpt_supports_trackbacks = $_POST['aione_cpt_supports_trackbacks'];
			$cpt_supports_revisions = $_POST['aione_cpt_supports_revisions'];
			$cpt_supports_author = $_POST['aione_cpt_supports_author'];
			$cpt_supports_excerpt = $_POST['aione_cpt_supports_excerpt'];
			$cpt_supports_thumbnail = $_POST['aione_cpt_supports_thumbnail'];
			$cpt_supports_custom_fields = $_POST['aione_cpt_supports_custom_fields'];
			$cpt_supports_page_attributes = $_POST['aione_cpt_supports_page_attributes'];
			$cpt_supports_post_formats = $_POST['aione_cpt_supports_post_formats'];

			$raw_array = array();
			if($cpt_supports_title == 1){
				$raw_array['title'] = 1;
			}
			if($cpt_supports_editor == 1){
				$raw_array['editor'] = 1;
			}
			if($cpt_supports_comments == 1){
				$raw_array['comments'] = 1;
			}
			if($cpt_supports_trackbacks == 1){
				$raw_array['trackbacks'] = 1;
			}
			if($cpt_supports_revisions == 1){
				$raw_array['revisions'] = 1;
			}
			if($cpt_supports_author == 1){
				$raw_array['author'] = 1;
			}
			if($cpt_supports_excerpt == 1){
				$raw_array['excerpt'] = 1;
			}
			if($cpt_supports_thumbnail == 1){
				$raw_array['thumbnail'] = 1;
			}
			if($cpt_supports_custom_fields == 1){
				$raw_array['custom-fields'] = 1;
			}
			if($cpt_supports_page_attributes == 1){
				$raw_array['page-attributes'] = 1;
			}
			if($cpt_supports_post_formats == 1){
				$raw_array['post-formats'] = 1;
			}


			$cpt_rewrite_enabled = $_POST['aione_cpt_rewrite_enabled'];
			$cpt_rewrite_custom = $_POST['aione_cpt_rewrite_custom'];
			$cpt_rewrite_slug = $_POST['aione_cpt_rewrite_slug'];
			$cpt_rewrite_with_front = $_POST['aione_cpt_rewrite_with_front'];
			$cpt_rewrite_feeds = $_POST['aione_cpt_rewrite_feeds'];
			$cpt_rewrite_pages = $_POST['aione_cpt_rewrite_pages'];
			$cpt_has_archive = $_POST['aione_cpt_has_archive'];
			$cpt_has_archive_slug = $_POST['aione_cpt_has_archive_slug'];
			$cpt_show_in_menu = $_POST['aione_cpt_show_in_menu'];
			$cpt_show_in_menu_page = $_POST['aione_cpt_show_in_menu_page'];
			$cpt_show_ui = $_POST['aione_cpt_show_ui'];
			$cpt_publicly_queryable = $_POST['aione_cpt_publicly_queryable'];
			$cpt_exclude_from_search = $_POST['aione_cpt_exclude_from_search'];
			$cpt_hierarchical = $_POST['aione_cpt_hierarchical'];
			$cpt_can_export = $_POST['aione_cpt_can_export'];
			$cpt_show_in_nav_menus = $_POST['aione_cpt_show_in_nav_menus'];
			$cpt_query_var_enabled = $_POST['aione_cpt_query_var_enabled'];
			$cpt_query_var = $_POST['aione_cpt_query_var'];
			$cpt_permalink_epmask = $_POST['aione_cpt_permalink_epmask'];
			$cpt_show_in_rest = $_POST['aione_cpt_show_in_rest'];
			$cpt_rest_base = $_POST['aione_cpt_rest_base'];
			$cpt_taxonomies = $_POST['taxnomy'];

			// Sanitize data
	        $cpt_name_plural = isset( $cpt_name_plural )
	            ? sanitize_text_field( $cpt_name_plural )
	            : '';

	        $cpt_name_singular = isset( $cpt_name_singular )
	            ? sanitize_text_field( $cpt_name_singular )
	            : '';

	        if (empty( $cpt_name_plural )|| empty( $cpt_name_singular )) {
	            $error = 'Please set post type name';
	        }

	        if (!isset($_GET['aione-post-type']) && empty($_GET['aione-post-type'])){
	        	if ( $this->aione_is_builtin_post_types($cpt_slug) ) {
		            $builtin = true;
		            $error = "Slug is already used";
		        } else {
		        	if($aione_registered_post_types == false || $aione_registered_post_types == ""){
		        		$aione_registered_post_types = array();
		        	}
		        	if ( array_key_exists( $cpt_slug, $aione_registered_post_types ) ) {
		                $error = "Slug is already used";
		            }
		        }
	        }
	        
	        
	       
			$cpt_meta = array();

			$cpt_meta['_builtin'] = $builtin;
			$cpt_meta['_toolset_edit_last'] = time();
			$cpt_meta['_wpcf_author_id'] = get_current_user_id();
			$cpt_meta['icon'] = $cpt_icon;
			$cpt_meta['labels'] = array(
					'name'=>$cpt_name_plural,
					'singular_name'=>$cpt_name_singular,
					'add_new'=>$cpt_add_new,
					'add_new_item'=>__($cpt_add_new_item,$cpt_name_singular),
					'edit_item'=>__($cpt_edit_item,$cpt_name_singular),
					'new_item'=>__($cpt_new_item,$cpt_name_singular),
					'view_item'=>__($cpt_view_item,$cpt_name_singular),
					'search_items'=>__($cpt_search_items,$cpt_name_singular),
					'not_found'=>__($cpt_not_found,$cpt_name_singular),
					'not_found_in_trash'=>__($cpt_not_found_in_trash,$cpt_name_singular),
					'parent_item_colon'=>$cpt_parent_item_colon,
					'all_items'=>$cpt_all_items,
					'menu_name'=>$cpt_enter_title_here,
				);

			$cpt_meta['slug'] = $cpt_slug;
			$cpt_meta['description'] = $cpt_description;
			$cpt_meta['status'] = get_post_status($post_id);
			$cpt_meta['menu_position'] = $cpt_menu_position;

			$cpt_meta['supports'] = $raw_array;

			$cpt_meta['rewrite'] = array(
					'enabled' => $cpt_rewrite_enabled,
					'custom' => $cpt_rewrite_custom,
					'slug' => $cpt_rewrite_slug,
					'with_front' => $cpt_rewrite_with_front,
					'feeds' => $cpt_rewrite_feeds,
					'pages' => $cpt_rewrite_pages
				);
			$cpt_meta['taxonomies'] = $cpt_taxonomies;
			$cpt_meta['has_archive'] = $cpt_has_archive;
			$cpt_meta['has_archive_slug'] = $cpt_has_archive_slug;
			$cpt_meta['show_in_menu'] = $cpt_show_in_menu;
			$cpt_meta['show_in_menu_page'] = $cpt_show_in_menu_page;
			$cpt_meta['show_ui'] = $cpt_show_ui;
			$cpt_meta['publicly_queryable'] = $cpt_publicly_queryable;
			$cpt_meta['exclude_from_search'] = $cpt_exclude_from_search;
			$cpt_meta['hierarchical'] = $cpt_hierarchical;
			$cpt_meta['can_export'] = $cpt_can_export;
			$cpt_meta['show_in_nav_menus'] = $cpt_show_in_nav_menus;
			$cpt_meta['query_var_enabled'] = $cpt_query_var_enabled;
			$cpt_meta['query_var'] = $cpt_query_var;
			$cpt_meta['permalink_epmask'] = $cpt_permalink_epmask;
			$cpt_meta['show_in_rest'] = $cpt_show_in_rest;
			$cpt_meta['rest_base'] = $cpt_rest_base;

			if(!empty($cpt_meta['taxonomies'])){
				$tax = $cpt_meta['taxonomies'];
				$taxonomies = get_option('aione-custom-taxonomies', array());
				
				foreach ($tax as $tax_key => $tax_value) {
					if(array_key_exists($tax_key, $taxonomies)){
						$taxonomies[$tax_key]['supports'][$cpt_slug] = 1;
						$taxonomies[$tax_key]['object_type'] = $cpt_slug;
					}
				}
				update_option('aione-custom-taxonomies', $taxonomies);
			} else {
				$tax = array();
				$taxonomies = get_option('aione-custom-taxonomies', array());
				
				foreach ($taxonomies as $taxonomies_key => $taxonomies_value) {
					unset($taxonomies[$taxonomies_key]['supports'][$cpt_slug]);
					if(($key = array_search($cpt_slug, $taxonomies[$taxonomies_key]['object_type'])) !== false) {
					    unset($taxonomies[$taxonomies_key]['object_type'][$key]);
					}
				}
				update_option('aione-custom-taxonomies', $taxonomies);
			}

			if($error == ""){
				if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])){
					$aione_registered_post_types[$cpt_slug] = array_replace($aione_registered_post_types[$cpt_slug],$cpt_meta);
					$aione_new_custom_post_types = $aione_registered_post_types;
				} else {
					if($aione_registered_post_types == false || $aione_registered_post_types == ""){
						$aione_new_custom_post_types = array($cpt_slug=>$cpt_meta);
					} else {
						$aione_registered_post_types[$cpt_slug] = $cpt_meta;
						$aione_new_custom_post_types = $aione_registered_post_types;
					}			
				}
				
				update_option('aione_custom_post_types', $aione_new_custom_post_types); 
				if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])) {
					show_message("Post type updated succesfully");
				} else {
					show_message("Post type registered succesfully");
				}
				
			} else {
				show_message( $error );
			}

			
		}


		echo '<div class="wrap">';
		if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])){
			echo '<h1>Edit Post Type <a href="'.admin_url('admin.php').'?page=aione-edit-cpt" class="add-new-h2">Add New</a></h1>';
		} else {
			echo '<h1>Add New Post Type</h1>';
		}
		
		echo '<form method="post" action="" class="wpcf-types-form wpcf-form-validate js-types-do-not-show-modal"><div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">';
		$this->cpt_metabox_main();
		echo '<div id="postbox-container-2" class="postbox-container">';
		echo '<div id="normal-sortables" class="meta-box-sortables ui-sortable">';
		$this->cpt_metabox_label();
		$this->cpt_metabox_taxonomy();
		$this->cpt_metabox_display_sections();
		$this->cpt_metabox_options();
		echo '</div>';
		echo '</div>';
		echo '<div id="advanced-sortables" class="meta-box-sortables ui-sortable"></div>';
		echo '</div></div></form>';
		echo '</div>';

		
	}

	
	
	function cpt_metabox_main(){
		wp_nonce_field( plugin_basename( __FILE__ ), 'cpt_noncename' );
		wp_nonce_field( plugin_basename( __FILE__ ), 'cpt_noncesave' );
		
		if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])){
			$cpt = $_GET['aione-post-type'];
			?>
			<input type="hidden" name="cpt-post-type" value="<?php echo $cpt; ?>" class="wpcf-form-hidden form-hidden hidden">
			<?php
			$custom_types = get_option('aione_custom_post_types', array());
			if (isset($custom_types[$cpt]) && isset($custom_types[$cpt]['slug'])) {
        		$cpt_meta =  $custom_types[$cpt];
    		}
			
		}
		?>
		<div id="post-body-content" class="">
		<table id="aione-cpt-form-name-table" class="aione-cpt-form-table widefat js-wpcf-slugize-container">
		<thead><tr><th colspan="2">Name and description</th></tr></thead>
			<tbody>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="name-plural">Name plural (<strong>required</strong>)</label></td>
					<td><input type="text" id="name-plural" name="cpt_name_plural" value="<?php echo $cpt_meta["labels"]["name"]; ?>"  placeholder="Enter Post Type name plural" class="large-text aione-cpt-form-textfield form-textfield textfield js-types-validate" data-anonymous-post-type="this Post Type"></td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="name-singular">Name singular (<strong>required</strong>)</label></td>
					<td><input type="text" id="name-singular" name="cpt_name_singular" value="<?php echo $cpt_meta["labels"]["singular_name"]; ?>" placeholder="Enter Post Type name singular" class="large-text aione-cpt-form-textfield form-textfield textfield js-types-validate js-wpcf-slugize-source" data-anonymous-post-type="this Post Type"></td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="slug">Slug (<strong>required</strong>)</label></td>
					<td><input type="text" id="slug" name="cpt_slug" value="<?php echo $cpt_meta["slug"]; ?>" maxlength="20" placeholder="Enter Post Type slug" class="large-text aione-cpt-form-textfield form-textfield textfield js-types-validate js-wpcf-slugize "></td>
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
					        	echo '<option value="'.$value.'" ';
					        	$target_url = explode( '--aione-cpt-add-menu-after--', $value );
					        	$saved_url = explode( '--aione-cpt-add-menu-after--', $cpt_meta["menu_position"] );
					        	if($target_url[1] == $saved_url[1]){
					        		echo ' selected = "selected" ';
					        	}
					        	echo 'class="aione-cpt-form-option form-option option">'.$key.'</option>';
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
					<?php
					if($cpt_meta["icon"]){
						$icon = $cpt_meta["icon"];
					} else {
						$icon = "admin-post";
					}
					?>
					<td><div class="aione-cpt-menu-image dashicons-before dashicons-<?php echo $icon ;?>"><br></div>
					<button type="button" id="cpt_icon" name="cpt_icon"  class="js-wpcf-choose-icon aione-cpt-form-button form-button button" data-wpcf-nonce="<?php echo wp_create_nonce('post-type-dashicons-list');?>" data-wpcf-post-type="<?php echo $cpt_meta["slug"]; ?>" data-wpcf-value="<?php echo $cpt_meta["icon"]; ?>">Change icon</button>
					</td>
				</tr>
			</tbody>
		</table>
		<input type="hidden" id="aione-types-icon" name="cpt_menu_icon" value="<?php echo $cpt_meta["icon"]; ?>" class="wpcf-form-hidden form-hidden hidden">

		</div>


		<div id="postbox-container-1" class="postbox-container">
            <div id="side-sortables" class="meta-box-sortables ui-sortable">
            	<div id="submitdiv" class="postbox ">
					<h2 class="hndle ui-sortable-handle"><span>Save</span></h2>
					<div class="inside">
 						<div class="submitbox" id="submitpost">
 						<div id="major-publishing-actions">
 						<div id="publishing-action"><span class="spinner"></span>
 						<?php if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])){
 							echo '<input type="submit" id="aione-submit" name="wpcf-submit" value="Update Post Type" class="button-primary wpcf-disabled-on-submit wpcf-form-submit form-submit submit">';
 						} else {
 							echo '<input type="submit" id="aione-submit" name="wpcf-submit" value="Save Post Type" class="button-primary wpcf-disabled-on-submit wpcf-form-submit form-submit submit">';
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

	function cpt_metabox_label(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_cpt_labels' );
		if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])){
			$cpt = $_GET['aione-post-type'];
			$custom_types = get_option('aione_custom_post_types', array());
			if (isset($custom_types[$cpt]) && isset($custom_types[$cpt]['slug'])) {
        		$cpt_meta =  $custom_types[$cpt];
    		} else {
    			$cpt_meta = $this->aione_custom_types_default();
    		}
		} else {
			$cpt_meta = $this->aione_custom_types_default();
		}
		?>
		<div id="types_labels" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Labels</span><span class="toggle-indicator" aria-hidden="true"></span></button>
	 	<h2 class="hndle ui-sortable-handle"><span>Labels</span></h2>
		<div class="inside">
			<table class="aione-cpt-form-table widefat striped fixed">
			<tbody>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_add_new">Add New</label>
					</td>
					<td><input type="text" id="aione_cpt_add_new" name="aione_cpt_add_new" value="<?php echo $cpt_meta["labels"]["add_new"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description wpcf-form-description aione-cpt-form-description-textfield description-textfield">The add new text. The default is Add New for both hierarchical and non-hierarchical types.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_add_new_item">Add New Item</label>
					</td>
					<td>
						<input type="text" id="aione_cpt_add_new_item" name="aione_cpt_add_new_item" value="<?php echo $cpt_meta["labels"]["add_new_item"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The add new item text. Default is Add New Post/Add New Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_edit_item">Edit Item</label>
					</td>
					<td><input type="text" id="aione_cpt_edit_item" name="aione_cpt_edit_item" value="<?php echo $cpt_meta["labels"]["edit_item"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The edit item text. Default is Edit Post/Edit Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_new_item">New Item</label>
					</td>
					<td><input type="text" id="aione_cpt_new_item" name="aione_cpt_new_item" value="<?php echo $cpt_meta["labels"]["new_item"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The new item text. Default is New Post/New Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_view_item">View Item</label>
					</td>
					<td><input type="text" id="aione_cpt_view_item" name="aione_cpt_view_item" value="<?php echo $cpt_meta["labels"]["view_item"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The view item text. Default is View Post/View Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_search_items">Search Items</label>
					</td>
					<td><input type="text" id="aione_cpt_search_items" name="aione_cpt_search_items" value="<?php echo $cpt_meta["labels"]["search_items"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The search items text. Default is Search Posts/Search Pages.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_not_found">Not Found</label>
					</td>
					<td><input type="text" id="aione_cpt_not_found" name="aione_cpt_not_found" value="<?php echo $cpt_meta["labels"]["not_found"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The not found text. Default is No posts found/No pages found.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_not_found_in_trash">Not Found In Trash</label>
					</td>
					<td><input type="text" id="aione_cpt_not_found_in_trash" name="aione_cpt_not_found_in_trash" value="<?php echo $cpt_meta["labels"]["not_found_in_trash"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The not found in trash text. Default is No posts found in Trash/No pages found in Trash.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_parent_item_colon">Parent Description</label>
					</td>
					<td><input type="text" id="aione_cpt_parent_item_colon" name="aione_cpt_parent_item_colon" value="<?php echo $cpt_meta["labels"]["parent_item_colon"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The parent text. This string isn't used on non-hierarchical types. In hierarchical ones the default is Parent Page.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_all_items">All Items</label>
					</td>
					<td><input type="text" id="aione_cpt_all_items" name="aione_cpt_all_items" value="<?php echo $cpt_meta["labels"]["all_items"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The all items text used in the menu. Default is the Name label.</p>
					</td>
				</tr>
				<tr>
					<td><label class="aione-cpt-form-label aione-cpt-form-textfield-label" for="aione_cpt_enter_title_here">Enter title here</label>
					</td>
					<td><input type="text" id="aione_cpt_enter_title_here" name="aione_cpt_enter_title_here" value="<?php echo $cpt_meta["labels"]["menu_name"]; ?>" class="widefat aione-cpt-form-textfield form-textfield textfield">
						<p class="description aione-cpt-form-description aione-cpt-form-description-textfield description-textfield">The text used as placeholder of post title. Default is the "Enter title here".</p>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
      </div>
      <?php
	}

	function cpt_metabox_taxonomy(){
		$taxonomies = get_option('aione-custom-taxonomies',array());
				
		?>
		<div id="types_taxonomies" class="postbox ">
			<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Taxonomies to be used with <i class="js-wpcf-singular"></i></span><span class="toggle-indicator" aria-hidden="true"></span></button>
			<h2 class="hndle ui-sortable-handle"><span>Taxonomies to be used with <i class="js-wpcf-singular"></i></span></h2>
			<div class="inside">
			<ul class="wpcf-list">
			<?php
			foreach ($taxonomies as $taxonomy_slug => $taxonomy) {
				$tax_name = $taxonomy['name'];
				if(isset($_GET['aione-post-type']) && ($_GET['aione-post-type'])){
					$custom_type = $_GET['aione-post-type'];
					if(array_key_exists($custom_type, $taxonomy['supports'])){
						$checked = "checked";
					} else {
						$checked = "";
					}
				} else {
					$checked = "";
				}
				echo '<li>';
				echo '<input type="checkbox" id="form-24467c3cbe1ed6773f1b35dbc95a876d-1" name="taxnomy['.$tax_name.']" value="1" class="wpcf-form-checkbox form-checkbox checkbox" '.$checked.'>
				<label class="wpcf-form-label wpcf-form-checkbox-label" for="form-24467c3cbe1ed6773f1b35dbc95a876d-1">'.$taxonomy['labels']['singular_name'].'</label>';
				echo '</li>';
			}
			?>
			</ul>
			</div>
		</div>	
		<?php
		
	}

	function cpt_metabox_display_sections(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_cpt_display_sections' );
		if (isset($_GET['aione-post-type']) && !empty($_GET['aione-post-type'])){
			$cpt = $_GET['aione-post-type'];
			$custom_types = get_option('aione_custom_post_types', array());
			$cpt_meta =  $custom_types[$cpt];
		} else {
			$cpt_meta['supports'] = array(
    					'title' => 1,
    					'editor' => 1,
    					'thumbnail' => 1
    				);
		}
		
		?>
		<div id="types_display_sections" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Sections to display when editing <i class="js-wpcf-singular"></i></span><span class="toggle-indicator" aria-hidden="true"></span></button>
		<h2 class="hndle ui-sortable-handle"><span>Sections to display when editing <i class="js-wpcf-singular"></i></span></h2>
		<div class="inside">
		<?php
		if(!empty($cpt_meta["supports"]["title"]) && $cpt_meta["supports"]["title"] == 1){
			$checked = "checked";
		} else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-title" name="aione_cpt_supports_title" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-title">Title</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Text input field to create a post title.</p>

		<?php
		if(!empty($cpt_meta["supports"]["editor"]) && $cpt_meta["supports"]["editor"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-editor" name="aione_cpt_supports_editor" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-editor">Editor</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Content input box for writing.</p>

		<?php
		if(!empty($cpt_meta["supports"]["comments"]) && $cpt_meta["supports"]["comments"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-comments" name="aione_cpt_supports_comments" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-comments">Comments</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Ability to turn comments on/off.</p>

		<?php
		if(!empty($cpt_meta["supports"]["trackbacks"]) && $cpt_meta["supports"]["trackbacks"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-trackbacks" name="aione_cpt_supports_trackbacks" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-trackbacks">Trackbacks</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Ability to turn trackbacks and pingbacks on/off.</p>

		<?php
		if(!empty($cpt_meta["supports"]["revisions"]) && $cpt_meta["supports"]["revisions"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-revisions" name="aione_cpt_supports_revisions" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-revisions">Revisions</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Allows revisions to be made of your post.</p>

		<?php
		if(!empty($cpt_meta["supports"]["author"]) && $cpt_meta["supports"]["author"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-author" name="aione_cpt_supports_author" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-author">Author</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Displays a dropdown menu for changing the post author.</p>

		<?php
		if(!empty($cpt_meta["supports"]["excerpt"]) && $cpt_meta["supports"]["excerpt"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-excerpt" name="aione_cpt_supports_excerpt" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-excerpt">Excerpt</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">A text area for writing a custom excerpt.</p>

		<?php
		if(!empty($cpt_meta["supports"]["thumbnail"]) && $cpt_meta["supports"]["thumbnail"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-thumbnail" name="aione_cpt_supports_thumbnail" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-thumbnail">Featured Image</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Allows to upload a featured image to the post.</p>

		<?php
		if(!empty($cpt_meta["supports"]["custom-fields"]) && $cpt_meta["supports"]["custom-fields"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-custom-fields" name="aione_cpt_supports_custom_fields" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-custom-fields">Custom Fields</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">The native WordPress custom post fields list. If you don't select this, Types post fields will still display.</p>

		<?php
		if(!empty($cpt_meta["supports"]["page-attributes"]) && $cpt_meta["supports"]["page-attributes"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-page-attributes" name="aione_cpt_supports_page_attributes" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-page-attributes">Page Attributes</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Menu order and page parent (only available for hierarchical posts).</p>

		<?php
		if(!empty($cpt_meta["supports"]["post-formats"]) && $cpt_meta["supports"]["post-formats"] == 1){
			$checked = "checked";
		}else {
			$checked = "";
		}
		?>
		<input type="checkbox" id="aione-supports-post-formats" name="aione_cpt_supports_post_formats" value="1" class="aione-form-checkbox form-checkbox checkbox" <?php echo $checked;?>><label class="aione-form-label aione-form-checkbox-label" for="aione-supports-post-formats">Post Formats</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">A selector for the format to use for the post.</p>

		</div>
		</div>

		<?php
	}
	

	function cpt_metabox_options(){
		wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_cpt_options' );
		?>
		<div id="types_options" class="postbox ">
		<button type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Options</span><span class="toggle-indicator" aria-hidden="true"></span></button><h2 class="hndle ui-sortable-handle"><span>Options</span></h2>
		<div class="inside">
		<input type="checkbox" id="aione_cpt_rewrite_enabled" name="aione_cpt_rewrite_enabled" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_rewrite_enabled">Rewrite</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Rewrite permalinks with this format. False to prevent rewrite. Default: true and use post type as slug.</p>


		<input type="radio" id="aione_cpt_rewrite_custom1" name="aione_cpt_rewrite_custom" value="normal" class="aione-form-radio form-radio radio" checked="checked"><label class="aione-form-label aione-form-radio-label" for="aione_cpt_rewrite_custom1">Use the normal WordPress URL logic</label>
		<br>

		<input type="radio" id="aione_cpt_rewrite_custom2" name="aione_cpt_rewrite_custom" value="custom" class="aione-form-radio form-radio radio"><label class="aione-form-label aione-form-radio-label" for="aione_cpt_rewrite_custom2">Use a custom URL format</label>
		<br>
		<br>
		<div id="aione-types-form-rewrite-toggle" class="hidden">

		<input type="text" id="aione_cpt_rewrite_slug" name="aione_cpt_rewrite_slug" value="" class="widefat aione-form-textfield form-textfield textfield js-types-validate">
		<p class="description aione-form-description aione-form-description-textfield description-textfield">Optional. Prepend posts with this slug - defaults to post type's name.</p>
		</div>

		<input type="checkbox" id="aione_cpt_rewrite_with_front" name="aione_cpt_rewrite_with_front" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_rewrite_with_front">Allow permalinks to be prepended with front base</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Example: if your permalink structure is /blog/, then your links will be: false-&gt;/news/, true-&gt;/blog/news/. Defaults to true.</p>

		<input type="checkbox" id="aione_cpt_rewrite_feeds" name="aione_cpt_rewrite_feeds" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_rewrite_feeds">Feeds</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Defaults to has_archive value.</p>

		<input type="checkbox" id="aione_cpt_rewrite_pages" name="aione_cpt_rewrite_pages" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_rewrite_pages">Pages</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Defaults to true.</p>

		<input type="checkbox" id="aione_cpt_has_archive" name="aione_cpt_has_archive" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_has_archive">has_archive</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Allow to have custom archive slug for CPT.<br>Default: not set.</p>
		<div id="aione_cpt_has_archive_slug"><input type="text" name="aione_cpt_has_archive_slug" class="regular-text" value=""><div class="description aione-form-description aione-form-description-checkbox description-checkbox">Optional. Default is value of rewrite or CPT slug.</div></div>

		<input type="checkbox" id="aione_cpt_show_in_menu" name="aione_cpt_show_in_menu" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_show_in_menu">show_in_menu</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Whether to show the post type in the admin menu and where to show that menu. Note that show_ui must be true.<br>Default: null.</p>
		<div id="aione_cpt_show_in_menu_page"><input type="text" name="aione_cpt_show_in_menu_page" class="regular-text" value=""><div class="description aione-form-description aione-form-description-checkbox description-checkbox">Optional. Top level page like 'tools.php' or 'edit.php?post_type=page'</div></div>

		<input type="checkbox" id="aione_cpt_show_ui" name="aione_cpt_show_ui" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_show_ui">show_ui</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Generate a default UI for managing this post type.<br>Default: value of public argument.</p>

		<input type="checkbox" id="aione_cpt_publicly_queryable" name="aione_cpt_publicly_queryable" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_publicly_queryable">publicly_queryable</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Whether post_type queries can be performed from the front end.<br>Default: value of public argument.</p>

		<input type="checkbox" id="aione_cpt_exclude_from_search" name="aione_cpt_exclude_from_search" value="1" class="aione-form-checkbox form-checkbox checkbox"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_exclude_from_search">exclude_from_search</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Whether to exclude posts with this post type from search results.<br>Default: value of the opposite of the public argument.</p>

		<input type="checkbox" id="aione_cpt_hierarchical" name="aione_cpt_hierarchical" value="1" class="aione-form-checkbox form-checkbox checkbox"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_hierarchical">hierarchical</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Whether the post type is hierarchical. Allows Parent to be specified.<br>Default: false.</p>

		<input type="checkbox" id="aione_cpt_can_export" name="aione_cpt_can_export" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_can_export">can_export</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Can this post_type be exported.<br>Default: true.</p>

		<input type="checkbox" id="aione_cpt_show_in_nav_menus" name="aione_cpt_show_in_nav_menus" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_show_in_nav_menus">show_in_nav_menus</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Whether post_type is available for selection in navigation menus.<br>Default: value of public argument.</p>

		<input type="checkbox" id="aione_cpt_query_var_enabled" name="aione_cpt_query_var_enabled" value="1" class="aione-form-checkbox form-checkbox checkbox" checked="checked"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_query_var_enabled">query_var</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Disable to prevent queries like "mysite.com/?post_type=example". Enable to use queries like "mysite.com/?post_type=example". Enable and set a value to use queries like "mysite.com/?query_var_value=example"<br>Default: true - set to $post_type.</p>
		<div id="aione_cpt_query_var"><input type="text" name="aione_cpt_query_var" value="" class="regular-text"><div class="description aione-form-description aione-form-description-checkbox description-checkbox">Optional. String to customize query var</div></div>

		<label class="aione-form-label aione-form-textfield-label" for="aione_cpt_permalink_epmask">Permalink epmask</label>
		<input type="text" id="aione_cpt_permalink_epmask" name="aione_cpt_permalink_epmask" value="EP_PERMALINK" class="aione-form-textfield form-textfield textfield">
		<p class="description aione-form-description aione-form-description-textfield description-textfield">Default value EP_PERMALINK. More info here <a href="http://core.trac.wordpress.org/ticket/12605" target="_blank">link</a>.</p>

		<input type="checkbox" id="aione_cpt_show_in_rest" name="aione_cpt_show_in_rest" value="1" class="aione-form-checkbox form-checkbox checkbox"><label class="aione-form-label aione-form-checkbox-label" for="aione_cpt_show_in_rest">show_in_rest</label>

		<p class="description aione-form-description aione-form-description-checkbox description-checkbox">Whether to expose this post type in the REST API.<br>Default: false.</p>

		<label class="aione-form-label aione-form-textfield-label" for="aione-textfield-5e5f456aff0d4063652b78a0ef377fae">Rest Base</label>
		<input type="text" id="aione_cpt_rest_base" name="aione_cpt_rest_base" value="" class="aione-form-textfield form-textfield textfield">
		<p class="description aione-form-description aione-form-description-textfield description-textfield">The base slug that this post type will use when accessed using the REST API.<br>Default: $post_type.</p>

		</div>
		<?php 
	}


	function aione_custom_types_init(){
		global $menu, $submenu;
		$custom_types = get_option( 'aione_custom_post_types', array() );
	    if ( !empty( $custom_types ) ) {
	        foreach ( $custom_types as $post_type => $data ) {
	            if ( empty($data) ) {
	                continue;
	            }
	            if (( isset($data['_builtin']) && $data['_builtin'] )) {
	                continue;
	            }
	            $this->aione_custom_types_register( $post_type, $data );
	        }
	    }

	    // rearrange menu items
	    add_filter( 'custom_menu_order' , '__return_true');
	    add_filter( 'menu_order', array( $this, 'aione_cpt_menu_order' ) );
	    // rearrange menu items - end

	}

	function aione_cpt_menu_order( $menu ) {
		$custom_types = get_option( 'aione_custom_post_types', array() );

		if ( !empty( $custom_types ) ) {
			foreach( $custom_types as $post_type => $data ) {
				if( empty( $data )
	                || !isset( $data['menu_position'] )
					|| strpos( $data['menu_position'],'--aione-cpt-add-menu-after--' ) === false )
					continue;

				// at this point we have not only an integer as menu position
				$target_url = explode( '--aione-cpt-add-menu-after--', $data['menu_position'] );

				if( !isset( $target_url[1] ) || empty( $target_url[1] ) )
					continue;

	            $target_url = $target_url[1];
	            //echo "target_url = ".$target_url."<br/>";
	            // current url
	            switch( $data['slug'] ) {
	                case 'post':
	                    $current_url = 'edit.php';
	                    break;
	                case 'attachment':
	                    $current_url = 'upload.php';
	                    break;
	                default:
	                    $current_url = 'edit.php?post_type=' . $data['slug'];
	                    break;
	            }
	            //echo "current_url = ".$current_url."<br/>";
	            //echo "<pre>";print_r($menu);echo "</pre>";
	            $this->aione_cpt_menu_order_item_sort( $menu, $current_url, $target_url );
	            // echo "<pre>";print_r($menu);echo "</pre>";
	            // store already reordered items
	            $reordered[$target_url][] = array(
	                'current_url' => $current_url,
	                'menu_position' => $target_url
	            );

	            // sort previous sorted items which depend on current again
	            if( isset( $reordered[$current_url] ) ) {
	                foreach( $reordered[$current_url] as $post_type ) {
	                    $this->aione_cpt_menu_order_item_sort( $menu, $post_type['current_url'], $post_type['menu_position'] );
	                }

	                unset( $reordered[$current_url] );
	            }
			}
		}

		return $menu;
	}

	function aione_cpt_menu_order_item_sort( &$menu, $current_url, $target_url ) {

	    // current index
	    $current_index = array_search( $current_url, $menu );
	    //echo "<pre>";print_r($current_index);echo "</pre>";

	    // remove all items of $menu which are not matching selected menu
	    $menu_filtered = array_keys( $menu, $target_url );
	    //echo "<pre>";print_r($menu_filtered);echo "</pre>";

	    // use last match for resorting
	    // https://onthegosystems.myjetbrains.com/youtrack/issue/types-591
	    $add_menu_after_index = array_pop( $menu_filtered );
	    //echo "<pre>";print_r($add_menu_after_index);echo "</pre>";

	    // if both found resort menu
	    if( $current_index && $add_menu_after_index )
	        $this->aione_custom_types_menu_order_move( $menu, $current_index, $add_menu_after_index );
	    return $menu;
	}

	function aione_custom_types_menu_order_move( &$menu, $item_move, $item_target ) {

	    // if item move comes after target we have to select the next element,
	    // otherwise the $item_move would be added before the target.
	    if( $item_move > $item_target )
	        $item_target++;

	    // if $item_target is the last menu item, place $item_move to the end of the array
	    if( !isset( $menu[$item_target]) ) {
	        $tmp_menu_item = $menu[$item_move];
	        unset( $menu[$item_move] );
	        $menu[] = $tmp_menu_item;

	    // $item_target is not the last menu, place $item_move after it
	    } else {
	        $cut_moving_element = array_splice( $menu, $item_move, 1 );
	        array_splice( $menu, $item_target, 0, $cut_moving_element );
	    }

	}


	function aione_custom_types_register( $post_type, $data ) {
		
		//echo "<pre>";print_r($data);echo "</pre>";
	    $default = $this->aione_custom_types_default();
	    //echo "<pre>";print_r($default);echo "</pre>";
	    // Set labels
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
	    if ( empty( $data['menu_position'] ) ) {
	        unset( $data['menu_position'] );
	    } else {
	        $default['menu_position'] = intval( $data['menu_position'] );
	    }
	    
	    if (!empty( $data['icon'] ) ) {
	        $default['menu_icon'] = sprintf( 'dashicons-%s', $data['icon'] );
	    }
	    $default['taxonomies'] = !empty( $data['taxonomies'] ) && is_array( $data['taxonomies'] ) ? array_keys( $data['taxonomies'] ) : array();

	    
	    $default['has_archive'] = (empty( $data['has_archive'] )) ? false : true;
	    $default['show_ui'] = (empty( $data['show_ui'] )) ? false : true;
	    $default['show_in_menu'] = (empty( $data['show_in_menu'] )) ? false : true;
	    $default['show_in_menu_page'] = (empty( $data['show_in_menu_page'] )) ? false : true;
	    $default['publicly_queryable'] = (empty( $data['publicly_queryable'] )) ? false : true;
	    $default['exclude_from_search'] = (empty( $data['exclude_from_search'] )) ? false : true;
	    $default['hierarchical'] = (empty( $data['hierarchical'] )) ? false : true;
	    $default['query_var_enabled'] = (empty( $data['query_var_enabled'] )) ? false : true;
	    $default['query_var'] = !empty( $data['query_var'] );
	    $default['can_export'] = (empty( $data['can_export'] )) ? false : true;
	    $default['show_rest'] = (empty( $data['show_rest'] )) ? false : true;
	    $default['rest_base'] = !empty( $data['rest_base'] );
	    $default['show_in_nav_menus'] = (empty( $data['show_in_nav_menus'] )) ? false : true;
	    $default['rest_base'] = !empty( $data['register_meta_box_cb'] );
	    $default['permalink_epmask'] = !empty( $data['permalink_epmask'] );

	    $support_array = array_keys($data['supports']);

	    $default['supports'] = $support_array;

	    //echo "<pre>";print_r($default);echo "</pre>";

	    register_post_type( $post_type,  $default  );
	   

	}

	



	function aione_custom_types_default() {
	    return array(
	    	'label' => '',
	        'labels' => array(
	            'name' => '',
	            'singular_name' => '',
	            'add_new' => 'Add New',
	            'add_new_item' => 'Add New %s',
	            'edit_item' => 'Edit %s',
	            'new_item' => 'New %s',
	            'view_item' => 'View %s',
	            'search_items' => 'Search %s',
	            'not_found' => 'No %s found',
	            'not_found_in_trash' => 'No %s found in Trash',
	            'parent_item_colon' => 'Parent %s',
	            'all_items' => 'All items',
	            'menu_name' => 'Enter title here',
	        ),
	        'slug' => '',
	        'description' => '',
	        'public' => true,
	        //'capabilities' => false,
	        'capability_type' => 'post',
	        'menu_position' => null,
	        'menu_icon' => null,
	        'taxonomies' => array(
	            'category' => false,
	            'post_tag' => false,
	        ),
	        'supports' => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'trackbacks', 'revisions', 'custom-fields', 'page-attributes', 'post-formats', ),
	        'rewrite' => array(
	            'enabled' => true,
	            'slug' => '',
	            'with_front' => true,
	            'feeds' => true,
	            'pages' => true,
	        ),
	        'has_archive' => true,
	        'show_ui' => true,
	        'show_in_menu' => true,
	        'show_in_menu_page' => '',
	        'publicly_queryable' => true,
	        'exclude_from_search' => false,
	        'hierarchical' => false,
	        'query_var_enabled' => true,
	        'query_var' => '',
	        'can_export' => true,
	        'show_rest' => false,
	        'rest_base' => '',
	        'show_in_nav_menus' => true,
	        'register_meta_box_cb' => '',
	        'permalink_epmask' => 'EP_PERMALINK',
	    );
	}

	
	public function get_dashboard_icons_list(){

        $icons = array(
            'admin-appearance' => __('appearance', 'wcpf'),
            'admin-collapse' => __('collapse', 'wcpf'),
            'admin-comments' => __('comments', 'wcpf'),
            'admin-generic' => __('generic', 'wcpf'),
            'admin-home' => __('home', 'wcpf'),
            'admin-links' => __('links', 'wcpf'),
            'admin-media' => __('media', 'wcpf'),
            'admin-network' => __('network', 'wcpf'),
            'admin-page' => __('page', 'wcpf'),
            'admin-plugins' => __('plugins', 'wcpf'),
            'admin-post' => __('post', 'wcpf'),
            'admin-settings' => __('settings', 'wcpf'),
            'admin-site' => __('site', 'wcpf'),
            'admin-tools' => __('tools', 'wcpf'),
            'admin-users' => __('users', 'wcpf'),
            'album' => __('album', 'wcpf'),
            'align-center' => __('align center', 'wcpf'),
            'align-left' => __('align left', 'wcpf'),
            'align-none' => __('align none', 'wcpf'),
            'align-right' => __('align right', 'wcpf'),
            'analytics' => __('analytics', 'wcpf'),
            'archive' => __('archive', 'wcpf'),
            'arrow-down-alt2' => __('down alt2', 'wcpf'),
            'arrow-down-alt' => __('down alt', 'wcpf'),
            'arrow-down' => __('down', 'wcpf'),
            'arrow-left-alt2' => __('left alt2', 'wcpf'),
            'arrow-left-alt' => __('left alt', 'wcpf'),
            'arrow-left' => __('left', 'wcpf'),
            'arrow-right-alt2' => __('right alt2', 'wcpf'),
            'arrow-right-alt' => __('right alt', 'wcpf'),
            'arrow-right' => __('right', 'wcpf'),
            'arrow-up-alt2' => __('up alt2', 'wcpf'),
            'arrow-up-alt' => __('up alt', 'wcpf'),
            'arrow-up' => __('up', 'wcpf'),
            'art' => __('art', 'wcpf'),
            'awards' => __('awards', 'wcpf'),
            'backup' => __('backup', 'wcpf'),
            'book-alt' => __('book alt', 'wcpf'),
            'book' => __('book', 'wcpf'),
            'building' => __('building', 'wcpf'),
            'businessman' => __('businessman', 'wcpf'),
            'calendar-alt' => __('calendar alt', 'wcpf'),
            'calendar' => __('calendar', 'wcpf'),
            'camera' => __('camera', 'wcpf'),
            'carrot' => __('carrot', 'wcpf'),
            'cart' => __('cart', 'wcpf'),
            'category' => __('category', 'wcpf'),
            'chart-area' => __('chart area', 'wcpf'),
            'chart-bar' => __('chart bar', 'wcpf'),
            'chart-line' => __('chart line', 'wcpf'),
            'chart-pie' => __('chart pie', 'wcpf'),
            'clipboard' => __('clipboard', 'wcpf'),
            'clock' => __('clock', 'wcpf'),
            'cloud' => __('cloud', 'wcpf'),
            'controls-back' => __('back', 'wcpf'),
            'controls-forward' => __('forward', 'wcpf'),
            'controls-pause' => __('pause', 'wcpf'),
            'controls-play' => __('play', 'wcpf'),
            'controls-repeat' => __('repeat', 'wcpf'),
            'controls-skipback' => __('skip back', 'wcpf'),
            'controls-skipforward' => __('skip forward', 'wcpf'),
            'controls-volumeoff' => __('volume off', 'wcpf'),
            'controls-volumeon' => __('volume on', 'wcpf'),
            'dashboard' => __('dashboard', 'wcpf'),
            'desktop' => __('desktop', 'wcpf'),
            'dismiss' => __('dismiss', 'wcpf'),
            'download' => __('download', 'wcpf'),
            'editor-aligncenter' => __('align center', 'wcpf'),
            'editor-alignleft' => __('align left', 'wcpf'),
            'editor-alignright' => __('align right', 'wcpf'),
            'editor-bold' => __('bold', 'wcpf'),
            'editor-break' => __('break', 'wcpf'),
            'editor-code' => __('code', 'wcpf'),
            'editor-contract' => __('contract', 'wcpf'),
            'editor-customchar' => __('custom char', 'wcpf'),
            'editor-distractionfree' => __('distraction free', 'wcpf'),
            'editor-expand' => __('expand', 'wcpf'),
            'editor-help' => __('help', 'wcpf'),
            'editor-indent' => __('indent', 'wcpf'),
            'editor-insertmore' => __('insert more', 'wcpf'),
            'editor-italic' => __('italic', 'wcpf'),
            'editor-justify' => __('justify', 'wcpf'),
            'editor-kitchensink' => __('kitchen sink', 'wcpf'),
            'editor-ol' => __('ol', 'wcpf'),
            'editor-outdent' => __('outdent', 'wcpf'),
            'editor-paragraph' => __('paragraph', 'wcpf'),
            'editor-paste-text' => __('paste text', 'wcpf'),
            'editor-paste-word' => __('paste word', 'wcpf'),
            'editor-quote' => __('quote', 'wcpf'),
            'editor-removeformatting' => __('remove formatting', 'wcpf'),
            'editor-rtl' => __('rtl', 'wcpf'),
            'editor-spellcheck' => __('spellcheck', 'wcpf'),
            'editor-strikethrough' => __('strike through', 'wcpf'),
            'editor-textcolor' => __('text color', 'wcpf'),
            'editor-ul' => __('ul', 'wcpf'),
            'editor-underline' => __('underline', 'wcpf'),
            'editor-unlink' => __('unlink', 'wcpf'),
            'editor-video' => __('video', 'wcpf'),
            'edit' => __('edit', 'wcpf'),
            'email-alt' => __('email alt', 'wcpf'),
            'email' => __('email', 'wcpf'),
            'excerpt-view' => __('excerpt view', 'wcpf'),

	        // because https://core.trac.wordpress.org/ticket/30832
	        // but we don't have to offer it at all, actually it looks deprecated
            // 'exerpt-view' => __('excerpt view', 'wcpf'),

            'external' => __('external', 'wcpf'),
            'facebook-alt' => __('facebook alt', 'wcpf'),
            'facebook' => __('facebook', 'wcpf'),
            'feedback' => __('feedback', 'wcpf'),
            'flag' => __('flag', 'wcpf'),
            'format-aside' => __('aside', 'wcpf'),
            'format-audio' => __('audio', 'wcpf'),
            'format-chat' => __('chat', 'wcpf'),
            'format-gallery' => __('gallery', 'wcpf'),
            'format-image' => __('image', 'wcpf'),
            'format-links' => __('links', 'wcpf'),
            'format-quote' => __('quote', 'wcpf'),
            'format-standard' => __('standard', 'wcpf'),
            'format-status' => __('status', 'wcpf'),
            'format-video' => __('video', 'wcpf'),
            'forms' => __('forms', 'wcpf'),
            'googleplus' => __('google plus', 'wcpf'),
            'grid-view' => __('grid view', 'wcpf'),
            'groups' => __('groups', 'wcpf'),
            'hammer' => __('hammer', 'wcpf'),
            'heart' => __('heart', 'wcpf'),
            'id-alt' => __('id alt', 'wcpf'),
            'id' => __('id', 'wcpf'),
            'images-alt2' => __('images alt2', 'wcpf'),
            'images-alt' => __('images alt', 'wcpf'),
            'image-crop' => __('image crop', 'wcpf'),
            'image-flip-horizontal' => __('image flip horizontal', 'wcpf'),
            'image-flip-vertical' => __('image flip vertical', 'wcpf'),
            'image-rotate-left' => __('image rotate left', 'wcpf'),
            'image-rotate-right' => __('image rotate right', 'wcpf'),
            'index-card' => __('index card', 'wcpf'),
            'info' => __('info', 'wcpf'),
            'leftright' => __('left right', 'wcpf'),
            'lightbulb' => __('light bulb', 'wcpf'),
            'list-view' => __('list view', 'wcpf'),
            'location-alt' => __('location alt', 'wcpf'),
            'location' => __('location', 'wcpf'),
            'lock' => __('lock', 'wcpf'),
            'marker' => __('marker', 'wcpf'),
            'media-archive' => __('media archive', 'wcpf'),
            'media-audio' => __('media audio', 'wcpf'),
            'media-code' => __('media code', 'wcpf'),
            'media-default' => __('media default', 'wcpf'),
            'media-document' => __('media document', 'wcpf'),
            'media-interactive' => __('media interactive', 'wcpf'),
            'media-spreadsheet' => __('media spreadsheet', 'wcpf'),
            'media-text' => __('media text', 'wcpf'),
            'media-video' => __('media video', 'wcpf'),
            'megaphone' => __('megaphone', 'wcpf'),
            'menu' => __('menu', 'wcpf'),
            'microphone' => __('microphone', 'wcpf'),
            'migrate' => __('migrate', 'wcpf'),
            'minus' => __('minus', 'wcpf'),
            'money' => __('money', 'wcpf'),
            'nametag' => __('name tag', 'wcpf'),
            'networking' => __('networking', 'wcpf'),
            'no-alt' => __('no alt', 'wcpf'),
            'no' => __('no', 'wcpf'),
            'palmtree' => __('palm tree', 'wcpf'),
            'performance' => __('performance', 'wcpf'),
            'phone' => __('phone', 'wcpf'),
            'playlist-audio' => __('playlist audio', 'wcpf'),
            'playlist-video' => __('playlist video', 'wcpf'),
            'plus-alt' => __('plus alt', 'wcpf'),
            'plus' => __('plus', 'wcpf'),
            'portfolio' => __('portfolio', 'wcpf'),
            'post-status' => __('post status', 'wcpf'),
            'post-trash' => __('post trash', 'wcpf'),
            'pressthis' => __('press this', 'wcpf'),
            'products' => __('products', 'wcpf'),
            'randomize' => __('randomize', 'wcpf'),
            'redo' => __('redo', 'wcpf'),
            'rss' => __('rss', 'wcpf'),
            'schedule' => __('schedule', 'wcpf'),
            'screenoptions' => __('screen options', 'wcpf'),
            'search' => __('search', 'wcpf'),
            'share1' => __('share1', 'wcpf'),
            'share-alt2' => __('share alt2', 'wcpf'),
            'share-alt' => __('share alt', 'wcpf'),
            'share' => __('share', 'wcpf'),
            'shield-alt' => __('shield alt', 'wcpf'),
            'shield' => __('shield', 'wcpf'),
            'slides' => __('slides', 'wcpf'),
            'smartphone' => __('smartphone', 'wcpf'),
            'smiley' => __('smiley', 'wcpf'),
            'sort' => __('sort', 'wcpf'),
            'sos' => __('sos', 'wcpf'),
            'star-empty' => __('star empty', 'wcpf'),
            'star-filled' => __('star filled', 'wcpf'),
            'star-half' => __('star half', 'wcpf'),
            'store' => __('store', 'wcpf'),
            'tablet' => __('tablet', 'wcpf'),
            'tagcloud' => __('tag cloud', 'wcpf'),
            'tag' => __('tag', 'wcpf'),
            'testimonial' => __('testimonial', 'wcpf'),
            'text' => __('text', 'wcpf'),
            'tickets-alt' => __('tickets alt', 'wcpf'),
            'tickets' => __('tickets', 'wcpf'),
            'translation' => __('translation', 'wcpf'),
            'trash' => __('trash', 'wcpf'),
            'twitter' => __('twitter', 'wcpf'),
            'undo' => __('undo', 'wcpf'),
            'universal-access-alt' => __('universal access alt', 'wcpf'),
            'universal-access' => __('universal access', 'wcpf'),
            'update' => __('update', 'wcpf'),
            'upload' => __('upload', 'wcpf'),
            'vault' => __('vault', 'wcpf'),
            'video-alt2' => __('video alt2', 'wcpf'),
            'video-alt3' => __('video alt3', 'wcpf'),
            'video-alt' => __('video alt', 'wcpf'),
            'visibility' => __('visibility', 'wcpf'),
            'welcome-add-page' => __('add page', 'wcpf'),
            'welcome-comments' => __('comments', 'wcpf'),
            'welcome-edit-page' => __('edit page', 'wcpf'),
            'welcome-learn-more' => __('learn more', 'wcpf'),
            'welcome-view-site' => __('view site', 'wcpf'),
            'welcome-widgets-menus' => __('widgets menus', 'wcpf'),
            'welcome-write-blog' => __('write blog', 'wcpf'),
            'wordpress-alt' => __('wordpress alt', 'wcpf'),
            'wordpress' => __('wordpress', 'wcpf'),
            'yes' => __('yes', 'wcpf'),
        );
        printf(
            '<p><input type="text" class="js-wpcf-search large-text" placeholder="%s" /</p>',
            esc_attr__('Search', 'wpcf')
        );
        $current = isset($_REQUEST['slug']) && is_string($_REQUEST['slug'])? $_REQUEST['slug']:'';
        echo '<ul>';
        foreach ( $icons as $slug => $title ) {
            printf(
                '<li data-wpcf-icon="%s" class="%s"><a href="#" data-wpcf-icon="%s"><span class="dashicons-before dashicons-%s">%s</span></a></li>',
                esc_attr($slug),
                $current == $slug? 'selected':'',
                esc_attr($slug),
                esc_attr($slug),
                $title
            );
        }
        echo '</ul>';
        die;
    }

    function aione_cpt_delete_post_type(){
    	$post_type = $this->wpcf_ajax_helper_get_post_type();
        if ( empty($post_type) ) {
            $this->wpcf_ajax_helper_print_error_and_die();
        }
        $post_types = get_option('aione_custom_post_types', array());
        unset($post_types[$post_type]);
        update_option('aione_custom_post_types', $post_types);
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
	function wpcf_ajax_helper_get_post_type()
	{
	    if (!isset($_REQUEST['wpcf-post-type']) || empty($_REQUEST['wpcf-post-type'])) {
	        return false;
	    }
	    $custom_types = get_option('aione_custom_post_types', array());
	    if (
	        isset($custom_types[$_REQUEST['wpcf-post-type']])
	        && isset($custom_types[$_REQUEST['wpcf-post-type']]['slug'])
	    ) {
	        return $custom_types[$_REQUEST['wpcf-post-type']]['slug'];
	    }
	    return false;
	}

	// Register Custom Post Type
	/*function register_custom_post_type() {
		$registered_post_types = get_option('_aione_cpt_meta');
		if($registered_post_types != false || $registered_post_types!= ""){
			if (is_array($registered_post_types) || is_object($registered_post_types)){
				foreach ($registered_post_types as $registered_post_type_key => $registered_post_type) {
					if($registered_post_type['menu_position']){
						$target_position = explode( '--aione-cpt-add-menu-after--', $registered_post_type['menu_position'] );
						$target_position = $target_position[1];
					} else {
						$target_position = null;
					}
					if( !isset( $registered_post_type['menu_position'] ) || strpos( $registered_post_type['menu_position'],'--aione-cpt-add-menu-after--' ) === false )
						continue;

					$target_url = explode( '--wpcf-add-menu-after--', $data['menu_position'] );
					if( !isset( $target_url[1] ) || empty( $target_url[1] ) )
						continue;
					$target_url = $target_url[1];

		            // current url
		            switch( $registered_post_type['slug'] ) {
		                case 'post':
		                    $current_url = 'edit.php';
		                    break;
		                case 'attachment':
		                    $current_url = 'upload.php';
		                    break;
		                default:
		                    $current_url = 'edit.php?post_type=' . $data['slug'];
		                    break;
		            }

		            $this->aione_cpt_menu_order_item_sort( $menu, $current_url, $target_url );

					

					add_action( 'init', function() use ($registered_post_type) {
						$labels = array(
							'name'                  => _x( $registered_post_type['labels']['name'], 'Post Type General Name', 'aione_cpt' ),
							'singular_name'         => _x( $registered_post_type['labels']['singular_name'], 'Post Type Singular Name', 'aione_cpt' ),
							'menu_name'             => __( $registered_post_type['labels']['name'], 'aione_cpt' ),
							'name_admin_bar'        => __( $registered_post_type['labels']['name'], 'aione_cpt' ),
							'all_items'             => __( $registered_post_type['labels']['name'], 'aione_cpt' ),
							'add_new_item'          => __( $registered_post_type['labels']['add_new_item'], 'aione_cpt' ),
							'add_new'               => __( $registered_post_type['labels']['add_new'], 'aione_cpt' ),
							'new_item'              => __( $registered_post_type['labels']['new_item'], 'aione_cpt' ),
							'edit_item'             => __( $registered_post_type['labels']['edit_item'], 'aione_cpt' ),
							'view_item'             => __( $registered_post_type['labels']['view_item'], 'aione_cpt' ),
							'search_items'          => __( $registered_post_type['labels']['search_items'], 'aione_cpt' ),
							'not_found'             => __( $registered_post_type['labels']['not_found'], 'aione_cpt' ),
							'not_found_in_trash'    => __( $registered_post_type['labels']['not_found_in_trash'], 'aione_cpt' ),
						);
						$rewrite = array(
							'slug'                  => $registered_post_type['slug'],
							'with_front'            => $registered_post_type['rewrite']['with_front'],
							'pages'                 => $registered_post_type['rewrite']['feeds'],
							'feeds'                 => $registered_post_type['rewrite']['pages'],
						);
						$capabilities = array(
							'edit_post'             => 'edit_post',
							'read_post'             => 'read_post',
							'delete_post'           => 'delete_post',
							'edit_posts'            => 'edit_posts',
							'edit_others_posts'     => 'edit_others_posts',
							'publish_posts'         => 'publish_posts',
							'read_private_posts'    => 'read_private_posts',
						);
						$args = array(
							'label'                 => $registered_post_type['labels']['name'],
							'description'           => $registered_post_type['description'],
							'labels'                => $labels,
							'supports'              => array_keys($registered_post_type['supports']),
							'taxonomies'            => array( 'category', 'post_tag' ),
							'hierarchical'          => $registered_post_type['hierarchical'],
							'public'                => true,
							'show_ui'               => $registered_post_type['show_ui'],
							'show_in_menu'          => $registered_post_type['show_in_menu'],
							'menu_position'         => $registered_post_type['menu_position'],
							'menu_icon'             => $registered_post_type['icon'],
							'show_in_admin_bar'     => true,
							'show_in_nav_menus'     => $registered_post_type['show_in_nav_menus'],
							'can_export'            => $registered_post_type['can_export'],
							'has_archive'           => $registered_post_type['has_archive'],
							'exclude_from_search'   => $registered_post_type['exclude_from_search'],
							'publicly_queryable'    => $registered_post_type['publicly_queryable'],
							'rewrite'               => $rewrite,
							'capabilities'          => $capabilities,
							'show_in_rest'          => $registered_post_type['show_in_rest'],
						);
					
						register_post_type( $registered_post_type['slug'], $args );

					}, 0 ); 

				}  //foreach $registered_post_types
			}  //if Array or Object $registered_post_types
		} //if not empty $registered_post_types
	}*/
	
}