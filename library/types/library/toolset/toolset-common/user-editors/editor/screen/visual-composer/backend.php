<?php

if( ! class_exists( 'Toolset_User_Editors_Editor_Screen_Abstract', false ) )
	require_once( TOOLSET_COMMON_PATH . '/user-editors/editor/screen/abstract.php' );

class Toolset_User_Editors_Editor_Screen_Visual_Composer_Backend
	extends Toolset_User_Editors_Editor_Screen_Abstract {

	private $post;
	public $editor;
	
	public function __construct() {		
		add_action( 'init',												array( $this, 'register_assets' ), 50 );
		add_action( 'admin_enqueue_scripts',							array( $this, 'admin_enqueue_assets' ), 50 );
		
		add_filter( 'toolset_filter_toolset_registered_user_editors',	array( $this, 'register_user_editor' ) );
		add_filter( 'wpv_filter_wpv_layout_template_extra_attributes',	array( $this, 'layout_template_attribute' ), 10, 3 );
		add_action( 'wpv_action_wpv_ct_inline_user_editor_buttons',		array( $this, 'register_inline_editor_action_buttons' ) );
		
		// Post edit page integration
		//add_action( 'edit_form_after_title',				array( $this, 'preventNested' ) );
	}

	public function isActive() {
		if( ! $this->setMediumAsPost() )
			return false;

		// check for functions used
		if(
			! function_exists( 'vc_user_access' )
			|| ! class_exists( 'Vc_Shortcodes_Manager' )
			|| ! method_exists( 'Vc_Manager', 'backendEditor' )
		)
			return false;

		// don't show VC if user role is not allowed to use the backend editor
		if( ! vc_user_access()->part( 'backend_editor' )->can()->get() ) {
			return false;
		}

		$this->action();
		return true;
	}

	private function action() {
		add_action( 'admin_init', array( $this, 'setup' ) );

		add_action( 'admin_print_scripts', array( &$this->editor, 'enqueueEditorScripts' ) );
		add_action( 'admin_print_scripts', array( $this, 'print_scripts' ) );
		add_action( 'admin_print_scripts', array( Vc_Shortcodes_Manager::getInstance(), 'buildShortcodesAssets' ), 1 );

		$this->medium->setHtmlEditorBackend( array( $this, 'html_output' ) );
	}

	/**
	 * Setup the editor
	 * called on action 'admin_init'
	 */
	public function setup() {
		// Disable Visual Composers Frontend Editor
		vc_disable_frontend();

		// Get backend editor object through VC_Manager (vc di container)
		global $vc_manager;
		$this->editor = $vc_manager->backendEditor();

		// VC_Backend_Editor->render() registers all needed scripts
		// the "real" render came later in $this->html_output();
		$this->editor->render( $this->post->post_type );
	}


	private function setMediumAsPost() {
		$medium_id  = $this->medium->getId();

		if( ! $medium_id )
			return false;

		$medium_post_object = get_post( $medium_id );
		if( $medium_post_object === null )
			return false;

		$this->post = $medium_post_object;

		return true;
	}
	
	public function register_assets() {
		
		$toolset_assets_manager = Toolset_Assets_Manager::getInstance();
		
		// Content Template as inline object assets
		
		$toolset_assets_manager->register_script(
			'toolset-user-editors-vc-layout-template-script',
			TOOLSET_COMMON_URL . '/user-editors/editor/screen/visual-composer/backend_layout_template.js',
			array( 'jquery', 'views-layout-template-js', 'underscore' ),
			TOOLSET_COMMON_VERSION,
			true
		);
		
		$vc_layout_template_i18n = array(
            'template_editor_url'	=> admin_url( 'admin.php?page=ct-editor' ),
			'template_overlay'		=> array(
										'title'		=> sprintf( __( 'This Content Template uses %1$s', 'wpv-views' ), $this->editor->getName() ),
										'text'		=> sprintf( __( 'To modify this Content Template, go to edit it and launch the %1$s.', 'wpv-views' ), $this->editor->getName() ),
										'button'	=> sprintf( __( 'Edit with %1$s', 'wpv-views' ), $this->editor->getName() ),
										'discard'	=> sprintf( __( 'Stop using %1$s for this Content Template', 'wpv-views' ), $this->editor->getName() )
									),
		);
		$toolset_assets_manager->localize_script( 
			'toolset-user-editors-vc-layout-template-script', 
			'toolset_user_editors_vc_layout_template_i18n', 
			$vc_layout_template_i18n 
		);
		
	}
	
	public function admin_enqueue_assets() {
		$page = toolset_getget( 'page' );
		if ( 
			'views-editor' == $page 
			|| 'view-archives-editor' == $page 
		) {
			
			do_action( 'toolset_enqueue_scripts', array( 'toolset-user-editors-vc-layout-template-script' ) );
			
		}
	}

	public function html_output() {
		
		ob_start(); ?>
		<div style="display: none;">
			<input type="hidden" id="post_ID" name="post_ID" value="<?php echo $this->post->ID; ?>">
			<textarea cols="30" rows="10" id="wpv_content" name="wpv_content" data-bind="textInput: postContentAccepted"></textarea>
			<?php wp_editor(  $this->post->post_content, 'content', array( 'media_buttons' => true ) ); ?>
		</div>

		<div id="wpb_visual_composer" style="padding-bottom: 5px; background: #fff;"><?php $this->editor->renderEditor( $this->post ); ?></div>
		<?php
		$script = "<script>
				jQuery( window ).load( function( ) {
					/* no fullscreen, no vc save button */
					jQuery( '#vc_navbar .vc_save-backend, #vc_fullscreen-button' ).remove();

					/* show vc editor */
					vc.app.show();
					vc.app.status = 'shown';
					
					var viewsBasicTextarea 		 = jQuery( '#wpv_content' );
					var wordpressDefaultTextarea = jQuery( '#content' );
					
					/* Visual Composer fires the 'sync' event everytime something is changed */
					/* we use this to enable button 'Save all sections at once' if content has changed */
					vc.shortcodes.on( 'sync', function() {
						if( wordpressDefaultTextarea.val() != viewsBasicTextarea.val() ) {
							viewsBasicTextarea.val( wordpressDefaultTextarea.val() );

							WPViews.ct_edit_screen.vm.postContentAccepted = function(){ return wordpressDefaultTextarea.val() };
							WPViews.ct_edit_screen.vm.propertyChangeByComparator( 'postContent', _.isEqual );
						}
					} );
				} );</script>";
		echo preg_replace('/\v(?:[\v\h]+)/', '', $script);
		$output = ob_get_contents();
		ob_end_clean();
		
		$admin_url = admin_url( 'admin.php?page=ct-editor&ct_id=' . esc_attr( $_GET['ct_id'] ) );
		$output .= '<p>' 
			. sprintf( 
				__( '%1$sStop using %2$s for this Content Template%3$s', 'wpv-views' ), 
				'<a href="' . esc_url( $admin_url ) . '&ct_editor_choice=basic">',
				'Visual Composer',
				'</a>'
			) 
			. '</p>';

		return $output;
	}

	/**
	 * We need some custom scripts ( &styles )
	 * called on 'admin_print_scripts'
	 */
	public function print_scripts() {

		// disable the 100% and fixed vc editor navigation when scrolling down
		$output = '
		<style type="text/css">
			body.toolset_page_ct-editor .composer-switch {
				display:none;
			}
			body.toolset_page_ct-editor .wpv-settings-section,
			body.toolset_page_ct-editor .wpv-setting-container {
				max-width: 96% !important;
			}
			
			body.toolset_page_ct-editor .wpv-setting-container .wpv-settings-header {
				width: 15% !important;
			}
			
			.wpv-setting {
				width: 84%;
			}
			
			.wpv-mightlong-list li {
				min-width: 21%;
			}

			body.toolset_page_ct-editor .js-wpv-content-section .wpv-settings-header {
				display: block;
			}
			
			body.toolset_page_ct-editor .wpv-ct-control-switch-editor {
				padding-left: 105px;
			}
			
			body.toolset_page_ct-editor .js-wpv-content-section .wpv-setting {
				width: 100% !important;
			}
			
			.vc_subnav-fixed{
				position:relative !important;
				top:auto !important;
				left:auto !important;
				z-index: 1 !important;
				padding-left:0 !important;
			}
		</style>';

		// disable our backbone extension due to conflicts with vc (see util.js)
		$output .= "<script>var ToolsetDisableBackboneExtension = '1';</script>";
		echo preg_replace('/\v(?:[\v\h]+)/', '', $output );
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
		$content_template_has_vc = ( get_post_meta( $content_template->ID, '_toolset_user_editors_editor_choice', true ) == 'vc' );
		if ( $content_template_has_vc ) {
			$attributes['builder'] = $this->editor->getId();
		}
		return $attributes;
	}
	
	public function register_inline_editor_action_buttons( $content_template ) {
		$content_template_has_vc = ( get_post_meta( $content_template->ID, '_toolset_user_editors_editor_choice', true ) == 'vc' );
		?>
		<button 
			class="button button-secondary js-wpv-ct-apply-user-editor js-wpv-ct-apply-user-editor-<?php echo esc_attr( $this->editor->getId() ); ?>" 
			data-editor="<?php echo esc_attr( $this->editor->getId() ); ?>" 
			<?php disabled( $content_template_has_vc );?>
		>
			<?php echo $this->editor->getName(); ?>
		</button>
		<?php
	}
}