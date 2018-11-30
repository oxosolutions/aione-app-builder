<?php

/**
 * Summary.
 *
 * Description.
 *
 * @since x.x.x
 * @access (for functions: only use if private)
 *
 * @see Function/method/class relied on
 * @link URL
 * @global type $varname Description.
 * @global type $varname Description.
 *
 * @param type $var Description.
 * @param type $var Optional. Description.
 * @return type Description.
 */
class Aione_Admin_Edit_Template extends Aione_Admin_Page
{
    public function __construct()
    {
        add_action( 'wp_ajax_aione_ajax_delete_template',array($this,'aione_ajax_delete_template_callback') );

        $custom_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, array() );
    }

    public function init_admin()
    {
        $this->init_hooks();

        $this->get_id = 'aione-template-slug';

        $this->post_type = 'template';
        $this->boxes = array(
            'types_template_type' => array(
                'callback' => array($this, 'box_template_type'),
                'title' => __('Template type', 'aione-app-builder'),
                'default' => 'normal',
                'post_types' => 'custom',
            ),
            'types_editor' => array(
                'callback' => array($this, 'box_editor'),
                'title' => __('Content Design', 'aione-app-builder'),
                'default' => 'advanced',
                'post_types' => 'custom',
            ),
            'types_structured_data' => array(
                'callback' => array($this, 'box_structured_data'),
                'title' => __('Structured Data', 'aione-app-builder'),
                'default' => 'advanced',
                'post_types' => 'custom',
            ),
            /*'types_applyto' => array(
                'callback' => array($this, 'box_applyto'),
                'title' => __('Applied to', 'aione-app-builder'),
                'default' => 'side',
                'post_types' => 'custom',
            ),*/

            'submitdiv' => array(
                'callback' => array($this, 'box_submitdiv'),
                'title' => __('Save', 'aione-app-builder'),
                'default' => 'side',
                'priority' => 'core',
            ),
        );
        $this->boxes = apply_filters('aione_meta_box_order_defaults', $this->boxes, 'template');
        $this->boxes = apply_filters('aione_meta_box_template', $this->boxes);

        /** This action is documented in includes/classes/class.types.admin.page.php  */
        add_action('aione_closedpostboxes', array($this, 'closedpostboxes'));
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function form()
    {
        $this->save();
        // Flush rewrite rules if we're asked to do so.
        //
        // This must be done after all post types and taxonomies are registered, and they can be registered properly
        // only on 'init'. So after making changes, we need to reload the page and THEN flush.
        if( '1' == aione_getget( 'flush', '0' ) ) {
            flush_rewrite_rules();
        }
        
        global $aione;

        $id = false;
        $update = false;
        $taxonomies = array();

        if ( isset( $_GET[$this->get_id] ) ) {
            $id = sanitize_text_field( $_GET[$this->get_id] );
        } elseif ( isset( $_POST[$this->get_id] ) ) {
            $id = sanitize_text_field( $_POST[$this->get_id] );
        } else {
            $id = sanitize_text_field( $this->get_id );
        }

        $custom_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, array() );
        $this->at = @$custom_templates[$id];
        
        $current_user_can_edit = true;
        
        $form = $this->prepare_screen();

        $form['form-open'] = array(
            '#type' => 'markup',
            '#markup' => sprintf(
                '<div id="post-body-content" class="%s">',
                $current_user_can_edit? '':'aione-read-only'
            ),
            '_builtin' => true,
        );

        $form['title-div-open'] = array(
            '#type' => 'markup',
            '#markup' => '<div id="titlediv"><div id="titlewrap"><label class="" id="aione-template-title" for="title">'.__( 'Template Name', 'aione-app-builder' ).'</label>',
        );
        if(isset($_GET[$this->get_id])){
            $title_attribute = '';
            $form['id'] = array(
                '#type' => 'hidden',
                '#value' => $id,
                '#name' => 'at[aione-template-slug]',
            );
        } else {
            $title_attribute = 'autofocus';
        } 
        $form['name'] = array(
            '#type' => 'textfield',
            '#name' => 'at[name]',
            '#value' => isset( $this->at['name'] ) ? $this->at['name']:'',
            '#validate' => array(
                'required' => array('value' => true),
                'maxlength' => array('value' => 30),
            ),
            '#attributes' => array(
                'class' => 'widefat js-aione-template-slugize-source',
                'id' => 'title',
                'size' => '30',
                'required' => 'required',
                $title_attribute => $title_attribute,
            ),
        );
        $form['title-div-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div></div>',
        );
        $form['error-div-open'] = array(
            '#type' => 'markup',
            '#markup' => '<div class="error template-slug-error notice is-dismissible hidden"><p></p>',
        );
        $form['error-div-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div>',
        );
        $form['slug-div-open'] = array(
            '#type' => 'markup',
            '#markup' => '<div class="inside"><div id="edit-slug-box"><strong>Template Slug : </strong><span class="template-slug"></span>',
        );
        $form['slug'] = array(
            '#type' => 'hidden',
            '#name' => 'at[slug]',
            '#value' => isset( $this->at['slug'] ) ? $this->at['slug']:'',
            '#attributes' => array(
                'id' => 'template_slug',
            ),
        );
        $form['slug-div-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div></div>',
        );

        $form['box-1-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div>',
            '_builtin' => true,
        );

        

        /**
         * return form if current_user_can edit
         */
        if ( $current_user_can_edit) {
            return $form;
        }

        return aione_admin_common_only_show($form);
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    function box_submitdiv()
    {
        $form = array();
        $form['visibility-begin'] = array(
            '#type' => 'markup',
            '#markup' => ' <div class="misc-pub-section misc-pub-visibility" id="visibility">',
            '_builtin' => true,
        );

        $form['visibility-status'] = array(
            '#type' => 'markup',
            '#markup' => sprintf(
                '%s: <span id="post-visibility-display">%s</span>',
                __('Status', 'aione-app-builder'),
                (isset( $this->ct['public'] ) && strval( $this->ct['public'] ) == 'hidden') ? __('Draft', 'aione-app-builder'):__('Published', 'aione-app-builder')
            ),
            '_builtin' => true,
        );

        $form['visibility-choose-begin'] = array(
            '#type' => 'markup',
            '#markup' => sprintf(
                ' <a href="#visibility" class="edit-visibility hide-if-no-js"><span aria-hidden="true">%s</span> <span class="screen-reader-text">%s</span></a>',
                __('Edit', 'aione-app-builder'),
                __('Edit status', 'aione-app-builder')
            ),
            '_builtin' => true,
        );

        $form['visibility-edit-begin'] = array(
            '#type' => 'markup',
            '#markup' => '<div id="post-visibility-select" class="hide-if-js">',
            '_builtin' => true,
        );

        $form['visibility-choose-public'] = array(
            '#type' => 'radios',
            '#name' => 'ct[public]',
            '#options' => array(
                sprintf(
                    '<span class="title">%s</span>',
                    __('Published', 'aione-app-builder')
                ) => 'public',
                sprintf(
                    '<span class="title">%s</span> <span class="description">(%s)</span>',
                    __('Draft', 'aione-app-builder'),
                    __('not visible in admin menus, no user-interface to administrate taxonomy, not queryable on front-end', 'aione-app-builder')
                ) => 'hidden',
            ),
            '#default_value' => (isset( $this->ct['public'] ) && strval( $this->ct['public'] ) == 'hidden') ? 'hidden' : 'public',
            '#inline' => true,
        );

        $form['aione-form-visiblity-toggle-open'] = array(
            '#type' => 'markup',
            '#markup' => sprintf(
                '<div id="aione-form-visiblity-toggle" %s>',
                (isset( $this->ct['public'] ) && strval( $this->ct['public'] ) == 'hidden') ? ' class="hidden"' : ''
            ),
        );

        $form['aione-form-visiblity-toggle-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div>',
        );

        $form['visibility-edit-end'] = array(
            '#type' => 'markup',
            '#markup' => '<p>
 <a href="#visibility" class="save-post-visibility hide-if-no-js button">OK</a>
 <a href="#visibility" class="cancel-post-visibility hide-if-no-js button-cancel">Cancel</a>
</p>
</div>',
            '_builtin' => true,
        );

        $form['visibility-end'] = array(
            '#type' => 'markup',
            '#markup' => '</div>',
            '_builtin' => true,
        );
        $button_text = __( 'Save Template', 'aione-app-builder' );
        $form = $this->submitdiv( $button_text, $form, 'custom-taxonomy', $this->ct['_builtin'] );
        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function box_editor()
    {
        $form = array();
        $content = isset( $this->at['content'] ) ? $this->at['content']:'';
        $editor_id = 'aione_template_editor';
        $settings = array(
            'textarea_name' => 'at[content]',
        );

        /*if(is_array( $content )){

            $content = stripslashes_deep( $content ) ;
        } else {
            $content = stripslashes( wp_kses_decode_entities( $content ) );
        }*/
        

        echo wp_editor( $content, $editor_id, $settings );

        //$form = aione_form(__FUNCTION__, $form);
        //echo $form->renderForm();
    }

    public function box_structured_data()
    {
        $form = array();
        $content = isset( $this->at['structured_data'] ) ? $this->at['structured_data']:'';
        $editor_id = 'aione_template_structured_data';
        $settings = array(
            'textarea_name' => 'at[structured_data]',
        );

        echo wp_editor( $content, $editor_id, $settings );

    }

    

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function box_template_type()
    {
        $form = array();
        $form['template-type'] = array(
            '#type' => 'radios',
            '#name' => 'at[template-type]',
            '#default_value' => (empty( $this->at['template-type'] ) || $this->at['template-type'] == 'single') ? 'single' : 'archive',
            '#inline' => true,
            '#options' => array(
                sprintf(
                    '<b>%s</b> - %s',
                    __('Single', 'aione-app-builder'),
                    __('template for single view of post', 'aione-app-builder' )
                ) => 'single',
                sprintf(
                    '<b>%s</b> - %s',
                    __('Archive', 'aione-app-builder'),
                    __( 'template design for archive view of post', 'aione-app-builder' )
                ) => 'archive',
            ),
        );

        $form['template_sidebar_left_enable'] = array(
            '#type' => 'select',
            '#label' => '<b>Enable Left Sidebar</b><br>',
            '#name' => 'at[template_sidebar_left_enable]',
            '#default_value' => (empty( $this->at['template_sidebar_left_enable'] ) || $this->at['template_sidebar_left_enable'] == 'default') ? 'default' : $this->at['template_sidebar_left_enable'],
            '#inline' => false,
            '#options' => array(
                sprintf(
                    '%s',
                    __('Default', 'aione-app-builder'),
                    __('Default', 'aione-app-builder' )
                ) => 'default',
                sprintf(
                    '%s',
                    __('Yes', 'aione-app-builder'),
                    __('Yes', 'aione-app-builder' )
                ) => 'yes',
                sprintf(
                    '%s',
                    __('No', 'aione-app-builder'),
                    __( 'No', 'aione-app-builder' )
                ) => 'no',
            ),
        );

        $sidebars = array();
        $sidebars['default'] = __( 'Default', 'gutenbergtheme' );
        foreach ( $GLOBALS['wp_registered_sidebars'] as $sidebar ) {
            $sidebar_id = $sidebar['id'];
            $sidebar_name = ucwords( $sidebar['name']);
            $sidebars[$sidebar_name] = $sidebar_id;
        }

        $form['template_sidebar_left'] = array(
            '#type' => 'select',
            '#label' => '<b>Select Left Sidebar</b><br>',
            '#name' => 'at[template_sidebar_left]',
            '#default_value' => (empty( $this->at['template_sidebar_left'] ) || $this->at['template_sidebar_left'] == 'default') ? 'default' : $this->at['template_sidebar_left'],
            '#inline' => false,
            '#options' => $sidebars,
        );


        $form['template_sidebar_right_enable'] = array(
            '#type' => 'select',
            '#label' => '<b>Enable Right Sidebar</b><br>',
            '#name' => 'at[template_sidebar_right_enable]',
            '#default_value' => (empty( $this->at['template_sidebar_right_enable'] ) || $this->at['template_sidebar_right_enable'] == 'default') ? 'default' : $this->at['template_sidebar_right_enable'],
            '#inline' => false,
            '#options' => array(
                sprintf(
                    '%s',
                    __('Default', 'aione-app-builder'),
                    __('Default', 'aione-app-builder' )
                ) => 'default',
                sprintf(
                    '%s',
                    __('Yes', 'aione-app-builder'),
                    __('Yes', 'aione-app-builder' )
                ) => 'yes',
                sprintf(
                    '%s',
                    __('No', 'aione-app-builder'),
                    __( 'No', 'aione-app-builder' )
                ) => 'no',
            ),
        );

        $form['template_sidebar_right'] = array(
            '#type' => 'select',
            '#label' => '<b>Select Right Sidebar</b><br>',
            '#name' => 'at[template_sidebar_right]',
            '#default_value' => (empty( $this->at['template_sidebar_right'] ) || $this->at['template_sidebar_right'] == 'default') ? 'default' : $this->at['template_sidebar_right'],
            '#inline' => false,
            '#options' => $sidebars,
        );


        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
    }

     /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
     public function get_post_types_supported_by_template($template){
        $custom_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, array() );
        if (
            true
            && isset($custom_templates[$template])
            && isset($custom_templates[$template]['component'])
            && is_array($custom_templates[$template]['component'])
        ) {
            return $custom_templates[$template]['component'];
        } else {
            return array();
        }
     }
    public function box_applyto()
    {
        global $aione; 
        $form = array();
        $post_types = get_post_types( '', 'objects' );
        $biultin_post_type = aione_get_builtin_in_post_types();
        $options = array();
        $supported = $this->get_post_types_supported_by_template($this->at['slug']);
        
        foreach ( $post_types as $post_type_slug => $post_type ) { 
            if ( in_array( $post_type_slug, $aione->excluded_post_types ) || !$post_type->show_ui ) {
                continue;
            }
            if ( in_array( $post_type_slug, $biultin_post_type ) ) {
                continue;
            }
            $options[$post_type_slug] = array(
                '#name' => 'at[component][' . $post_type_slug . ']',
                '#title' => $post_type->labels->name,
               // '#default_value' =>( ! empty( $this->at['post_type'][ $post_type_slug ] ) ),
                /*'#default_value' =>
                    in_array( $post_type_slug, $supported )
                    || array_key_exists( $post_type_slug, $supported )
                    || ( isset( $_GET['assign_type'] ) && $_GET['assign_type'] == $post_type_slug ),*/
                    '#default_value' =>
                        in_array( $post_type_slug, $supported )
                        || array_key_exists( $post_type_slug, $supported )
                        || ( isset( $_GET['assign_type'] ) && $_GET['assign_type'] == $post_type_slug ),
                '#inline' => true,
                '#before' => '<li>',
                '#after' => '</li>',
            );
        }

        $options = $this->sort_by_title($options);

        $form['types'] = array(
            '#type' => 'checkboxes',
            '#options' => $options,
            '#name' => 'at[component]',
            '#inline' => true,
            '#before' => '<ul class="aione-list">',
            '#after' => '</ul>',
        );
        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
        
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    public function closedpostboxes( $screen_base )
    {
        if ( 'aione-app-builder_page_aione-edit-taxonomy' != $screen_base ) {
            return;
        }
        $option_name = sprintf('closedpostboxes_%s', $screen_base);
        $closedpostboxes = get_user_meta(get_current_user_id(), $option_name);
        if ( !empty($closedpostboxes) ) {
            return;
        }
        $closedpostboxes[] = 'types_options';
        $closedpostboxes[] = 'types_labels';
        update_user_option( get_current_user_id(), $option_name, $closedpostboxes, true);
    }

    /**
     * Summary.
     *
     * Description.
     *
     * @since x.x.x
     * @access (for functions: only use if private)
     *
     * @see Function/method/class relied on
     * @link URL
     * @global type $varname Description.
     * @global type $varname Description.
     *
     * @param type $var Description.
     * @param type $var Optional. Description.
     * @return type Description.
     */
    private function save()
    {
        if ( !isset( $_POST['at'] ) ) {
            return false;
        }

        $data = $_POST['at'];
        $update = false;
        // Sanitize data
        $data['name'] = sanitize_text_field( $data['name'] );
        if (empty($data['name']) || empty($data['slug']) ) {
            aione_admin_message( __( 'Please set template name', 'aione-app-builder' ), 'error' );
            return false;
        }

        $protected_data_check = array();

        if ( isset( $data[$this->get_id] ) ) {
            $update = true;
            $data[$this->get_id] = sanitize_title( $data[$this->get_id] );
        } else {
            $data[$this->get_id] = null;
        }

        if ( isset( $data['slug'] ) ) {
            $data['slug'] = sanitize_title( $data['slug'] );
        } else {
            $data['slug'] = null;
        }

        $custom_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, array() );

        // Check overwriting
        if ( ( !array_key_exists( $this->get_id, $data ) || $data[$this->get_id] != $data['slug'] ) && array_key_exists( $data['slug'], $custom_templates ) ) {
            aione_admin_message( __( 'Template with name "'.$data['name'].'" is already exists. Please choose a different name.', 'aione-app-builder' ), 'error' );
            return false;
        }

        if ( !empty( $data[$this->get_id] )){
            // Set protected data
            $protected_data_check = $custom_templates[$data[$this->get_id]];
            // Delete old type
            unset( $custom_templates[$data[$this->get_id]] );
        } else {
            // Set protected data
            $protected_data_check = !empty( $custom_templates[$data['slug']] ) ? $custom_templates[$data['slug']] : array();
        }
        
        //$data['content'] = wp_kses_post($data['content']);
        $data['content'] = html_entity_decode(stripcslashes($data['content']));

        //$data['structured_data'] = wp_kses_post($data['structured_data']);
        $data['structured_data'] = html_entity_decode(stripcslashes($data['structured_data']));

        /******/
        foreach ($custom_templates as $key => $array) {
            foreach ($array['component'] as $k => $v) { 
                if(array_key_exists($k, $data['component'])){ 
                    unset($custom_templates[$key]['component'][$k]);
                }
            }
        }
        /*** **/
        
        /**
         * Sync with post types
         */
        $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $post_types = $post_type_option->get_components();

        foreach ( $post_types as $id => $type ) {
            if ( !empty( $data['component'] ) && array_key_exists( $id, $data['component'] ) ) {
                if($data['template-type'] == 'single'){
                    $post_types[$id]['single-template'] = $data['slug'];
                }
                if($data['template-type'] == 'archive'){
                    $post_types[$id]['archive-template'] = $data['slug'];
                }
            } else {
                //unset( $post_types[$id]['taxonomies'][$data['slug']] );
            }
        }

        update_option(AIONE_OPTION_NAME_COMPONENTS, $post_types);



        /**
         * set last edit time
         */
        $data[AIONE_EDIT_LAST] = time();

        /**
         * set last edit author
         */

        $data[AIONE_AUTHOR] = get_current_user_id();

        $custom_templates[$data['slug']] = array_merge( $protected_data_check, $data );

        update_option( AIONE_OPTION_NAME_TEMPLATES, $custom_templates );
        // success message
        $msg = $update
            ? __( 'Template saved.', 'aione-app-builder' )
            : __( 'New Template created.', 'aione-app-builder' );

        aione_admin_message_store(
            $msg,
            'updated notice notice-success is-dismissible'
        );

        flush_rewrite_rules();

        // Redirect
        wp_safe_redirect(
            esc_url_raw(
                add_query_arg(
                    array(
                        'page' => 'aione-edit-template',
                        $this->get_id => $data['slug'],
                        'aione-message' => 'view',
                        // Flush rewrite rules after reload
                        'flush' => '1'
                    ),
                    admin_url( 'admin.php' )
                )
            )
        );
        die();
    }

    function aione_ajax_delete_template_callback(){
        switch ($_REQUEST['aione_action']){
            case 'delete_template':
                $custom_template = $this->aione_ajax_helper_get_template();
                if ( empty($custom_template) ) {
                    aione_ajax_helper_print_error_and_die();
                }
                $custom_templates = get_option(AIONE_OPTION_NAME_TEMPLATES, array());
                unset($custom_templates[$custom_template]);
                update_option(AIONE_OPTION_NAME_TEMPLATES, $custom_templates);
                echo json_encode(
                    array(
                        'output' => '',
                        'execute' => 'reload',
                        'aione_nonce_ajax_callback' => wp_create_nonce('execute'),
                    )
                );

                break;


                default:
                $fallthrough = true;
                break;
        }
        die();
    }
    function aione_ajax_helper_get_template()    {
        if (!isset($_GET['aione-template-slug']) || empty($_GET['aione-template-slug'])) {
            return false;
        }
        $template = $_GET['aione-template-slug'];
        $custom_templates = get_option(AIONE_OPTION_NAME_TEMPLATES, array());
        if (
            isset($custom_templates[$template])
            && isset($custom_templates[$template]['slug'])
        ) {
            return $custom_templates[$template]['slug'];
        }
        return false;
    }
    
}

