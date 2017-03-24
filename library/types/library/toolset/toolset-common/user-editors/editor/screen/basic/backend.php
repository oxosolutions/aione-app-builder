<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Basic_Backend
	extends Toolset_User_Editors_Editor_Screen_Abstract {
		
		public function __construct() {		
		
		add_action( 'init',												array( $this, 'register_assets' ), 50 );
		add_action( 'admin_enqueue_scripts',							array( $this, 'admin_enqueue_assets' ), 50 );
		
		add_filter( 'toolset_filter_toolset_registered_user_editors',	array( $this, 'register_user_editor' ) );
		add_filter( 'wpv_filter_wpv_layout_template_extra_attributes',	array( $this, 'layout_template_attribute' ), 10, 3 );
		
		add_action( 'wp_ajax_toolset_set_layout_template_user_editor',	array( $this, 'set_layout_template_user_editor' ) );
	}

	public function isActive() {
		$this->action();
		return true;
	}

	private function action() {
		add_action( 'admin_enqueue_scripts', array( $this, 'action_assets' ) );
		$this->medium->setHtmlEditorBackend( array( $this, 'html_output' ) );
	}

	public function html_output() {

		if( ! isset( $_GET['ct_id'] ) )
			return 'No valid content template id';

		ob_start();
			include_once( dirname( __FILE__ ) . '/backend.phtml' );
			$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}
	
	public function register_assets() {
		
		$toolset_assets_manager = Toolset_Assets_Manager::getInstance();
		
		$toolset_assets_manager->register_script(
			'toolset-user-editors-basic-script',
			TOOLSET_COMMON_URL . '/user-editors/editor/screen/basic/backend.js',
			array( 'jquery' ),
			TOOLSET_COMMON_VERSION,
			true
		);
		
		$toolset_assets_manager->register_script(
			'toolset-user-editors-basic-layout-template-script',
			TOOLSET_COMMON_URL . '/user-editors/editor/screen/basic/backend_layout_template.js',
			array( 'jquery', 'views-layout-template-js', 'underscore' ),
			TOOLSET_COMMON_VERSION,
			true
		);
		
		$basic_layout_template_i18n = array(
            'template_editor_url'	=> admin_url( 'admin.php?page=ct-editor' ),
			'template_overlay'		=> array(
										'title'		=> __( 'Saving...', 'wpv-views' )
									),
			'user_editors'			=> apply_filters( 'toolset_filter_toolset_registered_user_editors', array() ),
			'wpnonce'				=> wp_create_nonce( 'toolset_layout_template_user_editor_nonce' )
		);
		$toolset_assets_manager->localize_script( 
			'toolset-user-editors-basic-layout-template-script', 
			'toolset_user_editors_basic_layout_template_i18n', 
			$basic_layout_template_i18n 
		);
		
	}
	
	public function admin_enqueue_assets() {
		$page = toolset_getget( 'page' );
		if ( 
			'views-editor' == $page 
			|| 'view-archives-editor' == $page 
		) {
			
			do_action( 'toolset_enqueue_scripts', array( 'toolset-user-editors-basic-layout-template-script' ) );
			
		}
	}
	
	public function action_assets() {
		
		do_action( 'toolset_enqueue_scripts',	array( 'toolset-user-editors-basic-script' ) );
		
	}
	
	public function register_user_editor( $editors ) {
		$editors[ $this->editor->getId() ] = $this->editor->getName();
		return $editors;
	}
	
	/**
	* Set the builder used by a Content Template, if any.
	*
	* On a Content Template used inside a View or WPA loop output, we set which builder it is using
	* so we can link to the CT edit page with the right builder instantiated.
	*
	* @since 2.3.0
	*/
	
	public function layout_template_attribute( $attributes, $content_template, $view_id ) {
		$content_template_has_basic = ( in_array( get_post_meta( $content_template->ID, '_toolset_user_editors_editor_choice', true ), array( '', 'basic' ) ) );
		if ( $content_template_has_basic ) {
			$attributes['builder'] = $this->editor->getId();
		}
		return $attributes;
	}
	
	public function set_layout_template_user_editor() {
		
		if ( ! current_user_can( 'manage_options' ) ) {
			$data = array(
				'type' => 'capability',
				'message' => __( 'You do not have permissions for that.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if ( 
			! isset( $_POST['wpnonce'] )
			|| ! wp_verify_nonce( $_POST['wpnonce'], 'toolset_layout_template_user_editor_nonce' )
		) {
			$data = array(
				'type' => 'nonce',
				'message' => __( 'Your security credentials have expired. Please reload the page to get new ones.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		if (
			! isset( $_POST['ct_id'] )
			|| ! is_numeric( $_POST['ct_id'] )
			|| intval( $_POST['ct_id'] ) < 1 
		) {
			$data = array(
				'type' => 'id',
				'message' => __( 'Wrong or missing ID.', 'wpv-views' )
			);
			wp_send_json_error( $data );
		}
		
		$ct_id = (int) $_POST['ct_id'];
		$editor = isset( $_POST['editor'] ) ? sanitize_text_field( $_POST['editor'] ) : 'basic';
		update_post_meta( $ct_id, '_toolset_user_editors_editor_choice', $editor );
		
		wp_send_json_success();
		
	}
}