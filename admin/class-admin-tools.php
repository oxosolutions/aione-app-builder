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
class Aione_Admin_Tools extends Aione_Admin_Page
{
    private $fields;


    public function __construct()   {
        $this->plugin_name = AIONE_PLUGIN_NAME;
        $this->version = AIONE_VERSION;
        
    }


    public function init_admin()    {
        $this->init_hooks();
        $this->get_id = 'aione-tools';

        $this->post_type = 'post_type';

        $this->boxes = array(
            
            'types_export' => array(
                'callback' => array($this, 'box_export'),
                'title' => __('Export', 'aione-app-builder'),
                'default' => 'advanced',
                'priority' => 'core',
            ),
            'types_import' => array(
                'callback' => array($this, 'box_import'),
                'title' => __('Import', 'aione-app-builder'),
                'default' => 'advanced',
                'priority' => 'core',
            ),
            
        );

        

        $this->boxes = apply_filters('aione_meta_box_order_defaults', $this->boxes, $this->post_type);

        $this->boxes = apply_filters('aione_meta_box_post_type', $this->boxes);


        /** This action is documented in includes/classes/class.types.admin.page.php  */
        add_action('aione_closedpostboxes', array($this, 'closedpostboxes'));

    }

    /**
     * Add/edit form
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
        $form = $this->prepare_screen();
        

       
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
    

    /**
     * Labels
     */
    public function box_export()
    {
        $form = array();
        $options[AIONE_OPTION_NAME_COMPONENTS] = array(
            '#name' => 'aione_tools[export]['.AIONE_OPTION_NAME_COMPONENTS.']',
            '#title' => 'Components',
            '#default_value' => '',
            '#inline' => true,
            '#before' => '<li>',
            '#after' => '</li>',
            '#attributes' => array(                
                //'disabled' => 'disabled',
            )
        );
        $options[AIONE_OPTION_NAME_TAXONOMIES] = array(
            '#name' => 'aione_tools[export]['.AIONE_OPTION_NAME_TAXONOMIES.']',
            '#title' => 'Taxonomies',
            '#default_value' => '',
            '#inline' => true,
            '#before' => '<li>',
            '#after' => '</li>',
            '#attributes' => array(                
                //'disabled' => 'disabled',
            )
        );
        $options[AIONE_OPTION_NAME_TEMPLATES] = array(
            '#name' => 'aione_tools[export]['.AIONE_OPTION_NAME_TEMPLATES.']',
            '#title' => 'Templates',
            '#default_value' => '',
            '#inline' => true,
            '#before' => '<li>',
            '#after' => '</li>',
            '#attributes' => array(                
                //'disabled' => 'disabled',
            )
        );
        $form['aione_tools_export'] = array(
            '#type' => 'checkboxes',
            '#options' => $options,
            '#name' => 'aione_tools[export]',
            '#inline' => true,
            '#before' => '<ul class="aione-list">',
            '#after' => '</ul>',
            
        ); 
        $form['aione_tools_export_submit'] = array(
            '#type' => 'submit',
            '#name' => 'aione_tools_export_submit',
            '#value' => 'Export',
            '#before' => '<p class="aione-submit">',
            '#after' => '</p>',
            '#attributes' => array(
                'class' => 'button button-primary aione-form-submit form-submit',
            ),
        ); 
        
        //echo '<form method="post" name="export-form" action="" class="aione-fields-form aione-form-validate js-types-show-modal">';
        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
        //echo "</form>";
    }

    /**
     * Render the content of the metabox "Taxonomies to be used with $post_type".
     *
     * @since unknown
     */
    public function box_import() {
	    $form = array();
	    $form['aione_tools_import'] = array(
            '#type' => 'file',
            '#name' => 'aione_tools_import',            
        ); 
        $form['aione_tools_import_submit'] = array(
            '#type' => 'submit',
            '#name' => 'aione_tools_import_submit',
            '#value' => 'Import',
            '#before' => '<p class="aione-submit">',
            '#after' => '</p>',
            '#attributes' => array(
                'class' => 'button button-primary aione-form-submit form-submit',
            ),
        ); 
        //echo '<form method="post" name="import-form" action="" class="aione-fields-form aione-form-validate js-types-show-modal" enctype="multipart/form-data">';
        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
        //echo "</form>";
    }


    private function save()
    {
        global $aione;
        

        if ( isset( $_POST['aione_tools_export_submit'] ) ) {

            $data = $_POST;
           
            // Sanitize data
            
            if(array_key_exists('aione_tools', $data) == false){ 
                //aione_admin_message( __( 'Please select any option', 'aione-app-builder' ), 'error' );
                aione_admin_message_store(
                    __( 'Please select any option', 'aione-app-builder' ),
                    'error'
                );          
                return false; 
            }
            if((array_key_exists('export', $data['aione_tools']) == false) || (empty($data['aione_tools']['export'])) ){ 
                //aione_admin_message( __( 'Please select any option', 'aione-app-builder' ), 'error' );
                aione_admin_message_store(
                    __( 'Please select any option', 'aione-app-builder' ),
                    'error'
                );
                return false; 
            }

            $export_items = array_keys($data['aione_tools']['export']);
            $option_value = array();
            foreach ($export_items as $option) {
                $option_value[$option] = get_option($option);
            }
            

            $file_name = 'aione-export-' . date('Y-m-d') . '.json';
            header( "Content-Description: File Transfer" );
            header( "Content-Disposition: attachment; filename={$file_name}" );
            header( "Content-Type: application/json; charset=utf-8" );
            
            echo json_encode($option_value);        
    	    flush_rewrite_rules();
            
            die();
        }  
        if ( isset( $_POST['aione_tools_import_submit'] ) ) { 

            if( empty($_FILES['aione_tools_import']['size']) ) {
                aione_admin_message_store(
                    __( 'No file selected', 'aione-app-builder' ),
                    'error'
                );
                return false;
            }
            // Get file data.
            $file = $_FILES['aione_tools_import'];
            
            // Check errors.
            if( $file['error'] ) {
                aione_admin_message_store(
                    __( 'Error uploading file. Please try again', 'aione-app-builder' ),
                    'error'
                );
                return false;
            }

            

            // Check file type.
            if( pathinfo($file['name'], PATHINFO_EXTENSION) !== 'json' ) {
                aione_admin_message_store(
                    __( 'Incorrect file type', 'aione-app-builder' ),
                    'error'
                );
                return false;
            }

            // Read JSON.
            $json = file_get_contents( $file['tmp_name'] );
            $json = json_decode($json, true);
            
            // Check if empty.
            if( !$json || !is_array($json) ) {
                aione_admin_message_store(
                    __( 'Import file empty', 'aione-app-builder' ),
                    'error'
                );
                return false;
            }
            
            // Import field group.
            $import_json = $this->aione_import( $json );

            aione_admin_message_store(
                    __( 'Import Successfully', 'aione-app-builder' ),
                    'updated notice notice-success is-dismissible'
                );

            flush_rewrite_rules();
            // Redirect
            wp_safe_redirect(
                esc_url_raw(
                    add_query_arg(
                        array(
                            'page' => 'aione-tools',
                            $this->get_id => $post_type,
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
        
    }

    function aione_import($data){
        if(array_key_exists(AIONE_OPTION_NAME_TEMPLATES,$data)){
            $aione_existing_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, true );
            $aione_new_templates = $data[AIONE_OPTION_NAME_TEMPLATES];
            $updated_templates_array = array_merge($aione_existing_templates,$aione_new_templates);
            update_option( AIONE_OPTION_NAME_TEMPLATES, $updated_templates_array);
        }
        if(array_key_exists(AIONE_OPTION_NAME_TAXONOMIES,$data)){
            $aione_existing_taxonomies = get_option( AIONE_OPTION_NAME_TAXONOMIES, true );
            $aione_new_taxonomies = $data[AIONE_OPTION_NAME_TAXONOMIES];
            $updated_taxonomies_array = array_merge($aione_existing_taxonomies,$aione_new_taxonomies);
            update_option( AIONE_OPTION_NAME_TAXONOMIES, $updated_taxonomies_array);
        }
        if(array_key_exists(AIONE_OPTION_NAME_COMPONENTS,$data)){
            $aione_existing_components = get_option( AIONE_OPTION_NAME_COMPONENTS, true );
            $aione_new_components = $data[AIONE_OPTION_NAME_COMPONENTS];
            $updated_components_array = array_merge($aione_existing_components,$aione_new_components);
            update_option( AIONE_OPTION_NAME_COMPONENTS, $updated_components_array);
        }
        
        
        return $data;
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
        
        if ( 'aione-app-builder_page_aione-tools' != $screen_base ) {
            return;
        }
        $option_name = sprintf('closedpostboxes_%s', $screen_base);
        
        $closedpostboxes = get_user_meta(get_current_user_id(), $option_name);

        if ( !empty($closedpostboxes) ) {
            return;
        }       
        
        update_user_option( get_current_user_id(), $option_name, $closedpostboxes, true);
    }

    


    

    
}

