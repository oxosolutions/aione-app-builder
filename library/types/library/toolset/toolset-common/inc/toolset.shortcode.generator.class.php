<?php

/**
* Toolset_Shortcode_Generator
*
* Generic class to manage the Toolset shortcodes admin bar entry.
*
* Use the filter toolset_shortcode_generator_register_item before admin_init:99
* Register your items as follows:
* 		add_filter( 'toolset_shortcode_generator_register_item', 'register_my_shortcodes_in_the_shortcode_generator' );
* 		function register_my_shortcodes_in_the_shortcode_generator( $registered_sections ) {
* 			// Do your logic here to determine whether you need to add your section or not, and check if you need specific assets
* 			// In case you do, register as follows:
* 			$registered_sections['section-id'] = array(
* 				'id'		=> 'section-id',						// The ID of the item
*				'title'		=> __( 'My fields', 'my-textdomain' ),	// The title for the item
*				'href'		=> '#my_anchor',						// The href attribute for the link of the item
*				'parent'	=> 'toolset-shortcodes',				// Set the parent item as the known 'toolset-shortcodes'
*				'meta'		=> 'js-my-classname'					// Cloassname for the li container of the item
* 			);
* 			return $registered_sections;
* 		}
*
* Note that you will have to take care of displaying the dialog after clicking on the item, and deal with what is should do.
*
* @since 1.9
*/

if ( ! class_exists( 'Toolset_Shortcode_Generator' ) ) {
    
    abstract class Toolset_Shortcode_Generator {

	private static $registered_admin_bar_items	= array();
	private static $can_show_admin_bar_item		= false;
	private static $target_dialog_added			= false;
	
	function __construct() {
		
		add_action( 'admin_init',		array( $this, 'register_shortcodes_admin_bar_items' ), 99 );
	    add_action( 'admin_bar_menu',	array( $this, 'display_shortcodes_admin_bar_items' ), 99 );
		add_action( 'admin_footer',		array( $this, 'display_shortcodes_target_dialog' ) );
	}
	
	public function register_shortcodes_admin_bar_items() {
		
		// Only register sections if the Admin Bar item is to be shown.
		$toolset_settings = Toolset_Settings::get_instance();
		$toolset_shortcodes_generator = ( isset( $toolset_settings['shortcodes_generator'] ) && in_array( $toolset_settings['shortcodes_generator'], array( 'unset', 'disable', 'editor', 'always' ) ) ) ? $toolset_settings['shortcodes_generator'] : 'unset';
		if ( $toolset_shortcodes_generator == 'unset' ) {
			$toolset_shortcodes_generator = apply_filters( 'toolset_filter_force_unset_shortcode_generator_option', $toolset_shortcodes_generator );
		}
		$register_section = false;
		switch ( $toolset_shortcodes_generator ) {
			case 'always':
				$register_section = true;
				break;
			case 'editor':
				$register_section = $this->is_admin_editor_page();
				break;
		}
		if ( ! $register_section ) {
			return;
		}
		
		// Now that we know that it will be shown, collect the registered items.
		$registered_items = self::$registered_admin_bar_items;
		$registered_items = apply_filters( 'toolset_shortcode_generator_register_item', $registered_items );
		self::$registered_admin_bar_items = $registered_items;
		
	}
	
	/*
	 * Add admin bar main item for shortcodes
	 */
	public function display_shortcodes_admin_bar_items( $wp_admin_bar ) {
		if ( ! is_admin() ) {
			return;
		}
		$registered_items = self::$registered_admin_bar_items;
		if ( empty( $registered_items ) ) {
			return;
		}
		self::$can_show_admin_bar_item = true;
	    $this->create_admin_bar_item( $wp_admin_bar, 'toolset-shortcodes', __( 'Toolset shortcodes', 'wpv-views' ), '#', false );
		foreach ( $registered_items as $item_key => $item_args ) {
			$this->create_admin_bar_item( $wp_admin_bar, $item_args['id'], $item_args['title'], $item_args['href'], $item_args['parent'], $item_args['meta'] );
		}
	}
	
	/*
	 * General function for creating admin bar menu items
	 * 
	 */
	public static function create_admin_bar_item( $wp_admin_bar, $id, $name, $href, $parent, $classes = null ) {
	    $args = array(
			'id'		=> $id,
			'title'		=> $name,
			'href'		=> $href,
			'parent'	=> $parent,
			'meta' 		=> array( 'class' => $id . '-shortcode-menu ' . $classes )
	    );
	    $wp_admin_bar->add_node( $args );
	}
	
	/**
	 * is_admin_editor_page
	 *
	 * Helper method to check whether we are on an admin editor page. 
	 * This covers edit pages for posts, terms and users, 
	 * as well as Toolset object edit pages.
	 *
	 * @since 2.3.0
	 */
	
	public function is_admin_editor_page() {
		if ( ! is_admin() ) {
			return false;
		}
		global $pagenow, $wp_version;
		$allowed_pagenow_array = array( 'post.php', 'post-new.php', 'term.php', 'user-new.php', 'user-edit.php', 'profile.php' );				
		$allowed_page_array = array( 'views-editor', 'ct-editor', 'view-archives-editor', 'dd_layouts_edit' );
		// @todo maybe add a filter here for future Toolset admin pages...
		if (
			in_array( $pagenow, $allowed_pagenow_array ) 
			|| (
				$pagenow == 'admin.php' 
				&& isset( $_GET['page'] ) 
				&& in_array( $_GET['page'], $allowed_page_array )
			)
			|| (
				// In WordPress < 4.5, the edit tag admin page is edit-tags.php?action=edit&taxonomy=category&tag_ID=X
				version_compare( $wp_version, '4.5', '<' ) 
				&& $pagenow == 'edit-tags.php' 
				&& isset( $_GET['action'] ) 
				&& $_GET['action'] == 'edit'
			)
		) {
			return true;
		}
		return false;
	}
	
	/**
	 * is_frontend_editor_page
	 *
	 * Helper method to check whether we are on an frontend editor page. 
	 * This should cover as many frontend editors as possible.
	 *
	 * @since 2.3.0
	 */
	
	public function is_frontend_editor_page() {
		if ( is_admin() ) {
			return false;
		}
		if (
			// Layouts frontend editor
			isset( $_GET['toolset_editor'] )
			// Beaver Builder frontend editor
			|| isset( $_GET['fl_builder'] ) 
			// CRED frontend editor pages, when discoverable
		) {
			return true;
		}
		return false;
	}
	
	/*
	 * Dialog Template HTML code
	 */
	public function display_shortcodes_target_dialog() {
	    if ( 
			self::$can_show_admin_bar_item
			&& self::$target_dialog_added === false 
		) {
			?>
			<div class="toolset-dialog-container" style="display:none">
				<div id="toolset-shortcode-generator-target-dialog" class="toolset-shortcode-gui-dialog-container js-toolset-shortcode-generator-target-dialog">
					<textarea id="toolset-shortcode-generator-target" class="textarea" rows="4" style="width:100%;"></textarea>
				</div>
			</div>
			<?php
			self::$target_dialog_added = true; 
		}

	}
    


    }
    
}