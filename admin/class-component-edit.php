<?php

//require_once dirname( __FILE__ ) . '/class-aione-admin-page.php';
//include_once dirname( __FILE__ ).'/common-functions.php';
//include_once dirname( __FILE__ ).'/component-functions.php';

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
class Aione_Admin_Edit_Component extends Aione_Admin_Page
{
    private $fields;


    public function __construct()   {
        $this->plugin_name = AIONE_PLUGIN_NAME;
        $this->version = AIONE_VERSION;
        //add_action('wp_ajax_aione_edit_post_get_child_fields_screen', array($this, 'prepare_field_select_screen'));
        
        //add_action('wp_ajax_aione_edit_post_save_child_fields', array($this, 'save_child_fields'));
        //add_action('wp_ajax_aione_edit_post_save_custom_fields_groups', array($this, 'save_custom_fields_groups'));
        //add_filter('types_get_post_type_slug_from_request', array($this, 'get_post_type_slug_from_request'));
        //add_action( 'wp_ajax_aione_get_forbidden_names', array($this,'ajax_aione_is_reserved_name' ));

        add_action('wp_ajax_aione_edit_post_get_icons_list', array($this, 'get_icons_list'));
        add_action( 'wp_ajax_aione_ajax_delete_component',array($this,'aione_ajax_delete_component_callback') );
        
    }


    public function init_admin()    {
        $this->init_hooks();
        $this->get_id = 'aione-component-slug';

        $this->post_type = 'post_type';

        $this->boxes = array(
            'submitdiv' => array(
                'callback' => array($this, 'box_submitdiv'),
                'title' => __('Save', 'aione-app-builder'),
                'default' => 'side',
                'priority' => 'core',
            ),
            'types_labels' => array(
                'callback' => array($this, 'box_labels'),
                'title' => __('Labels', 'aione-app-builder'),
                'default' => 'normal',
                'post_types' => 'custom',
                'priority' => 'core',
            ),
            'types_taxonomies' => array(
                'callback' => array($this, 'box_taxonomies'),
                'title' => __('Taxonomies to be used with <i class="js-aione-singular"></i>', 'aione-app-builder'),
                'default' => 'normal',
                'priority' => 'core',
            ),
            'types_display_sections' => array(
                'callback' => array($this, 'box_display_sections'),
                'title' => __('Sections to display when editing <i class="js-aione-singular"></i>', 'aione-app-builder'),
                'default' => 'normal',
                'priority' => 'low',
                'post_types' => 'custom',
            ),
            'types_options' => array(
                'callback' => array($this, 'box_options'),
                'title' => __('Options', 'aione-app-builder'),
                'default' => 'normal',
                'post_types' => 'custom',
                'priority' => 'low',
            ),
            'types_template' => array(
                'callback' => array($this, 'box_template'),
                'title' => __('Set Template', 'aione-app-builder'),
                'default' => 'normal',
                //'post_types' => 'custom',
                'priority' => 'high',
            ),
            
        );

        if(sanitize_text_field( $_GET['aione-component-slug'] )){
            $this->boxes['types_admin_custom_columns'] = array(
                    'callback' => array($this, 'box_admin_custom_columns'),
                    'title' => __('Admin Columns', 'aione-app-builder'),
                    'default' => 'side',
                    'post_types' => 'custom',
                );

            $this->boxes['types_filter'] = array(
                    'callback' => array($this, 'filters'),
                    'title' => __('Filters', 'aione-app-builder'),
                    'default' => 'side',
                    'post_types' => 'custom',
                );

            $this->boxes['types_admin_columns_order'] = array(
                    'callback' => array($this, 'box_admin_columns_order'),
                    'title' => __('Admin Columns Order', 'aione-app-builder'),
                    'default' => 'side',
                    'post_types' => 'custom',
                );
        }

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
        $current_user_can_edit = true;


        $id = false;
        $update = false;

        if ( isset( $_GET[$this->get_id] ) ) {
            $id = sanitize_text_field( $_GET[$this->get_id] );
        } elseif ( isset( $_POST[$this->get_id] ) ) {
            $id = sanitize_text_field( $_POST[$this->get_id] );
        }

        /**
         * get current post type
         */
        //require_once dirname( __FILE__ ).'/class-aione-admin-component.php';
        $aione_post_type = new Aione_Admin_Component();
        $custom_post_type = $aione_post_type->get_post_type($id);
        if (empty($custom_post_type)) {
            aione_admin_message( __( 'Please save new Component first.', 'aione-app-builder' ), 'error' );
            die;
        }
        $this->ct = $custom_post_type;
        
        $aione_custom_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, true );
               
        /**
         * sanitize _builtin
         */
        if ( !isset($this->ct['_builtin']) ) {
            $this->ct['_builtin'] = false;
        }

        /**
         * fix taxonomies assigment for builitin post types
         */
        if ( $this->ct['_builtin']) {
            $taxonomies = get_taxonomies( '', 'objects' );
            foreach( $taxonomies as $slug => $tax ) {
                foreach( $tax->object_type as $post_slug ) {
                    if ( $this->ct['slug'] == $post_slug) {
                        $this->ct['taxonomies'][$slug] = 1;
                    }
                }
            }
        }

        $form = $this->prepare_screen();
        if ( $this->ct['update'] ) {
            $form['id'] = array(
                '#type' => 'hidden',
                '#value' => $id,
                '#name' => 'ct[aione-component-slug]',
                '_builtin' => true,
            );

	        $form['slug_conflict_check_nonce'] = array(
		        '#type' => 'hidden',
		        '#value' => wp_create_nonce( 'check_slug_conflicts' ),
		        '#name' => 'aione_check_slug_conflicts_nonce',
		        '_builtin' => true,
	        );
	        
            /**
             * update Taxonomy too
             */
            $custom_taxonomies = get_option( AIONE_OPTION_NAME_TAXONOMIES, array() );
            foreach( $custom_taxonomies as $slug => $data ) {
                if ( !array_key_exists('supports', $data)) {
                    continue;
                }
                if ( !array_key_exists($id, $data['supports']) ) {
                    continue;
                }
                if (
                    array_key_exists('taxonomies', $this->ct)
                    && array_key_exists($slug, $this->ct['taxonomies'])
                ) {
                    continue;
                }
                unset($custom_taxonomies[$slug]['supports'][$id]);
            }
            update_option( AIONE_OPTION_NAME_TAXONOMIES, $custom_taxonomies);
        }

        /*
         * menu icon
         */
        switch( $this->ct['slug'] ) {
            case 'page':
                $menu_icon = 'admin-page';
                break;
            case 'attachment':
                $menu_icon = 'admin-media';
                break;
            default:
                $menu_icon = isset( $this->ct['icon']) && !empty($this->ct['icon']) ? $this->ct['icon'] : 'admin-post';
                break;
        }

        /**
         * post icon field
         */
        $form['icon'] = array(
            '#type' => 'hidden',
            '#name' => 'ct[icon]',
            '#value' => $menu_icon,
            '#id' => 'aione-icon',
        );

        $form['form-open'] = array(
            '#type' => 'markup',
            '#markup' => sprintf(
                '<div id="post-body-content" class="%s">',
                $current_user_can_edit? '':'aione-read-only'
            ),
            '_builtin' => true,
        );

        $form['table-1-open'] = array(
            '#type' => 'markup',
            '#markup' => '<table id="aione-form-name-table" class="aione-form-table widefat js-aione-slugize-container"><thead><tr><th colspan="2">' . __( 'Name and description', 'aione-app-builder' ) . '</th></tr></thead><tbody>',
            '_builtin' => true,
        );
        $table_row = '<tr><td><LABEL></td><td><ERROR><BEFORE><ELEMENT><AFTER></td></tr>';
        $form['name'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[labels][name]',
            '#title' => __( 'Name plural', 'aione-app-builder' ) . ' (<strong>' . __( 'required', 'aione-app-builder' ) . '</strong>)',
            '#description' => '<strong>' . __( 'Enter in plural!', 'aione-app-builder' )
            . '.',
            '#value' => isset( $this->ct['labels']['name'] ) ? $this->ct['labels']['name'] : '',
            '#validate' => array(
                'required' => array('value' => 'true'),
            ),
            '#pattern' => $table_row,
            '#inline' => true,
            '#id' => 'name-plural',
            '#attributes' => array(
                'data-aione_warning_same_as_slug' => __( "It is not recommended to have same plural and singular name for a Post Type. Please use a different name for the singular and plural names.", 'aione-app-builder' ),
                'data-aione_warning_same_as_slug_ignore' => __( 'Ignore this warning.', 'aione-app-builder' ),
                'placeholder' => __('Enter Component name plural', 'aione-app-builder' ),
                'class' => 'large-text js-aione-validate',
                'required' => 'required',
            ),
            '_builtin' => true,
        );
        $form['name-singular'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[labels][singular_name]',
            '#title' => __( 'Name singular', 'aione-app-builder' ) . ' (<strong>' . __( 'required', 'aione-app-builder' ) . '</strong>)',
            '#description' => '<strong>' . __( 'Enter in singular!', 'aione-app-builder' )
            . '</strong><br />'
            . '.',
            '#value' => isset( $this->ct['labels']['singular_name'] ) ? $this->ct['labels']['singular_name'] : '',
            '#validate' => array(
                'required' => array('value' => 'true'),
            ),
            '#pattern' => $table_row,
            '#inline' => true,
            '#id' => 'name-singular',
            '#attributes' => array(
                'placeholder' => __('Enter Component name singular', 'aione-app-builder' ),
                'class' => 'js-aione-slugize-source large-text js-aione-validate',
                'data-anonymous-component' => __( 'this Component', 'aione-app-builder' ),
                'required' => 'required',
            ),
            '_builtin' => true,
        );

        /**
         * IF isset $_POST['slug'] it means form is not submitted
         */
        $attributes = array();
        if ( !empty( $_POST['ct']['slug'] ) ) {
            $reserved = $this->aione_is_reserved_name( sanitize_text_field( $_POST['ct']['slug'] ), 'post_type' );

            if ( is_wp_error( $reserved ) ) {
                $attributes = array(
                    'class' => 'aione-form-error',
                    'onclick' => 'jQuery(this).removeClass(\'aione-form-error\');'
                );
            }
        }

        $form['slug'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[slug]',
            '#title' => __( 'Slug', 'aione-app-builder' ) . ' (<strong>' . __( 'required', 'aione-app-builder' ) . '</strong>)',
            '#value' => isset( $this->ct['slug'] ) ? $this->ct['slug'] : '',
            '#pattern' => $table_row,
            '#inline' => true,
            '#validate' => array(
                'required' => array('value' => 'true'),
                'nospecialchars' => array('value' => 'true'),
                'maxlength' => array('value' => '20'),
            ),
            '#attributes' => $attributes + array(
                'maxlength' => '20',
                'placeholder' => __('Enter Component slug', 'aione-app-builder' ),
                'class' => 'js-aione-slugize large-text js-aione-validate',
                'required' => 'required',
            ),
            '#id' => 'slug',
            '_builtin' => true,
        );

        // disable for inbuilt
        if ( $this->ct['_builtin'] ) {
            $form['slug']['#disable'] = 1;
            $form['slug']['#pattern'] = '<tr><td><LABEL></td><td><ERROR><BEFORE><ELEMENT><DESCRIPTION><AFTER></td></tr>';
            $form['slug']['#description'] = __('This option is not available for built-in post types.', 'aione-app-builder');
        }

        $form['description'] = array(
            '#type' => 'textarea',
            '#name' => 'ct[description]',
            '#title' => __( 'Description', 'aione-app-builder' ),
            '#value' => isset( $this->ct['description'] ) ? $this->ct['description'] : '',
            '#attributes' => array(
                'rows' => 4,
                'cols' => 60,
                'placeholder' => __('Enter Component description', 'aione-app-builder' ),
                'class' => 'hidden js-aione-description',
            ),
            '#pattern' => $table_row,
            '#inline' => true,
            '#after' => sprintf(
                '<a class="js-aione-toggle-description hidden" href="#">%s</a>',
                __('Add description', 'aione-app-builder')
            ),
        );
        /**
         * icons only for version 3.8 up
         */
        global $wp_version;
        if ( version_compare( '3.8', $wp_version ) < 1 ) {
            $form['choose-icon'] = array(
                '#name' => 'choose-icon',
                '#type' => 'button',
                '#value' => esc_attr__('Change icon', 'aione-app-builder'),
                '#inline' => true,
                '#title' => __('Icon', 'aione-app-builder'),
                '#pattern' => '<tr><td><LABEL></td><td><ERROR><BEFORE><ELEMENT><DESCRIPTION><AFTER></td></tr>',
                '#attributes' => array(
                    'data-aione-nonce' => wp_create_nonce('post-type-dashicons-list'),
                    'data-aione-post-type' => esc_attr($this->ct['slug']),
                    'data-aione-message-loading' => esc_attr__('Please Wait, Loading…', 'aione-app-builder'),
                    'data-aione-title' => esc_attr__('Choose icon', 'aione-app-builder'),
                    'data-aione-cancel' => esc_attr__('Cancel', 'aione-app-builder'),
                    'data-aione-value' => esc_attr($menu_icon),
                    'class' => 'js-aione-choose-icon',
                ),
                '#before' => sprintf(
                    '<div class="aione-menu-image dashicons-before dashicons-%s"><br></div>',
                    esc_attr($menu_icon)
                ),
            );
            /**
             * clear ability to change for builitin post types
             */
            if ( $this->ct['_builtin'] ) {
                $form['choose-icon']['#disable'] = 1;
                $form['choose-icon']['#description'] = __('This option is not available for built-in post types.', 'aione-app-builder');
            }
        }
        $form['table-1-close'] = array(
            '#type' => 'markup',
            '#markup' => '</tbody></table>',
            '_builtin' => true,
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
    function box_submitdiv()    {
        $button_text = __( 'Save Component', 'aione-app-builder' );

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
                    __('not visible in admin menus, no user-interface to administrate posts, not queryable on front-end', 'aione-app-builder')
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
            if( 'edit.php?post_type=' . $this->ct['slug'] == $menu_item[2] )
                continue;

            // add menu item to options
            $options[$option_name] = $menu_position . '--aione-add-menu-after--' . $menu_item[2];
        }

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

        /**
         * admin menu position
         */
        $form['menu_position'] = array(
            '#type' => 'select',
            '#name' => 'ct[menu_position]',
            '#title' => __( 'Admin Menu position after: ', 'aione-app-builder' ),
            '#default_value' => isset( $this->ct['menu_position'] ) ? $this->ct['menu_position'] : '',
            // '#validate' => array('number' => array('value' => true)),
            '#inline' => true,
            '#pattern' => '<BEFORE><p><LABEL><ELEMENT><ERROR></p><AFTER>',
            '#options' => $options,
            '#before' => '<div class="misc-pub-section">',
            '#after' => '</div>',
            '#attributes' => array(
                'class' => 'js-aione-menu-position-after widefat',
                'data-aione-menu-position' => isset( $this->ct['menu_position'] ) ? $this->ct['menu_position'] : ''
            ),
        );
        /**
         * dashboard glance option to show counters on admin dashbord widget
         */
        if( $this->ct['slug'] != 'post' && $this->ct['slug'] != 'page' ) {
            $form['dashboard_glance'] = array(
                '#type' => 'checkbox',
                '#before' => '<div class="misc-pub-section">',
                '#after' => '</div>',
                '#name' => 'ct[dashboard_glance]',
                '#title' => __( 'Show number of entries on "At a Glance" admin widget.', 'aione-app-builder' ),
                '#default_value' => !empty( $this->ct['dashboard_glance'] ),
            );
        }

        $form = $this->submitdiv($button_text, $form);

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
    public function box_options()
    {
        $form = array();
        
        $form['rewrite-enabled'] = array(
            '#type' => 'checkbox',
            '#title' => __( 'Rewrite', 'aione-app-builder' ),
            '#name' => 'ct[rewrite][enabled]',
            '#description' => __( 'Rewrite permalinks with this format. False to prevent rewrite. Default: true and use post type as slug.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['rewrite']['enabled'] ),
            '#inline' => true,
        );
        $form['rewrite-custom'] = array(
            '#type' => 'radios',
            '#name' => 'ct[rewrite][custom]',
            '#options' => array(
                __( 'Use the normal WordPress URL logic', 'aione-app-builder' ) => 'normal',
                __( 'Use a custom URL format', 'aione-app-builder' ) => 'custom',
            ),
            '#default_value' => empty( $this->ct['rewrite']['custom'] ) || $this->ct['rewrite']['custom'] != 'custom' ? 'normal' : 'custom',
            '#inline' => true,
            '#after' => '<br />',
        );
        $hidden = empty( $this->ct['rewrite']['custom'] ) || $this->ct['rewrite']['custom'] != 'custom' ? ' class="hidden"' : '';
        $form['rewrite-slug'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[rewrite][slug]',
            '#description' => __( 'Optional.', 'aione-app-builder' ) . ' ' . __( "Prepend posts with this slug - defaults to post type's name.", 'aione-app-builder' ),
            '#value' => isset( $this->ct['rewrite']['slug'] ) ? $this->ct['rewrite']['slug'] : '',
            '#inline' => true,
            '#before' => '<div id="aione-form-rewrite-toggle"' . $hidden . '>',
            '#after' => '</div>',
            '#validate' => array('rewriteslug' => array('value' => 'true')),
            '#attributes' => array(
                'class' => 'widefat',
            ),
        );
        $form['rewrite-with_front'] = array(
            '#type' => 'checkbox',
            '#title' => __( 'Allow permalinks to be prepended with front base', 'aione-app-builder' ),
            '#name' => 'ct[rewrite][with_front]',
            '#description' => __( 'Example: if your permalink structure is /blog/, then your links will be: false->/news/, true->/blog/news/.', 'aione-app-builder' ) . ' ' . __( 'Defaults to true.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['rewrite']['with_front'] ),
            '#inline' => true,
        );
        $form['rewrite-feeds'] = array(
            '#type' => 'checkbox',
            '#name' => 'ct[rewrite][feeds]',
            '#title' => __( 'Feeds', 'aione-app-builder' ),
            '#description' => __( 'Defaults to has_archive value.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['rewrite']['feeds'] ),
            '#inline' => true,
        );
        $form['rewrite-pages'] = array(
            '#type' => 'checkbox',
            '#name' => 'ct[rewrite][pages]',
            '#title' => __( 'Pages', 'aione-app-builder' ),
            '#description' => __( 'Defaults to true.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['rewrite']['pages'] ),
            '#inline' => true,
        );
        $show_in_menu_page = isset( $this->ct['show_in_menu_page'] ) ? $this->ct['show_in_menu_page'] : '';
        $hidden = !empty( $this->ct['show_in_menu'] ) ? '' : ' class="hidden"';

        $has_archive_slug = isset( $this->ct['has_archive_slug'] ) ? $this->ct['has_archive_slug'] : '';
        $has_archive_slug_show = empty( $this->ct['has_archive'] )? ' class="hidden"':'';

        $form['vars'] = array(
            '#type' => 'checkboxes',
            '#name' => 'ct[vars]',
            '#inline' => true,
            '#options' => array(
                'has_archive' => array(
                    '#name' => 'ct[has_archive]',
                    '#default_value' => !empty( $this->ct['has_archive'] ),
                    '#title' => __( 'has_archive', 'aione-app-builder' ),
                    '#description' => __( 'Allow to have custom archive slug for CPT.', 'aione-app-builder' ) . '<br />' . __( 'Default: not set.', 'aione-app-builder' ),
                    '#inline' => true,
                    '#after' => '<div id="aione-form-has_archive-toggle"' . $has_archive_slug_show . '><input type="text" name="ct[has_archive_slug]" class="regular-text" value="' . $has_archive_slug . '" /><div class="description aione-form-description aione-form-description-checkbox description-checkbox">' . __( 'Optional.', 'aione-app-builder' ) . ' ' . __( 'Default is value of rewrite or CPT slug.', 'aione-app-builder' ) . '</div></div>',
                ),
                'show_in_menu' => array(
                    '#name' => 'ct[show_in_menu]',
                    '#default_value' => !empty( $this->ct['show_in_menu'] ),
                    '#title' => __( 'show_in_menu', 'aione-app-builder' ),
                    '#description' => __( 'Whether to show the post type in the admin menu and where to show that menu. Note that show_ui must be true.', 'aione-app-builder' ) . '<br />' . __( 'Default: null.', 'aione-app-builder' ),
                    '#after' => '<div id="aione-form-showinmenu-toggle"' . $hidden . '><input type="text" name="ct[show_in_menu_page]" class="regular-text" value="' . $show_in_menu_page . '" /><div class="description aione-form-description aione-form-description-checkbox description-checkbox">' . __( 'Optional.', 'aione-app-builder' ) . ' ' . __( "Top level page like 'tools.php' or 'edit.php?post_type=page'", 'aione-app-builder' ) . '</div></div>',
                    '#inline' => true,
                ),
                'show_ui' => array(
                    '#name' => 'ct[show_ui]',
                    '#default_value' => !empty( $this->ct['show_ui'] ),
                    '#value' => !empty( $this->ct['show_ui'] ),
                    '#title' => __( 'show_ui', 'aione-app-builder' ),
                    '#description' => __( 'Generate a default UI for managing this post type.', 'aione-app-builder' ) . '<br />' . __( 'Default: value of public argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'publicly_queryable' => array(
                    '#name' => 'ct[publicly_queryable]',
                    '#default_value' => !empty( $this->ct['publicly_queryable'] ),
                    '#value' => !empty( $this->ct['publicly_queryable'] ),
                    '#title' => __( 'publicly_queryable', 'aione-app-builder' ),
                    '#description' => __( 'Whether post_type queries can be performed from the front end.', 'aione-app-builder' ) . '<br />' . __( 'Default: value of public argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'exclude_from_search' => array(
                    '#name' => 'ct[exclude_from_search]',
                    '#default_value' => !empty( $this->ct['exclude_from_search'] ),
                    '#title' => __( 'exclude_from_search', 'aione-app-builder' ),
                    '#description' => __( 'Whether to exclude posts with this post type from search results.', 'aione-app-builder' ) . '<br />' . __( 'Default: value of the opposite of the public argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'hierarchical' => array(
                    '#name' => 'ct[hierarchical]',
                    '#default_value' => !empty( $this->ct['hierarchical'] ),
                    '#title' => __( 'hierarchical', 'aione-app-builder' ),
                    '#description' => __( 'Whether the post type is hierarchical. Allows Parent to be specified.', 'aione-app-builder' ) . '<br />' . __( 'Default: false.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'can_export' => array(
                    '#name' => 'ct[can_export]',
                    '#default_value' => !empty( $this->ct['can_export'] ),
                    '#value' => !empty( $this->ct['can_export'] ),
                    '#title' => __( 'can_export', 'aione-app-builder' ),
                    '#description' => __( 'Can this post_type be exported.', 'aione-app-builder' ) . '<br />' . __( 'Default: true.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'show_in_nav_menus' => array(
                    '#name' => 'ct[show_in_nav_menus]',
                    '#default_value' => !empty( $this->ct['show_in_nav_menus'] ),
                    '#value' => !empty( $this->ct['show_in_nav_menus'] ),
                    '#title' => __( 'show_in_nav_menus', 'aione-app-builder' ),
                    '#description' => __( 'Whether post_type is available for selection in navigation menus.', 'aione-app-builder' ) . '<br />' . __( 'Default: value of public argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
            ),
        );
        $query_var = isset( $this->ct['query_var'] ) ? $this->ct['query_var'] : '';
        $hidden = !empty( $this->ct['query_var_enabled'] ) ? '' : ' class="hidden"';
        $form['query_var'] = array(
            '#type' => 'checkbox',
            '#name' => 'ct[query_var_enabled]',
            '#title' => 'query_var',
            '#description' => __( 'Disable to prevent queries like "mysite.com/?post_type=example". Enable to use queries like "mysite.com/?post_type=example". Enable and set a value to use queries like "mysite.com/?query_var_value=example"', 'aione-app-builder' ) . '<br />' . __( 'Default: true - set to $post_type.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['query_var_enabled'] ),
            '#after' => '<div id="aione-form-queryvar-toggle"' . $hidden . '><input type="text" name="ct[query_var]" value="' . $query_var . '" class="regular-text" /><div class="description aione-form-description aione-form-description-checkbox description-checkbox">' . __( 'Optional', 'aione-app-builder' ) . '. ' . __( 'String to customize query var', 'aione-app-builder' ) . '</div></div>',
            '#inline' => true,
        );
        $form['permalink_epmask'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[permalink_epmask]',
            '#title' => __( 'Permalink epmask', 'aione-app-builder' ),
            '#description' => sprintf( __( 'Default value EP_PERMALINK. More info here %s.', 'aione-app-builder' ),
            '<a href="http://core.trac.wordpress.org/ticket/12605" target="_blank">link</a>' ),
            '#value' => isset( $this->ct['permalink_epmask'] ) ? $this->ct['permalink_epmask'] : '',
            '#inline' => true,
        );

        $form['show_in_rest'] = array(
            '#type' => 'checkbox',
            '#name' => 'ct[show_in_rest]',
            '#default_value' => !empty( $this->ct['show_in_rest'] ),
            '#value' => !empty( $this->ct['show_in_rest'] ),
            '#title' => __( 'show_in_rest', 'aione-app-builder' ),
            '#description' => __( 'Whether to expose this post type in the REST API.', 'aione-app-builder' ) . '<br />' . __( 'Default: true.', 'aione-app-builder' ),
            '#inline' => true,
        );

        $form['rest_base'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[rest_base]',
            '#title' => __( 'Rest Base', 'aione-app-builder' ),
            '#description' => __( 'The base slug that this post type will use when accessed using the REST API.', 'aione-app-builder' ) . '<br />' . __( 'Default: $post_type.', 'aione-app-builder' ),
            '#value' => isset( $this->ct['rest_base'] ) ? $this->ct['rest_base'] : '',
            '#inline' => true,
        );

        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
    }

    public function box_template()
    {
        $form = array();
        $custom_templates = get_option(AIONE_OPTION_NAME_TEMPLATES, array());
        $options_single = array(
            __('Default Template') => 'single'
        );
        $options_archive = array(
            __('Default Template') => 'archive'
        );
        foreach ($custom_templates as $slug => $data) {
            if($data['template-type'] == 'single'){
                $options_single[$data['name']] = $slug;
            } else {
                $options_archive[$data['name']] = $slug;
            }
        }
        
        $form['single_template'] = array(
            '#type' => 'select',
            '#name' => 'ct[single_template]',
            '#title' => __( 'Select template for single view ', 'aione-app-builder' ),
            '#default_value' => isset( $this->ct['single_template'] ) ? $this->ct['single_template'] : '',
            // '#validate' => array('number' => array('value' => true)),
            '#inline' => true,
            '#pattern' => '<BEFORE><p><LABEL><ELEMENT><ERROR></p><AFTER>',
            '#options' => $options_single,
            '#before' => '<div class="misc-pub-section">',
            '#after' => '</div>',
            '#attributes' => array(
                'class' => 'js-aione-menu-position-after widefat',
                'data-aione-menu-position' => isset( $this->ct['single_template'] ) ? $this->ct['single_template'] : ''
            ),
        );

        $form['archive_template'] = array(
            '#type' => 'select',
            '#name' => 'ct[archive_template]',
            '#title' => __( 'Select template for achive view ', 'aione-app-builder' ),
            '#default_value' => isset( $this->ct['archive_template'] ) ? $this->ct['archive_template'] : '',
            // '#validate' => array('number' => array('value' => true)),
            '#inline' => true,
            '#pattern' => '<BEFORE><p><LABEL><ELEMENT><ERROR></p><AFTER>',
            '#options' => $options_archive,
            '#before' => '<div class="misc-pub-section">',
            '#after' => '</div>',
            '#attributes' => array(
                'class' => 'js-aione-menu-position-after widefat',
                'data-aione-menu-position' => isset( $this->ct['archive_template'] ) ? $this->ct['archive_template'] : ''
            ),
        );

        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();

    }

    public function box_admin_custom_columns(){ 
        global $aione; 
        $form = array();
        
        $component_slug = sanitize_text_field( $_GET['aione-component-slug'] );       
        $groups = acf_get_field_groups(array('post_type' => $component_slug));
        
        if(!empty($groups)){
            foreach ($groups as $key => $group) {
                $options = array();                
                $fields = acf_get_fields($group['key']);
                
                if(!empty($fields)){
                    foreach ( $fields as $field_key => $field ) {
                        $options[$field['key']] = array(
                            '#name' => 'ct[admin_custom_columns][' . $field['key'] . ']',
                            '#title' => $field['label'],
                            '#default_value' => ( ! empty( $this->ct['admin_custom_columns'][ $field['key'] ] ) ),
                            '#inline' => true,
                            '#before' => '<li>',
                            '#after' => '</li>',
                            '#attributes' => array(                
                                //'disabled' => 'disabled',
                            ),
                        );
                    }
                    $form['admin_custom_columns_'.$group['ID']] = array(
                        '#type' => 'checkboxes',
                        '#options' => $options,
                        '#name' => 'ct[admin_custom_columns]',
                        '#inline' => true,
                        '#before' => '<h4>'.$group['title'].'</h4> <ul class="aione-list">',
                        '#after' => '</ul>',
                        
                    );                    
                } // if fields
            }
           
            //echo "<pre>";print_r($form);echo "</pre>";
            $form = aione_form(__FUNCTION__, $form);
            echo $form->renderForm();
        } else {
            echo "No ACF group is applied to this component";
        }
    }

    public function filters(){ 
        global $aione; 
        $form = array();
        
        $component_slug = sanitize_text_field( $_GET['aione-component-slug'] );      
        $groups = acf_get_field_groups(array('post_type' => $component_slug));
        
        if(!empty($groups)){
            foreach ($groups as $key => $group) {
                $options = array();                
                $fields = acf_get_fields($group['key']);
                
                if(!empty($fields)){
                    foreach ( $fields as $field_key => $field ) {
                        $options[$field['key']] = array(
                            '#name' => 'ct[filters][' . $field['key'] . ']',
                            '#title' => $field['label'],
                            '#default_value' => ( ! empty( $this->ct['filters'][ $field['key'] ] ) ),
                            '#inline' => true,
                            '#before' => '<li>',
                            '#after' => '</li>',
                            '#attributes' => array(                
                                //'disabled' => 'disabled',
                            ),
                        );
                    }
                    $form['filters_'.$group['ID']] = array(
                        '#type' => 'checkboxes',
                        '#options' => $options,
                        '#name' => 'ct[filters]',
                        '#inline' => true,
                        '#before' => '<ul class="aione-list">',
                        '#after' => '</ul>',
                        
                    );                    
                } // if fields
            }
           
            //echo "<pre>";print_r($form);echo "</pre>";
            $form = aione_form(__FUNCTION__, $form);
            echo $form->renderForm();
        } else {
            echo "No ACF group is applied to this component";
        }
    }

    public function box_admin_columns_order(){
        $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $custom_types = $post_type_option->get_components();
        //echo "<pre>";print_r($custom_types);echo "</pre>";
        $component_slug = sanitize_text_field( $_GET['aione-component-slug'] );
        $custom_type = $custom_types[$component_slug];
        $screen = get_current_screen();
        /*echo "<pre>";print_r($screen);echo "</pre>";
        echo "<pre>";print_r($screen->id);echo "</pre>";*/
        /*$column_headers = get_column_headers();
        echo "<pre>";print_r($column_headers);echo "</pre>";*/
    }
    /**
     * post type properites
     */
    public function box_display_sections()
    {
        $form = array();
        $options = array(
            'title' => array(
                '#name' => 'ct[supports][title]',
                '#default_value' => !empty( $this->ct['supports']['title'] ),
                '#value' => !empty( $this->ct['supports']['title'] ),
                '#title' => __( 'Title', 'aione-app-builder' ),
                '#description' => __( 'Text input field to create a post title.', 'aione-app-builder' ),
                '#inline' => true,
                '#id' => 'aione-supports-title',
            ),
            'editor' => array(
                '#name' => 'ct[supports][editor]',
                '#default_value' => !empty( $this->ct['supports']['editor'] ),
                '#value' => !empty( $this->ct['supports']['editor'] ),
                '#title' => __( 'Editor', 'aione-app-builder' ),
                '#description' => __( 'Content input box for writing.', 'aione-app-builder' ),
                '#inline' => true,
                '#id' => 'aione-supports-editor',
            ),
            'comments' => array(
                '#name' => 'ct[supports][comments]',
                '#default_value' => !empty( $this->ct['supports']['comments'] ),
                '#title' => __( 'Comments', 'aione-app-builder' ),
                '#description' => __( 'Ability to turn comments on/off.', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'trackbacks' => array(
                '#name' => 'ct[supports][trackbacks]',
                '#default_value' => !empty( $this->ct['supports']['trackbacks'] ),
                '#title' => __( 'Trackbacks', 'aione-app-builder' ),
                '#description' => __( 'Ability to turn trackbacks and pingbacks on/off.', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'revisions' => array(
                '#name' => 'ct[supports][revisions]',
                '#default_value' => !empty( $this->ct['supports']['revisions'] ),
                '#title' => __( 'Revisions', 'aione-app-builder' ),
                '#description' => __( 'Allows revisions to be made of your post.', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'author' => array(
                '#name' => 'ct[supports][author]',
                '#default_value' => !empty( $this->ct['supports']['author'] ),
                '#title' => __( 'Author', 'aione-app-builder' ),
                '#description' => __( 'Displays a dropdown menu for changing the post author.', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'excerpt' => array(
                '#name' => 'ct[supports][excerpt]',
                '#default_value' => !empty( $this->ct['supports']['excerpt'] ),
                '#title' => __( 'Excerpt', 'aione-app-builder' ),
                '#description' => __( 'A text area for writing a custom excerpt.', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'thumbnail' => array(
                '#name' => 'ct[supports][thumbnail]',
                '#default_value' => !empty( $this->ct['supports']['thumbnail'] ),
                '#title' => __( 'Featured Image', 'aione-app-builder' ),
                '#description' => __( 'Allows to upload a featured image to the post.', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'custom-fields' => array(
                '#name' => 'ct[supports][custom-fields]',
                '#default_value' => !empty( $this->ct['supports']['custom-fields'] ),
                '#title' => __( 'Custom Fields', 'aione-app-builder' ),
                '#description' => __( "The native WordPress custom post fields list. If you don't select this, Types post fields will still display.", 'aione-app-builder' ),
                '#inline' => true,
            ),
            'page-attributes' => array(
                '#name' => 'ct[supports][page-attributes]',
                '#default_value' => !empty( $this->ct['supports']['page-attributes'] ),
                '#title' => __( 'Page Attributes', 'aione-app-builder' ),
                '#description' => __( 'Menu order and page parent (only available for hierarchical posts).', 'aione-app-builder' ),
                '#inline' => true,
            ),
            'post-formats' => array(
                '#name' => 'ct[supports][post-formats]',
                '#default_value' => !empty( $this->ct['supports']['post-formats'] ),
                '#title' => __( 'Post Formats', 'aione-app-builder' ),
                '#description' => __( 'A selector for the format to use for the post.', 'aione-app-builder' ),
                '#inline' => true,
            ),
        );
        $form['supports'] = array(
            '#type' => 'checkboxes',
            '#options' => $options,
            '#name' => 'ct[supports]',
            '#inline' => true,
        );
        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
    }

    /**
     * Labels
     */
    public function box_labels()
    {
        $form = array();
        $labels = array(
            'add_new' => array(
                'title' => __( 'Add New', 'aione-app-builder' ),
                'description' => __( 'The add new text. The default is Add New for both hierarchical and non-hierarchical types.', 'aione-app-builder' ),
                'label' => __('Add New', 'aione-app-builder'),
            ),
            'add_new_item' => array(
                'title' => __( 'Add New %s', 'aione-app-builder' ),
                'description' => __( 'The add new item text. Default is Add New Post/Add New Page.', 'aione-app-builder' ),
                'label' => __('Add New Item', 'aione-app-builder'),
            ),
            'edit_item' => array(
                'title' => __( 'Edit %s', 'aione-app-builder' ),
                'description' => __( 'The edit item text. Default is Edit Post/Edit Page.', 'aione-app-builder' ),
                'label' => __('Edit Item', 'aione-app-builder'),
            ),
            'new_item' => array(
                'title' => __( 'New %s', 'aione-app-builder' ),
                'description' => __( 'The new item text. Default is New Post/New Page.', 'aione-app-builder' ),
                'label' => __('New Item', 'aione-app-builder'),
            ),
            'view_item' => array(
                'title' => __( 'View %s', 'aione-app-builder' ),
                'description' => __( 'The view item text. Default is View Post/View Page.', 'aione-app-builder' ),
                'label' => __('View Item', 'aione-app-builder'),
            ),
            'search_items' => array(
                'title' => __( 'Search %s', 'aione-app-builder' ),
                'description' => __( 'The search items text. Default is Search Posts/Search Pages.', 'aione-app-builder' ),
                'label' => __('Search Items', 'aione-app-builder'),
            ),
            'not_found' => array(
                'title' => __( 'No %s found', 'aione-app-builder' ),
                'description' => __( 'The not found text. Default is No posts found/No pages found.', 'aione-app-builder' ),
                'label' => __('Not Found', 'aione-app-builder'),
            ),
            'not_found_in_trash' => array(
                'title' => __( 'No %s found in Trash', 'aione-app-builder' ),
                'description' => __( 'The not found in trash text. Default is No posts found in Trash/No pages found in Trash.', 'aione-app-builder' ),
                'label' => __('Not Found In Trash', 'aione-app-builder'),
            ),
            'parent_item_colon' => array(
                'title' => __( 'Parent text', 'aione-app-builder' ),
                'description' => __( "The parent text. This string isn't used on non-hierarchical types. In hierarchical ones the default is Parent Page.", 'aione-app-builder' ),
                'label' => __('Parent Description', 'aione-app-builder'),
            ),
            'all_items' => array(
                'title' => __( 'All items', 'aione-app-builder' ),
                'description' => __( 'The all items text used in the menu. Default is the Name label.', 'aione-app-builder' ),
                'label' => __('All Items', 'aione-app-builder'),
            ),
            'enter_title_here' => array(
                'title' => __( 'Enter title here', 'aione-app-builder' ),
                'description' => __( 'The text used as placeholder of post title. Default is the "Enter title here".', 'aione-app-builder' ),
                'label' => __('Enter title here', 'aione-app-builder'),
                'default_value' => __('Enter title here', 'aione-app-builder'),
                'force_if_empty' => true,
            ),
        );
        $form['table-open'] = array(
            '#type' => 'markup',
            '#markup' => '<table class="aione-form-table widefat striped fixed"><tbody>',
            '_builtin' => true,
        );
        foreach ( $labels as $name => $data ) {
            /**
             * get value
             */
            $value = empty($this->ct['slug'])? $data['title']:(isset( $this->ct['labels'][$name] ) ? $this->ct['labels'][$name] : '');
            /**
             * force if empty
             */
            if (
                true
                && empty($value)
                && isset($data['force_if_empty'])
                && isset($data['default_value'])
                && $data['force_if_empty']
            ) {
                $value = $data['default_value'];
            }
            $form['labels-' . $name] = array(
                '#type' => 'textfield',
                '#name' => 'ct[labels][' . $name . ']',
                '#title' => $data['label'],
                '#description' => $data['description'],
                '#value' => $value,
                '#inline' => true,
                '#pattern' => '<tr><td><LABEL></td><td><ELEMENT><DESCRIPTION></td></tr>',
                '#attributes' => array(
                    'class' => 'widefat',
                ),
            );
        }
        $form['table-close'] = array(
            '#type' => 'markup',
            '#markup' => '</tbody></table>',
            '_builtin' => true,
        );
        $form = aione_form(__FUNCTION__, $form);
        echo $form->renderForm();
    }

    /**
     * Render the content of the metabox "Taxonomies to be used with $post_type".
     *
     * @since unknown
     */
    public function box_taxonomies() {
	    $form = array();
	    $taxonomies = get_editable_taxonomies();
	    $options = array();

	    foreach( $taxonomies as $taxonomy_slug => $taxonomy ) {

		    $options[ $taxonomy_slug ] = array(
			    '#name' => 'ct[taxonomies][' . $taxonomy_slug . ']',
			    '#title' => $taxonomy->labels->name,
			    '#default_value' => ( ! empty( $this->ct['taxonomies'][ $taxonomy_slug ] ) ),
			    '#inline' => true,
			    '#before' => '<li>',
			    '#after' => '</li>',
		    );
		    $options[ $taxonomy_slug ]['_builtin'] = $taxonomy->_builtin;

	    }

	    $form['taxonomies'] = array(
		    '#type' => 'checkboxes',
		    '#options' => $options,
		    '#name' => 'ct[taxonomies]',
		    '#inline' => true,
		    '#before' => '<ul class="aione-list">',
		    '#after' => '</ul>',
		    '_builtin' => true,
	    );
	    $form = aione_form( __FUNCTION__, $form );
	    echo $form->renderForm();
    }


    private function save()
    {
        global $aione;

        if ( !isset( $_POST['ct'] ) ) {
            return false;
        }
        $data = $_POST['ct'];
        $update = false;
        
        // Sanitize data
        $data['labels']['name'] = isset( $data['labels']['name'] )
            ? sanitize_text_field( $data['labels']['name'] )
            : '';

        $data['labels']['singular_name'] = isset( $data['labels']['singular_name'] )
            ? sanitize_text_field( $data['labels']['singular_name'] )
            : '';

        if (
            empty( $data['labels']['name'] )
            || empty( $data['labels']['singular_name'] )
        ) {
            aione_admin_message( __( 'Please set post type name', 'aione-app-builder' ), 'error' );
            return false;
        }

        if ( isset( $data[$this->get_id] ) ) {
            $update = true;
            $data[$this->get_id] = sanitize_title( $data[$this->get_id] );
        } else {
            $data[$this->get_id] = null;
        }
        if ( isset( $data['slug'] ) ) {
            $data['slug'] = sanitize_title( $data['slug'] );
        } elseif(
            $_GET['aione-component-slug'] == 'post'
            || $_GET['aione-component-slug'] == 'page'
            || $_GET['aione-component-slug'] == 'attachment'
        ) {
            $data['slug'] = sanitize_text_field( $_GET['aione-component-slug'] );
        } else {
            $data['slug'] = null;
        }
        if ( isset( $data['rewrite']['slug'] ) ) {
            $data['rewrite']['slug'] = remove_accents( $data['rewrite']['slug'] );
            $data['rewrite']['slug'] = strtolower( $data['rewrite']['slug'] );
            $data['rewrite']['slug'] = trim( $data['rewrite']['slug'] );
        }
        $data['_builtin'] = false;

        // Set post type name
        $post_type = null;
        if ( !empty( $data['slug'] ) ) {
            $post_type = $data['slug'];
        } elseif ( !empty( $data[$this->get_id] ) ) {
            $post_type = $data[$this->get_id];
        } elseif ( !empty( $data['labels']['singular_name'] ) ) {
            $post_type = sanitize_title( $data['labels']['singular_name'] );
        }

        if ( empty( $post_type ) ) {
            aione_admin_message( __( 'Please set post type name', 'aione-app-builder' ), 'error' );
            return false;
        }

        $data['slug'] = $post_type;
	    $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $custom_types = $post_type_option->get_components();
        $protected_data_check = array();

        if ( aione_is_builtin_post_types($data['slug']) ) {
            $data['_builtin'] = true;
            $update = true;
        } else {
            // Check reserved name
            $reserved = $this->aione_is_reserved_name( $post_type, 'post_type' );
            if ( is_wp_error( $reserved ) ) {
                aione_admin_message( $reserved->get_error_message(), 'error' );
                return false;
            }

            // Check overwriting
            if ( ( !array_key_exists( $this->get_id, $data ) || $data[$this->get_id] != $post_type ) && array_key_exists( $post_type, $custom_types ) ) {
                aione_admin_message( __( 'Post Type already exists', 'aione-app-builder' ), 'error' );
                return false;
            }

            /*
             * Since Types 1.2
             * We do not allow plural and singular names to be same.
             */
            /*if ( $aione->post_types->check_singular_plural_match( $data ) ) {
                aione_admin_message( $aione->post_types->message( 'warning_singular_plural_match' ), 'error' );
                return false;
            }*/

            // Check if renaming then rename all post entries and delete old type

            if ( !empty( $data[$this->get_id] )
                && $data[$this->get_id] != $post_type ) {
                    global $wpdb;
                    $wpdb->update( $wpdb->posts, array('post_type' => $post_type),
                        array('post_type' => $data[$this->get_id]), array('%s'),
                        array('%s')
                    );

                    /**
                     * update post meta "_wp_types_group_post_types"
                     */
                    $sql = $wpdb->prepare(
                        "select meta_id, meta_value from {$wpdb->postmeta} where meta_key = %s",
                        '_wp_types_group_post_types'
                    );
                    $all_meta = $wpdb->get_results($sql, OBJECT_K);
                    $re = sprintf( '/,%s,/', $data[$this->get_id] );
                    foreach( $all_meta as $meta ) {
                        if ( !preg_match( $re, $meta->meta_value ) ) {
                            continue;
                        }
                        $wpdb->update(
                            $wpdb->postmeta,
                            array(
                                'meta_value' => preg_replace( $re, ','.$post_type.',', $meta->meta_value ),
                            ),
                            array(
                                'meta_id' => $meta->meta_id,
                            ),
                            array( '%s' ),
                            array( '%d' )
                        );
                    }

                    /**
                     * update _wpcf_belongs_{$data[$this->get_id]}_id
                     */
                    $wpdb->update(
                        $wpdb->postmeta,
                        array(
                            'meta_key' => sprintf( '_aione_belongs_%s_id', $post_type ),
                        ),
                        array(
                            'meta_key' => sprintf( '_aione_belongs_%s_id', $data[$this->get_id] ),
                        ),
                        array( '%s' ),
                        array( '%s' )
                    );

                    /**
                     * update options "wpv_options"
                     */
                    $wpv_options = get_option( 'wpv_options', true );
                    if ( is_array( $wpv_options ) ) {
                        $re = sprintf( '/(views_template_(archive_)?for_)%s/', $data[$this->get_id] );
                        foreach( $wpv_options as $key => $value ) {
                            if ( !preg_match( $re, $key ) ) {
                                continue;
                            }
                            unset($wpv_options[$key]);
                            $key = preg_replace( $re, "$1".$post_type, $key );
                            $wpv_options[$key] = $value;
                        }
                        update_option( 'wpv_options', $wpv_options );
                    }

                    /**
                     * update option "wpcf-custom-taxonomies"
                     */
                    $aione_custom_taxonomies = get_option( AIONE_OPTION_NAME_TAXONOMIES, array() );
                    if ( is_array( $aione_custom_taxonomies ) ) {
                        $update_aione_custom_taxonomies = false;
                        foreach( $aione_custom_taxonomies as $key => $value ) {
                            if ( array_key_exists( 'supports', $value ) && array_key_exists( $data[$this->get_id], $value['supports'] ) ) {
                                unset( $aione_custom_taxonomies[$key]['supports'][$data[$this->get_id]] );
                                $update_aione_custom_taxonomies = true;
                            }
                        }
                        if ( $update_aione_custom_taxonomies ) {
                            update_option( AIONE_OPTION_NAME_TAXONOMIES, $aione_custom_taxonomies );
                        }
                    }

                    // Sync action
                    do_action( 'aione_post_type_renamed', $post_type, $data[$this->get_id] );

                    // Set protected data
                    $protected_data_check = $custom_types[$data[$this->get_id]];
                    // Delete old type
                    unset( $custom_types[$data[$this->get_id]] );
                    $data[$this->get_id] = $post_type;
                } else {
                    // Set protected data
                    $protected_data_check = !empty( $custom_types[$post_type] ) ? $custom_types[$post_type] : array();
                }

            // Check if active
            if ( isset( $custom_types[$post_type]['disabled'] ) ) {
                $data['disabled'] = $custom_types[$post_type]['disabled'];
            }
        }

        // Sync taxes with custom taxes
        $taxes = get_option( AIONE_OPTION_NAME_TAXONOMIES, array() );

        foreach ( $taxes as $id => $tax ) {
            if ( isset( $data['taxonomies'] ) && !empty( $data['taxonomies'] ) && array_key_exists( $id, $data['taxonomies'] ) ) {
                $taxes[$id]['supports'][$data['slug']] = 1;
            } else {
                if( isset( $taxes[$id]['supports'][$data['slug']] ) )
                    unset( $taxes[$id]['supports'][$data['slug']] );
            }
        }

        update_option( AIONE_OPTION_NAME_TAXONOMIES, $taxes );

         /**
         * Sync with template
         */
        $aione_custom_templates = get_option( AIONE_OPTION_NAME_TEMPLATES, array() );
        foreach ( $aione_custom_templates as $slug => $template_array ) {
            if ( isset( $data['single_template'] ) && !empty( $data['single_template'] ) &&  ($slug == $data['single_template'] ) ) { 
                $aione_custom_templates[$slug]['component'][$data['slug']] = 1;
            } else if ( isset( $data['archive_template'] ) && !empty( $data['archive_template'] ) &&  ($slug == $data['archive_template'] ) ) {
                $aione_custom_templates[$slug]['component'][$data['slug']] = 1;
            } else {
                if( isset( $aione_custom_templates[$slug]['component'][$data['slug']] ) )
                    unset( $aione_custom_templates[$slug]['component'][$data['slug']] );
            }
        }
        
        update_option(AIONE_OPTION_NAME_TEMPLATES, $aione_custom_templates);

        // Preserve protected data
        foreach ( $protected_data_check as $key => $value ) {
            if ( strpos( $key, '_' ) !== 0 ) {
                unset( $protected_data_check[$key] );
            }
        }
      

        /**
         * set last edit time
         */
        $data[AIONE_EDIT_LAST] = time();

        /**
         * set last edit author
         */

        $data[AIONE_AUTHOR] = get_current_user_id();

        /**
         * set single view template
         */

        $data['single_template'] = $data['single_template'];

        /**
         * set archive view template
         */

        $data['archive_template'] = $data['archive_template'];

        /**
         * add builid in
         */
        if ( $data['_builtin'] && !isset( $protected_data_check[$data['slug']])) {
            $protected_data_check[$data['slug']] = array();
        }

        // Merging protected data
        $custom_types[$post_type] = array_merge( $protected_data_check, $data );

        update_option( AIONE_OPTION_NAME_COMPONENTS, $custom_types );

        // WPML register strings
        if ( !$data['_builtin'] ) {
            //aione_custom_types_register_translation( $post_type, $data );
        }

        // success message
        $msg = $update
            ? __( 'Post Type saved.', 'aione-app-builder' )
            : __( 'New Post Type created.', 'aione-app-builder' );

        aione_admin_message_store(
            $msg,
            'updated notice notice-success is-dismissible'
        );

	    flush_rewrite_rules();

        if ( !$data['_builtin'] ) {
            do_action( 'aione_custom_types_save', $data );
        }

        // Redirect
        wp_safe_redirect(
            esc_url_raw(
                add_query_arg(
                    array(
                        'page' => 'aione-edit-component',
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
        if ( 'aione-app-builder_page_aione-edit-component' != $screen_base ) {
            return;
        }
        $option_name = sprintf('closedpostboxes_%s', $screen_base);
        $closedpostboxes = get_user_meta(get_current_user_id(), $option_name);
        if ( !empty($closedpostboxes) ) {
            return;
        }
        $closedpostboxes[] = 'types_labels';
        $closedpostboxes[] = 'types_options';
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
    public function prepare_field_select_screen()
    {
        /**
         * check nonce
         */
        if (
            0
            || !isset($_REQUEST['_wpnonce'])
            || !isset($_REQUEST['parent'])
            || !isset($_REQUEST['child'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], $this->get_nonce('child-post-fields', $_REQUEST['parent'], $_REQUEST['child']))
        ) {
            $this->verification_failed_and_die();
        }
        $parent = $_REQUEST['parent'];
        $child = $_REQUEST['child'];

        $post_type_parent = get_post_type_object( $parent );
        $post_type_child = get_post_type_object( $child );

        if ( null == $post_type_parent || null == $post_type_child ) {
            die( __( 'Wrong post types', 'aione-app-builder' ) );
        }
        $relationships = get_option( 'aione_post_relationship', array() );
        if ( !isset( $relationships[$parent][$child] ) ) {
            $this->print_notice_and_die(
                __( 'Please save Post Type first to edit these fields.', 'aione-app-builder' )
            );
        }
        $repetitive_warning_markup = array();
        $data = $relationships[$parent][$child];

        $form = array();
        $form['repetitive_warning_markup'] = $repetitive_warning_markup;
        $form['select'] = array(
            '#type' => 'radios',
            '#name' => 'fields_setting',
            '#options' => array(
                __( 'Title, all custom fields and parents', 'aione-app-builder' ) => 'all_cf',
                __( 'Do not show management options for this post type', 'aione-app-builder' ) => 'only_list',
                __( 'All fields, including the standard post fields', 'aione-app-builder' ) => 'all_cf_standard',
                __( 'Specific fields', 'aione-app-builder' ) => 'specific',
            ),
            '#attributes' => array(
                'display' => 'ul',
            ),
            '#default_value' => empty( $data['fields_setting'] ) ? 'all_cf' : $data['fields_setting'],
        );
        /**
         * check default, to avoid missing configuration
         */
        if ( !in_array($form['select']['#default_value'], $form['select']['#options']) ) {
            $form['select']['#default_value'] = 'all_cf';
        }
        /**
         * Specific options
         */
        $groups = aione_admin_get_groups_by_post_type( $child );
        $options_cf = array();
        $repetitive_warning = false;
        $repetitive_warning_txt = __( 'Repeating fields should not be used in child posts. Types will update all field values.', 'aione-app-builder' );
        foreach ( $groups as $group ) {
            $fields = aione_admin_fields_get_fields_by_group( $group['id'] );
            foreach ( $fields as $key => $cf ) {
                $__key = aione_types_cf_under_control( 'check_outsider', $key ) ? $key : WPCF_META_PREFIX . $key;
                $options_cf[$__key] = array(
                    '#title' => $cf['name'],
                    '#name' => 'fields[' . $__key . ']',
                    '#default_value' => isset( $data['fields'][$__key] ) ? 1 : 0,
                    '#inline' => true,
                    '#before' => '<li>',
                    '#after' => '</li>',
                );
                // Repetitive warning
                if ( aione_admin_is_repetitive( $cf ) ) {
                    if ( !$repetitive_warning ) {
                        $repetitive_warning_markup = array(
                            '#type' => 'markup',
                            '#markup' => '<div class="message error" style="display:none;" id="aione-repetitive-warning"><p>' . $repetitive_warning_txt . '</p></div>',
                        );
                    }
                    $repetitive_warning = true;
                    $options_cf[$__key]['#after'] = !isset( $data['fields'][$__key] ) ? '<div class="message error" style="display:none;"><p>' : '<div class="message error"><p>';
                    $options_cf[$__key]['#after'] .= $repetitive_warning_txt;
                    $options_cf[$__key]['#after'] .= '</p></div></li>';
                    $options_cf[$__key]['#attributes'] = array(
                        'onclick' => 'jQuery(this).parent().find(\'.message\').toggle();',
                        'disabled' => 'disabled',
                    );
                }
            }
        }

        /**
         * build options for "Specific fields"
         */
        $options = array();
        /**
         * check and add built-in properites
         */
       /* require_once WPCF_INC_ABSPATH . '/post-relationship.php';
        $supports= wpcf_post_relationship_get_supported_fields_by_post_type($child);
        foreach ( $supports as $child_field_key => $child_field_data ) {
            $options[$child_field_data['name']] = array(
                '#title' => $child_field_data['title'],
                '#name' => sprintf('fields[%s]', $child_field_data['name']),
                '#default_value' => isset( $data['fields'][$child_field_data['name']] ) ? 1 : 0,
                '#inline' => true,
                '#before' => '<li>',
                '#after' => '</li>',
            );
        }*/

        /**
         * add custom fields
         */
        $options = $options + $options_cf;
        $temp_belongs = aione_pr_admin_get_belongs( $child );
        foreach ( $temp_belongs as $temp_parent => $temp_data ) {
            if ( $temp_parent == $parent ) {
                continue;
            }
            $temp_parent_type = get_post_type_object( $temp_parent );
            $options[$temp_parent] = array(
                '#title' => $temp_parent_type->label,
                '#name' => 'fields[_aione_pr_parents][' . $temp_parent . ']',
                '#default_value' => isset( $data['fields']['_aione_pr_parents'][$temp_parent] ) ? 1 : 0,
                '#inline' => true,
                '#before' => '<li>',
                '#after' => '</li>',
            );
        }
        /**
         * remove "Specific fields" if there is no fields
         */
        if ( empty($options) ) {
            unset($form['select']['#options'][__('Specific fields', 'aione-app-builder')]);
            if ('specific' == $form['select']['#default_value']) {
                $form['select']['#default_value'] = 'all_cf';
            }
        }

        // Taxonomies
        $taxonomies = get_object_taxonomies( $post_type_child->name, 'objects' );
        if ( !empty( $taxonomies ) ) {
            foreach ( $taxonomies as $tax_id => $taxonomy ) {
                $options[$tax_id] = array(
                    '#title' => sprintf( __('Taxonomy - %s', 'aione-app-builder'), $taxonomy->label ),
                    '#name' => 'fields[_aione_pr_taxonomies][' . $tax_id . ']',
                    '#default_value' => isset( $data['fields']['_aione_pr_taxonomies'][$tax_id] ) ? 1 : 0,
                    '#inline' => true,
                    '#before' => '<li>',
                    '#after' => '</li>',
                );
            }
        }

        $form['specific'] = array(
            '#type' => 'checkboxes',
            '#name' => 'fields',
            '#options' => $options,
            '#default_value' => isset( $data['fields'] ),
            '#before' => sprintf(
                '<ul id="aione-specific" class="%s">',
                'specific' == $form['select']['#default_value']? '':'hidden'
            ),
            '#after' => '</ul>',
        );
        $form['nonce'] = array(
            '#type' => 'hidden',
            '#value' => wp_create_nonce($this->get_nonce('child-post-fields-save', $parent, $child)),
            '#name' => 'aione-fields-save-nonce',
            '#id' => 'aione-fields-save-nonce',
        );
        $form['parent'] = array(
            '#type' => 'hidden',
            '#value' => esc_attr($parent),
            '#name' => 'aione-parent',
            '#id' => 'aione-parent',
        );
        $form['child'] = array(
            '#type' => 'hidden',
            '#value' => esc_attr($child),
            '#name' => 'aione-child',
            '#id' => 'aione-child',
        );
        echo aione_form_simple( $form );
        die;
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
    public function save_child_fields()
    {
        /**
         * check nonce
         */
        if (
            0
            || !isset($_REQUEST['_wpnonce'])
            || !isset($_REQUEST['current'])
            || !isset($_REQUEST['parent'])
            || !isset($_REQUEST['child'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], $this->get_nonce('child-post-fields-save', $_REQUEST['parent'], $_REQUEST['child']))
        ) {
            $this->verification_failed_and_die();
        }
        $parent = $_REQUEST['parent'];
        $child = $_REQUEST['child'];
        $fields = array();
        parse_str($_REQUEST['current'], $fields);

        $relationships = get_option( 'aione_post_relationship', array() );
        $relationships[$parent][$child]['fields_setting'] = sanitize_text_field( $fields['fields_setting'] );
        /**
         * sanitize
         */
        /*require_once WPCF_INC_ABSPATH . '/post-relationship.php';
        $relationships[$parent][$child]['fields'] = array();
        if (  isset( $fields['fields'] ) && is_array($fields['fields'])) {
            $allowed_keys = wpcf_post_relationship_get_specific_fields_keys($child);
            foreach( $fields['fields'] as $key => $value ) {

                // other parent cpts
                if ( '_wpcf_pr_parents' == $key ) {
                    $relationships[$parent][$child]['fields'][$key] = array();
                    foreach( array_keys($value) as $parents) {
                        $relationships[$parent][$child]['fields'][$key][$parents] = 1;
                    }
                }

                
                if ( '_wpcf_pr_taxonomies' == $key ) {
                    if ( is_array($value) ) {
                        $relationships[$parent][$child]['fields'][$key] = array();
                        foreach( array_keys($value) as $taxonomy) {
                            $taxonomy = get_taxonomy($taxonomy);
                            if ( is_object($taxonomy) ) {
                                $relationships[$parent][$child]['fields'][$key][$taxonomy->name] = 1;
                            }
                        }
                    }
                    continue;
                }
                if ( array_key_exists( $key, $allowed_keys) ) {
                    $relationships[$parent][$child]['fields'][$key] = 1;
                }
            }
        }*/
        update_option( 'aione_post_relationship', $relationships );
        die;
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
    public function get_icons_list(){
        /**
         * check nonce
         */
        
        if (
            0
            || !isset($_REQUEST['_wpnonce'])
            || !wp_verify_nonce($_REQUEST['_wpnonce'], $this->get_nonce('post-type-dashicons-list'))
        ) {
            $this->verification_failed_and_die();
        }
        $icons = array(
            'admin-appearance' => __('appearance', 'aione-app-builder'),
            'admin-collapse' => __('collapse', 'aione-app-builder'),
            'admin-comments' => __('comments', 'aione-app-builder'),
            'admin-generic' => __('generic', 'aione-app-builder'),
            'admin-home' => __('home', 'aione-app-builder'),
            'admin-links' => __('links', 'aione-app-builder'),
            'admin-media' => __('media', 'aione-app-builder'),
            'admin-network' => __('network', 'aione-app-builder'),
            'admin-page' => __('page', 'aione-app-builder'),
            'admin-plugins' => __('plugins', 'aione-app-builder'),
            'admin-post' => __('post', 'aione-app-builder'),
            'admin-settings' => __('settings', 'aione-app-builder'),
            'admin-site' => __('site', 'aione-app-builder'),
            'admin-tools' => __('tools', 'aione-app-builder'),
            'admin-users' => __('users', 'aione-app-builder'),
            'album' => __('album', 'aione-app-builder'),
            'align-center' => __('align center', 'aione-app-builder'),
            'align-left' => __('align left', 'aione-app-builder'),
            'align-none' => __('align none', 'aione-app-builder'),
            'align-right' => __('align right', 'aione-app-builder'),
            'analytics' => __('analytics', 'aione-app-builder'),
            'archive' => __('archive', 'aione-app-builder'),
            'arrow-down-alt2' => __('down alt2', 'aione-app-builder'),
            'arrow-down-alt' => __('down alt', 'aione-app-builder'),
            'arrow-down' => __('down', 'aione-app-builder'),
            'arrow-left-alt2' => __('left alt2', 'aione-app-builder'),
            'arrow-left-alt' => __('left alt', 'aione-app-builder'),
            'arrow-left' => __('left', 'aione-app-builder'),
            'arrow-right-alt2' => __('right alt2', 'aione-app-builder'),
            'arrow-right-alt' => __('right alt', 'aione-app-builder'),
            'arrow-right' => __('right', 'aione-app-builder'),
            'arrow-up-alt2' => __('up alt2', 'aione-app-builder'),
            'arrow-up-alt' => __('up alt', 'aione-app-builder'),
            'arrow-up' => __('up', 'aione-app-builder'),
            'art' => __('art', 'aione-app-builder'),
            'awards' => __('awards', 'aione-app-builder'),
            'backup' => __('backup', 'aione-app-builder'),
            'book-alt' => __('book alt', 'aione-app-builder'),
            'book' => __('book', 'aione-app-builder'),
            'building' => __('building', 'aione-app-builder'),
            'businessman' => __('businessman', 'aione-app-builder'),
            'calendar-alt' => __('calendar alt', 'aione-app-builder'),
            'calendar' => __('calendar', 'aione-app-builder'),
            'camera' => __('camera', 'aione-app-builder'),
            'carrot' => __('carrot', 'aione-app-builder'),
            'cart' => __('cart', 'aione-app-builder'),
            'category' => __('category', 'aione-app-builder'),
            'chart-area' => __('chart area', 'aione-app-builder'),
            'chart-bar' => __('chart bar', 'aione-app-builder'),
            'chart-line' => __('chart line', 'aione-app-builder'),
            'chart-pie' => __('chart pie', 'aione-app-builder'),
            'clipboard' => __('clipboard', 'aione-app-builder'),
            'clock' => __('clock', 'aione-app-builder'),
            'cloud' => __('cloud', 'aione-app-builder'),
            'controls-back' => __('back', 'aione-app-builder'),
            'controls-forward' => __('forward', 'aione-app-builder'),
            'controls-pause' => __('pause', 'aione-app-builder'),
            'controls-play' => __('play', 'aione-app-builder'),
            'controls-repeat' => __('repeat', 'aione-app-builder'),
            'controls-skipback' => __('skip back', 'aione-app-builder'),
            'controls-skipforward' => __('skip forward', 'aione-app-builder'),
            'controls-volumeoff' => __('volume off', 'aione-app-builder'),
            'controls-volumeon' => __('volume on', 'aione-app-builder'),
            'dashboard' => __('dashboard', 'aione-app-builder'),
            'desktop' => __('desktop', 'aione-app-builder'),
            'dismiss' => __('dismiss', 'aione-app-builder'),
            'download' => __('download', 'aione-app-builder'),
            'editor-aligncenter' => __('align center', 'aione-app-builder'),
            'editor-alignleft' => __('align left', 'aione-app-builder'),
            'editor-alignright' => __('align right', 'aione-app-builder'),
            'editor-bold' => __('bold', 'aione-app-builder'),
            'editor-break' => __('break', 'aione-app-builder'),
            'editor-code' => __('code', 'aione-app-builder'),
            'editor-contract' => __('contract', 'aione-app-builder'),
            'editor-customchar' => __('custom char', 'aione-app-builder'),
            'editor-distractionfree' => __('distraction free', 'aione-app-builder'),
            'editor-expand' => __('expand', 'aione-app-builder'),
            'editor-help' => __('help', 'aione-app-builder'),
            'editor-indent' => __('indent', 'aione-app-builder'),
            'editor-insertmore' => __('insert more', 'aione-app-builder'),
            'editor-italic' => __('italic', 'aione-app-builder'),
            'editor-justify' => __('justify', 'aione-app-builder'),
            'editor-kitchensink' => __('kitchen sink', 'aione-app-builder'),
            'editor-ol' => __('ol', 'aione-app-builder'),
            'editor-outdent' => __('outdent', 'aione-app-builder'),
            'editor-paragraph' => __('paragraph', 'aione-app-builder'),
            'editor-paste-text' => __('paste text', 'aione-app-builder'),
            'editor-paste-word' => __('paste word', 'aione-app-builder'),
            'editor-quote' => __('quote', 'aione-app-builder'),
            'editor-removeformatting' => __('remove formatting', 'aione-app-builder'),
            'editor-rtl' => __('rtl', 'aione-app-builder'),
            'editor-spellcheck' => __('spellcheck', 'aione-app-builder'),
            'editor-strikethrough' => __('strike through', 'aione-app-builder'),
            'editor-textcolor' => __('text color', 'aione-app-builder'),
            'editor-ul' => __('ul', 'aione-app-builder'),
            'editor-underline' => __('underline', 'aione-app-builder'),
            'editor-unlink' => __('unlink', 'aione-app-builder'),
            'editor-video' => __('video', 'aione-app-builder'),
            'edit' => __('edit', 'aione-app-builder'),
            'email-alt' => __('email alt', 'aione-app-builder'),
            'email' => __('email', 'aione-app-builder'),
            'excerpt-view' => __('excerpt view', 'aione-app-builder'),


            'external' => __('external', 'aione-app-builder'),
            'facebook-alt' => __('facebook alt', 'aione-app-builder'),
            'facebook' => __('facebook', 'aione-app-builder'),
            'feedback' => __('feedback', 'aione-app-builder'),
            'flag' => __('flag', 'aione-app-builder'),
            'format-aside' => __('aside', 'aione-app-builder'),
            'format-audio' => __('audio', 'aione-app-builder'),
            'format-chat' => __('chat', 'aione-app-builder'),
            'format-gallery' => __('gallery', 'aione-app-builder'),
            'format-image' => __('image', 'aione-app-builder'),
            'format-links' => __('links', 'aione-app-builder'),
            'format-quote' => __('quote', 'aione-app-builder'),
            'format-standard' => __('standard', 'aione-app-builder'),
            'format-status' => __('status', 'aione-app-builder'),
            'format-video' => __('video', 'aione-app-builder'),
            'forms' => __('forms', 'aione-app-builder'),
            'googleplus' => __('google plus', 'aione-app-builder'),
            'grid-view' => __('grid view', 'aione-app-builder'),
            'groups' => __('groups', 'aione-app-builder'),
            'hammer' => __('hammer', 'aione-app-builder'),
            'heart' => __('heart', 'aione-app-builder'),
            'id-alt' => __('id alt', 'aione-app-builder'),
            'id' => __('id', 'aione-app-builder'),
            'images-alt2' => __('images alt2', 'aione-app-builder'),
            'images-alt' => __('images alt', 'aione-app-builder'),
            'image-crop' => __('image crop', 'aione-app-builder'),
            'image-flip-horizontal' => __('image flip horizontal', 'aione-app-builder'),
            'image-flip-vertical' => __('image flip vertical', 'aione-app-builder'),
            'image-rotate-left' => __('image rotate left', 'aione-app-builder'),
            'image-rotate-right' => __('image rotate right', 'aione-app-builder'),
            'index-card' => __('index card', 'aione-app-builder'),
            'info' => __('info', 'aione-app-builder'),
            'leftright' => __('left right', 'aione-app-builder'),
            'lightbulb' => __('light bulb', 'aione-app-builder'),
            'list-view' => __('list view', 'aione-app-builder'),
            'location-alt' => __('location alt', 'aione-app-builder'),
            'location' => __('location', 'aione-app-builder'),
            'lock' => __('lock', 'aione-app-builder'),
            'marker' => __('marker', 'aione-app-builder'),
            'media-archive' => __('media archive', 'aione-app-builder'),
            'media-audio' => __('media audio', 'aione-app-builder'),
            'media-code' => __('media code', 'aione-app-builder'),
            'media-default' => __('media default', 'aione-app-builder'),
            'media-document' => __('media document', 'aione-app-builder'),
            'media-interactive' => __('media interactive', 'aione-app-builder'),
            'media-spreadsheet' => __('media spreadsheet', 'aione-app-builder'),
            'media-text' => __('media text', 'aione-app-builder'),
            'media-video' => __('media video', 'aione-app-builder'),
            'megaphone' => __('megaphone', 'aione-app-builder'),
            'menu' => __('menu', 'aione-app-builder'),
            'microphone' => __('microphone', 'aione-app-builder'),
            'migrate' => __('migrate', 'aione-app-builder'),
            'minus' => __('minus', 'aione-app-builder'),
            'money' => __('money', 'aione-app-builder'),
            'nametag' => __('name tag', 'aione-app-builder'),
            'networking' => __('networking', 'aione-app-builder'),
            'no-alt' => __('no alt', 'aione-app-builder'),
            'no' => __('no', 'aione-app-builder'),
            'palmtree' => __('palm tree', 'aione-app-builder'),
            'performance' => __('performance', 'aione-app-builder'),
            'phone' => __('phone', 'aione-app-builder'),
            'playlist-audio' => __('playlist audio', 'aione-app-builder'),
            'playlist-video' => __('playlist video', 'aione-app-builder'),
            'plus-alt' => __('plus alt', 'aione-app-builder'),
            'plus' => __('plus', 'aione-app-builder'),
            'portfolio' => __('portfolio', 'aione-app-builder'),
            'post-status' => __('post status', 'aione-app-builder'),
            'post-trash' => __('post trash', 'aione-app-builder'),
            'pressthis' => __('press this', 'aione-app-builder'),
            'products' => __('products', 'aione-app-builder'),
            'randomize' => __('randomize', 'aione-app-builder'),
            'redo' => __('redo', 'aione-app-builder'),
            'rss' => __('rss', 'aione-app-builder'),
            'schedule' => __('schedule', 'aione-app-builder'),
            'screenoptions' => __('screen options', 'aione-app-builder'),
            'search' => __('search', 'aione-app-builder'),
            'share1' => __('share1', 'aione-app-builder'),
            'share-alt2' => __('share alt2', 'aione-app-builder'),
            'share-alt' => __('share alt', 'aione-app-builder'),
            'share' => __('share', 'aione-app-builder'),
            'shield-alt' => __('shield alt', 'aione-app-builder'),
            'shield' => __('shield', 'aione-app-builder'),
            'slides' => __('slides', 'aione-app-builder'),
            'smartphone' => __('smartphone', 'aione-app-builder'),
            'smiley' => __('smiley', 'aione-app-builder'),
            'sort' => __('sort', 'aione-app-builder'),
            'sos' => __('sos', 'aione-app-builder'),
            'star-empty' => __('star empty', 'aione-app-builder'),
            'star-filled' => __('star filled', 'aione-app-builder'),
            'star-half' => __('star half', 'aione-app-builder'),
            'store' => __('store', 'aione-app-builder'),
            'tablet' => __('tablet', 'aione-app-builder'),
            'tagcloud' => __('tag cloud', 'aione-app-builder'),
            'tag' => __('tag', 'aione-app-builder'),
            'testimonial' => __('testimonial', 'aione-app-builder'),
            'text' => __('text', 'aione-app-builder'),
            'tickets-alt' => __('tickets alt', 'aione-app-builder'),
            'tickets' => __('tickets', 'aione-app-builder'),
            'translation' => __('translation', 'aione-app-builder'),
            'trash' => __('trash', 'aione-app-builder'),
            'twitter' => __('twitter', 'aione-app-builder'),
            'undo' => __('undo', 'aione-app-builder'),
            'universal-access-alt' => __('universal access alt', 'aione-app-builder'),
            'universal-access' => __('universal access', 'aione-app-builder'),
            'update' => __('update', 'aione-app-builder'),
            'upload' => __('upload', 'aione-app-builder'),
            'vault' => __('vault', 'aione-app-builder'),
            'video-alt2' => __('video alt2', 'aione-app-builder'),
            'video-alt3' => __('video alt3', 'aione-app-builder'),
            'video-alt' => __('video alt', 'aione-app-builder'),
            'visibility' => __('visibility', 'aione-app-builder'),
            'welcome-add-page' => __('add page', 'aione-app-builder'),
            'welcome-comments' => __('comments', 'aione-app-builder'),
            'welcome-edit-page' => __('edit page', 'aione-app-builder'),
            'welcome-learn-more' => __('learn more', 'aione-app-builder'),
            'welcome-view-site' => __('view site', 'aione-app-builder'),
            'welcome-widgets-menus' => __('widgets menus', 'aione-app-builder'),
            'welcome-write-blog' => __('write blog', 'aione-app-builder'),
            'wordpress-alt' => __('wordpress alt', 'aione-app-builder'),
            'wordpress' => __('wordpress', 'aione-app-builder'),
            'yes' => __('yes', 'aione-app-builder'),
        );
        printf(
            '<p><input type="text" class="js-aione-search large-text" placeholder="%s" /</p>',
            esc_attr__('Search', 'aione-app-builder')
        );
        $current = isset($_REQUEST['slug']) && is_string($_REQUEST['slug'])? $_REQUEST['slug']:'';
        echo '<ul>';
        foreach ( $icons as $slug => $title ) {
            printf(
                '<li data-aione-icon="%s" class="%s"><a href="#" data-aione-icon="%s"><span class="dashicons-before dashicons-%s">%s</span></a></li>',
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


    /**
     * Checks if name is reserved.
     *
     * @param type $name
     * @return type
     */
    function aione_is_reserved_name($name, $context, $check_pages = true)
    {
        $name = strval( $name );
        /*
         *
         * If name is empty string skip page cause there might be some pages without name
         */
        if ( $check_pages && !empty( $name ) ) {
            global $wpdb;
            $page = $wpdb->get_var(
                $wpdb->prepare(
                    "SELECT ID FROM $wpdb->posts WHERE post_name = %s AND post_type='page'",
                    sanitize_title( $name )
                )
            );
            if ( !empty( $page ) ) {
                return new WP_Error( 'aione_reserved_name', __( 'You cannot use this slug because there is already a page by that name. Please choose a different slug.',
                                        'aione-app-builder' ) );
            }
        }

        // Add custom types
        $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $custom_types = $post_type_option->get_components();
        $post_types = get_post_types();
        if ( !empty( $custom_types ) ) {
            $custom_types = array_keys( $custom_types );
            $post_types = array_merge( array_combine( $custom_types, $custom_types ),
                    $post_types );
        }
        // Unset to avoid checking itself
        /* Note: This will unset any post type with the same slug, so it's possible to overwrite it
        if ( $context == 'post_type' && isset( $post_types[$name] ) ) {
            unset( $post_types[$name] );
        }
        */
        // abort test...
        
        if( $context == 'post_type' // ... for post type ...
            && isset( $_POST['ct']['aione-component-slug'] ) // ... if it's an already saved taxonomy ...
            && $_POST['ct']['aione-component-slug'] == $name // ... and the slug didn't changed.
        ) {
            return false;
        }

        // Add taxonomies
        $custom_taxonomies = (array) get_option( AIONE_OPTION_NAME_TAXONOMIES, array() );
        $taxonomies = get_taxonomies();
        if ( !empty( $custom_taxonomies ) ) {
            $custom_taxonomies = array_keys( $custom_taxonomies );
            $taxonomies = array_merge( array_combine( $custom_taxonomies,
                            $custom_taxonomies ), $taxonomies );
        }

        // Unset to avoid checking itself
        /* Note: This will unset any taxonomy with the same slug, so it's possible to overwrite it
        if ( $context == 'taxonomy' && isset( $taxonomies[$name] ) ) {
            unset( $taxonomies[$name] );
        }
        */

        // abort test...
        if( $context == 'taxonomy' // ... for taxonomy ...
            && isset( $_POST['ct']['aione-taxonomy'] ) // ... if it's an already saved taxonomy ...
            && $_POST['ct']['aione-taxonomy'] == $name // ... and the slug didn't changed.
        ) {
            return false;
        }

        $reserved_names = $this->aione_reserved_names();
        $reserved = array_merge( array_combine( $reserved_names, $reserved_names ),
                array_merge( $post_types, $taxonomies ) );

        return in_array( $name, $reserved ) ? new WP_Error( 'aione_reserved_name', __( 'You cannot use this slug because it is already used by WordPress. Please choose a different slug.',
                                'aione-app-builder' ) ) : false;
    }

    function aione_reserved_names()    {
        $reserved = get_reserved_terms();
        $reserved[] = 'action';

        return apply_filters( 'aione_reserved_names', $reserved );
    }

    function aione_ajax_delete_component_callback(){
        //$fallthrough = true;
        /*if ( !(isset($_REQUEST['_wpnonce']) && wp_verify_nonce($_REQUEST['_wpnonce'], $_REQUEST['aione_action']))) {
            if( $fallthrough ) {
                return true;
            } else {
                die();
            }
        }*/

        $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        switch ($_REQUEST['aione_action']){
            case 'delete_component':
                $post_type = $this->aione_ajax_helper_get_post_type();
                if ( empty($post_type) ) {
                    aione_ajax_helper_print_error_and_die();
                }
                $post_types = $post_type_option->get_components();

                /**
                 * Delete relation between custom posts types
                 *
                 * Filter allow to delete all custom fields used to make
                 * a relation between posts.
                 *
                 * @since 1.6.4
                 *
                 * @param bool   $delete True or false flag to delete relationships.
                 * @param string $var Currently deleted post type.
                 */
                if ( apply_filters('aione_delete_relation_meta', false, $post_type) ) {
                    global $wpdb;
                    $wpdb->delete(
                        $wpdb->postmeta,
                        array( 'meta_key' => sprintf( '_aione_belongs_%s_id', $post_type ) ),
                        array( '%s' )
                    );
                }

                unset($post_types[$post_type]);
                /**
                 * remove post relation
                 */
                foreach ( array_keys($post_types) as $post_type ) {
                    if ( array_key_exists( 'post_relationship', $post_types[$post_type] ) ) {
                        /**
                         * remove "has" relation
                         */
                        if (
                            array_key_exists( 'has', $post_types[$post_type]['post_relationship'] )
                            && array_key_exists( $post_type, $post_types[$post_type]['post_relationship']['has'] )
                        ) {
                            unset($post_types[$post_type]['post_relationship']['has'][$post_type]);
                            $post_types[$post_type][AIONE_EDIT_LAST] = time();
                        }
                        /**
                         * remove "belongs" relation
                         */
                        if (
                            array_key_exists( 'belongs', $post_types[$post_type]['post_relationship'] )
                            && array_key_exists( $post_type, $post_types[$post_type]['post_relationship']['belongs'] )
                        ) {
                            unset($post_types[$post_type]['post_relationship']['belongs'][$post_type]);
                            $post_types[$post_type][AIONE_EDIT_LAST] = time();
                        }
                    }
                }
                update_option(AIONE_OPTION_NAME_COMPONENTS, $post_types);
                aione_admin_deactivate_content('post_type', $post_type);
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
        /*if( ! $fallthrough ) {
            die();
        }

        return $fallthrough;*/
    }

    function aione_ajax_helper_get_post_type(){
        if (!isset($_REQUEST['aione-component-slug']) || empty($_REQUEST['aione-component-slug'])) {
            return false;
        }
        $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $custom_types = $post_type_option->get_components();
        if (
            isset($custom_types[$_REQUEST['aione-component-slug']])
            && isset($custom_types[$_REQUEST['aione-component-slug']]['slug'])
        ) {
            return $custom_types[$_REQUEST['aione-component-slug']]['slug'];
        }
        return false;
    }

    
}

