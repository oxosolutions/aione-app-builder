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
class Aione_Admin_Edit_Taxonomy extends Aione_Admin_Page
{
    public function __construct()
    {
        $this->taxonomies = new Aione_Admin_Taxonomies();
        add_action( 'wp_ajax_aione_ajax_delete_taxonomy',array($this,'aione_ajax_delete_taxonomy_callback') );
    }

    public function init_admin()
    {
        $this->init_hooks();

        $this->get_id = 'aione-taxonomy-slug';

        $this->post_type = 'taxonomy';
        $this->boxes = array(
            'types_labels' => array(
                'callback' => array($this, 'box_labels'),
                'title' => __('Labels', 'aione-app-builder'),
                'default' => 'normal',
                'post_types' => 'custom',
            ),
            'types_taxonomy_type' => array(
                'callback' => array($this, 'box_taxonomy_type'),
                'title' => __('Taxonomy type', 'aione-app-builder'),
                'default' => 'normal',
                'post_types' => 'custom',
            ),
            'types_taxonomies' => array(
                'callback' => array($this, 'box_post_types'),
                'title' => __('Post Types to be used with this Taxonomy', 'aione-app-builder'),
                'default' => 'normal',
            ),
            'types_options' => array(
                'callback' => array($this, 'box_options'),
                'title' => __('Options', 'aione-app-builder'),
                'default' => 'advanced',
                'post_types' => 'custom',
            ),

            'submitdiv' => array(
                'callback' => array($this, 'box_submitdiv'),
                'title' => __('Save', 'aione-app-builder'),
                'default' => 'side',
                'priority' => 'core',
            ),
        );
        $this->boxes = apply_filters('aione_meta_box_order_defaults', $this->boxes, 'taxonomy');
        $this->boxes = apply_filters('aione_meta_box_taxonomy', $this->boxes);

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
        }

        if ( $id ) {
            $taxonomies = $this->taxonomies->get();
            if ( isset( $taxonomies[$id] ) ) {
                $this->ct = $taxonomies[$id];
                $update = true;
            } else {
                aione_admin_message_store( __( 'Wrong Taxonomy specified.', 'aione-app-builder' ), 'error' );
                return false;
            }
        } else {
            $this->ct = aione_custom_taxonomies_default();
        }

        $current_user_can_edit = true;
        
        /**
         * sanitize _builtin
         */
        if ( !isset($this->ct['_builtin']) ) {
            $this->ct['_builtin'] = false;
        }

        $form = $this->prepare_screen();

        if ( $current_user_can_edit && $update ) {
            $form['id'] = array(
                '#type' => 'hidden',
                '#value' => $id,
                '#name' => 'ct[aione-taxonomy-slug]',
            );
            
            $form['slug_conflict_check_nonce'] = array(
                '#type' => 'hidden',
                '#value' => wp_create_nonce( 'check_slug_conflicts' ),
                '#name' => 'aione_check_slug_conflicts_nonce',
                '_builtin' => true,
            );

        }

        /**
         * post icon field
         */
        $menu_icon = isset( $this->ct['icon']) && !empty($this->ct['icon']) ? $this->ct['icon'] : 'admin-post';
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
        );
        $table_row = '<tr><td><LABEL></td><td><ERROR><BEFORE><ELEMENT><AFTER></td></tr>';

        $form['name'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[labels][name]',
            '#title' => __( 'Name plural', 'aione-app-builder' ) . ' (<strong>' . __( 'required', 'aione-app-builder' ) . '</strong>)',
            '#description' => '<strong>' . __( 'Enter in plural!', 'aione-app-builder' ) . '.',
            '#value' =>  isset( $this->ct['labels']['name'] ) ? $this->ct['labels']['name']:'',
            '#validate' => array(
                'required' => array('value' => true),
                'maxlength' => array('value' => 30),
            ),
            '#pattern' => $table_row,
            '#inline' => true,
            '#attributes' => array(
                'placeholder' => __('Enter Taxonomy name plural','aione-app-builder'),
                'class' => 'widefat',
                'required' => 'required',
            ),
        );

        $form['name-singular'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[labels][singular_name]',
            '#title' => __( 'Name singular', 'aione-app-builder' ) . ' (<strong>' . __( 'required', 'aione-app-builder' ) . '</strong>)',
            '#description' => '<strong>' . __( 'Enter in singular!', 'aione-app-builder' ) . '</strong><br />' . '.',
            '#value' => isset( $this->ct['labels']['singular_name'] ) ? $this->ct['labels']['singular_name']:'',
            '#validate' => array(
                'required' => array('value' => true),
                'maxlength' => array('value' => 30),
            ),
            '#pattern' => $table_row,
            '#inline' => true,
            '#attributes' => array(
                'placeholder' => __('Enter Taxonomy name singular','aione-app-builder'),
                'class' => 'widefat js-aione-slugize-source',
                'required' => 'required',
            ),
        );

        /*
         *
         * IF isset $_POST['slug'] it means form is not submitted
         */
        $attributes = array();
        if ( !empty( $_POST['ct']['slug'] ) ) {
            $reserved = $this->aione_is_reserved_name( sanitize_text_field( $_POST['ct']['slug'] ), 'taxonomy' );
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
            '#description' => '<strong>' . __( 'Enter in singular!', 'aione-app-builder' )
            . '</strong><br />' . __( 'Machine readable name.', 'aione-app-builder' )
            . '<br />' . __( 'If not provided - will be created from singular name.', 'aione-app-builder' ) . '<br />',
                '#value' => isset( $this->ct['slug'] ) ? $this->ct['slug'] : '',
                '#pattern' => $table_row,
                '#inline' => true,
                '#validate' => array(
                    'required' => array('value' => true),
                    'nospecialchars' => array('value' => true),
                    'maxlength' => array('value' => 30),
                ),
                '#attributes' => $attributes + array(
                    'maxlength' => '30',
                    'placeholder' => __('Enter Taxonomy slug','aione-app-builder'),
                    'class' => 'widefat js-aione-slugize',
                    'required' => 'required',
                ),
            );
        $form['description'] = array(
            '#type' => 'textarea',
            '#name' => 'ct[description]',
            '#title' => __( 'Description', 'aione-app-builder' ),
            '#value' => isset( $this->ct['description'] ) ? $this->ct['description'] : '',
            '#attributes' => array(
                'rows' => 4,
                'cols' => 60,
                'placeholder' => __('Enter Taxonomy description','aione-app-builder'),
                'class' => 'hidden js-aione-description',
            ),
            '#pattern' => $table_row,
            '#inline' => true,
            '#after' => ( $this->ct['_builtin'] )
                ? __( 'This is built-in WordPress Taxonomy.', 'aione-app-builder' )
                : sprintf(
                    '<a class="js-aione-toggle-description hidden" href="#">%s</a>',
                    __('Add description', 'aione-app-builder')
                ),
        );
        $form['table-1-close'] = array(
            '#type' => 'markup',
            '#markup' => '</tbody></table>',
        );

        $form['box-1-close'] = array(
            '#type' => 'markup',
            '#markup' => '</div>',
            '_builtin' => true,
        );

        if ( $this->ct['_builtin']) {
            $form['name']['#attributes']['readonly'] = 'readonly';
            $form['name-singular']['#attributes']['readonly'] = 'readonly';
            $form['slug']['#attributes']['readonly'] = 'readonly';
            $form['description']['#attributes']['readonly'] = 'readonly';
        }

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
        $button_text = __( 'Save Taxonomy', 'aione-app-builder' );
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
    public function box_options()
    {
        $form = array();
        $form['rewrite-enabled'] = array(
            '#type' => 'checkbox',
            '#force_boolean' => true,
            '#title' => __( 'Rewrite', 'aione-app-builder' ),
            '#name' => 'ct[rewrite][enabled]',
            '#description' => __( 'Rewrite permalinks with this format. Default will use $taxonomy as query var.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['rewrite']['enabled'] ),
            '#inline' => true,
        );
        $hidden = empty( $this->ct['rewrite']['enabled'] ) ? ' class="hidden"' : '';
        $form['rewrite-slug'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[rewrite][slug]',
            '#title' => __( 'Replace taxonomy slug with this', 'aione-app-builder' ),
            '#description' => __( 'Optional', 'aione-app-builder' ) . '. ' . __( 'Replace taxonomy slug with this - defaults to taxonomy slug.', 'aione-app-builder' ),
            '#value' => isset( $this->ct['rewrite']['slug'] ) ? $this->ct['rewrite']['slug'] : '',
            '#inline' => true,
            '#before' => '<div id="aione-form-rewrite-toggle"' . $hidden . '>',
            '#after' => '</div>',
            '#validate' => array('rewriteslug' => array('value' => 'true')),
            '#attributes' => array(
                'class' => 'regular-text',
            ),
        );
        $form['rewrite-with_front'] = array(
            '#type' => 'checkbox',
            '#force_boolean' => true,
            '#title' => __( 'Allow permalinks to be prepended with front base', 'aione-app-builder' ),
            '#name' => 'ct[rewrite][with_front]',
            '#description' => __( 'Defaults to true.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['rewrite']['with_front'] ),
            '#inline' => true,
        );
        $form['rewrite-hierarchical'] = array(
            '#type' => 'checkbox',
            '#name' => 'ct[rewrite][hierarchical]',
            '#title' => __( 'Hierarchical URLs', 'aione-app-builder' ),
            '#description' => sprintf( __( 'True or false allow hierarchical urls (implemented in %sVersion 3.1%s).', 'aione-app-builder' ), '<a href="http://codex.wordpress.org/Version_3.1" title="Version 3.1" target="_blank">', '</a>' ),
            '#default_value' => !empty( $this->ct['rewrite']['hierarchical'] ),
            '#inline' => true,
        );
        $form['vars'] = array(
            '#type' => 'checkboxes',
            '#name' => 'ct[advanced]',
            '#inline' => true,
            '#options' => array(
                'show_ui' => array(
                    '#name' => 'ct[show_ui]',
                    '#default_value' => !empty( $this->ct['show_ui'] ),
                    '#title' => __( 'show_ui', 'aione-app-builder' ),
                    '#description' => __( 'Whether to generate a default UI for managing this taxonomy.', 'aione-app-builder' ) . '<br />' . __( 'Default: if not set, defaults to value of public argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'show_in_nav_menus' => array(
                    '#name' => 'ct[show_in_nav_menus]',
                    '#default_value' => !empty( $this->ct['show_in_nav_menus'] ),
                    '#title' => __( 'show_in_nav_menus', 'aione-app-builder' ),
                    '#description' => __( 'True makes this taxonomy available for selection in navigation menus.', 'aione-app-builder' ) . '<br />' . __( 'Default: if not set, defaults to value of public argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'show_tagcloud' => array(
                    '#name' => 'ct[show_tagcloud]',
                    '#default_value' => !empty( $this->ct['show_tagcloud'] ),
                    '#title' => __( 'show_tagcloud', 'aione-app-builder' ),
                    '#description' => __( 'Whether to allow the Tag Cloud widget to use this taxonomy.', 'aione-app-builder' ) . '<br />' . __( 'Default: if not set, defaults to value of show_ui argument.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
                'show_in_rest' => array(
                    '#name' => 'ct[show_in_rest]',
                    '#default_value' => !empty( $this->ct['show_in_rest'] ),
                    '#title' => __( 'show_in_rest', 'aione-app-builder' ),
                    '#description' => __( 'Whether to expose this post type in the REST API.', 'aione-app-builder' ) . '<br />' . __( 'Default: true.', 'aione-app-builder' ),
                    '#inline' => true,
                ),
            ),
        );
        //if ( wpcf_compare_wp_version( '3.5', '>=' )) {
            $form['vars']['#options']['show_admin_column'] = array(
                '#name' => 'ct[show_admin_column]',
                '#default_value' => !empty( $this->ct['show_admin_column'] ),
                '#title' => __( 'show_admin_column', 'aione-app-builder' ),
                '#description' => __( 'Whether to allow automatic creation of taxonomy columns on associated post-types.', 'aione-app-builder' ) . '<br />' . __( 'Default: false.', 'aione-app-builder' ),
                '#inline' => true,
            );
        //}
        $query_var = isset( $this->ct['query_var'] ) ? $this->ct['query_var'] : '';
        $hidden = !empty( $this->ct['query_var_enabled'] ) ? '' : ' class="hidden"';
        $form['query_var'] = array(
            '#type' => 'checkbox',
            '#name' => 'ct[query_var_enabled]',
            '#title' => 'query_var',
            '#description' => __( 'Disable to prevent queries like "mysite.com/?taxonomy=example". Enable to use queries like "mysite.com/?taxonomy=example". Enable and set a value to use queries like "mysite.com/?query_var_value=example"', 'aione-app-builder' ) . '<br />' . __( 'Default: true - set to $taxonomy.', 'aione-app-builder' ),
            '#default_value' => !empty( $this->ct['query_var_enabled'] ),
            '#after' => '<div id="aione-form-queryvar-toggle"' . $hidden . '><input type="text" name="ct[query_var]" value="' . $query_var . '" class="regular-text aione-form-textfield form-textfield textfield" /><div class="description aione-form-description aione-form-description-checkbox description-checkbox">' . __( 'Optional', 'aione-app-builder' ) . '. ' . __( 'String to customize query var', 'aione-app-builder' ) . '</div></div>',
            '#inline' => true,
        );
        $form['update_count_callback'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[update_count_callback]',
            '#title' => 'update_count_callback', 'aione-app-builder',
            '#description' => __( 'Function name that will be called to update the count of an associated $object_type, such as post, is updated.', 'aione-app-builder' ) . '<br />' . __( 'Default: None.', 'aione-app-builder' ),
            '#value' => !empty( $this->ct['update_count_callback'] ) ? $this->ct['update_count_callback'] : '',
            '#inline' => true,
            '#attributes' => array(
                'class' => 'regular-text',
            ),
        );

        $form['meta_box_cb-header'] = array(
            '#type' => 'markup',
            '#markup' => sprintf('<h3>%s</h3>', __('Meta box callback function', 'aione-app-builder')),
        );
        $form['meta_box_cb-disabled'] = array(
            '#type' => 'checkbox',
            '#force_boolean' => true,
            '#title' => __( 'Hide taxonomy meta box.', 'aione-app-builder' ),
            '#name' => 'ct[meta_box_cb][disabled]',
            '#default_value' => !empty( $this->ct['meta_box_cb']['disabled'] ),
            '#inline' => true,
            '#description' => __( 'If you disable this, there will be no metabox on entry edit screen.', 'aione-app-builder' ),
        );
        $hidden = empty( $this->ct['meta_box_cb']['disabled'] ) ? '':' class="hidden"';
        $form['meta_box_cb'] = array(
            '#type' => 'textfield',
            '#name' => 'ct[meta_box_cb][callback]',
            '#title' => __('meta_box_cb', 'aione-app-builder'),
            '#description' => __( 'Provide a callback function name for the meta box display.', 'aione-app-builder' ) . '<br />' . __( 'Default: None.', 'aione-app-builder' ),
            '#value' => !empty( $this->ct['meta_box_cb']['callback']) ? $this->ct['meta_box_cb']['callback'] : '',
            '#inline' => true,
            '#before' => '<div id="aione-form-meta_box_cb-toggle"' . $hidden . '>',
            '#after' => '</div>',
            '#attributes' => array(
                'class' => 'regular-text',
            ),
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
    public function box_labels()
    {
        $labels = array(
            'search_items' => array(
                'title' => __( 'Search %s', 'aione-app-builder' ),
                'description' => __( "The search items text. Default is __( 'Search Tags' ) or __( 'Search Categories' ).", 'aione-app-builder' ),
                'label' => __('Search Items', 'aione-app-builder'),
            ),
            'popular_items' => array(
                'title' => __( 'Popular %s', 'aione-app-builder' ),
                'description' => __( "The popular items text. Default is __( 'Popular Tags' ) or null.", 'aione-app-builder' ),
                'label' => __('Popular Items', 'aione-app-builder'),
            ),
            'all_items' => array(
                'title' => __( 'All %s', 'aione-app-builder' ),
                'description' => __( "The all items text. Default is __( 'All Tags' ) or __( 'All Categories' ).", 'aione-app-builder' ),
                'label' => __('All Items', 'aione-app-builder'),
            ),
            'parent_item' => array(
                'title' => __( 'Parent %s', 'aione-app-builder' ),
                'description' => __( "The parent item text. This string is not used on non-hierarchical taxonomies such as post tags. Default is null or __( 'Parent Category' ).", 'aione-app-builder' ),
                'label' => __('Parent Item', 'aione-app-builder'),
            ),
            'parent_item_colon' => array(
                'title' => __( 'Parent %s:', 'aione-app-builder' ),
                'description' => __( "The same as parent_item, but with colon : in the end null, __( 'Parent Category:' ).", 'aione-app-builder' ),
                'label' => __('Parent Item with colon', 'aione-app-builder'),
            ),
            'edit_item' => array(
                'title' => __( 'Edit %s', 'aione-app-builder' ),
                'description' => __( "The edit item text. Default is __( 'Edit Tag' ) or __( 'Edit Category' ).", 'aione-app-builder' ),
                'label' => __('Edit Item', 'aione-app-builder'),
            ),
            'update_item' => array(
                'title' => __( 'Update %s', 'aione-app-builder' ),
                'description' => __( "The update item text. Default is __( 'Update Tag' ) or __( 'Update Category' ).", 'aione-app-builder' ),
                'label' => __('Update Item', 'aione-app-builder'),
            ),
            'add_new_item' => array(
                'title' => __( 'Add New %s', 'aione-app-builder' ),
                'description' => __( "The add new item text. Default is __( 'Add New Tag' ) or __( 'Add New Category' ).", 'aione-app-builder' ),
                'label' => __('Add New Item', 'aione-app-builder'),
            ),
            'new_item_name' => array(
                'title' => __( 'New %s Name', 'aione-app-builder' ),
                'description' => __( "The new item name text. Default is __( 'New Tag Name' ) or __( 'New Category Name' ).", 'aione-app-builder' ),
                'label' => __('New Item Name', 'aione-app-builder'),
            ),
            'separate_items_with_commas' => array(
                'title' => __( 'Separate %s with commas', 'aione-app-builder' ),
                'description' => __( "The separate item with commas text used in the taxonomy meta box. This string isn't used on hierarchical taxonomies. Default is __( 'Separate tags with commas' ), or null.", 'aione-app-builder' ),
                'label' => __('Separate Items', 'aione-app-builder'),
            ),
            'add_or_remove_items' => array(
                'title' => __( 'Add or remove %s', 'aione-app-builder' ),
                'description' => __( "the add or remove items text used in the meta box when JavaScript is disabled. This string isn't used on hierarchical taxonomies. Default is __( 'Add or remove tags' ) or null.", 'aione-app-builder' ),
                'label' => __('Add or remove', 'aione-app-builder'),
            ),
            'choose_from_most_used' => array(
                'title' => __( 'Choose from the most used %s', 'aione-app-builder' ),
                'description' => __( "The choose from most used text used in the taxonomy meta box. This string isn't used on hierarchical taxonomies. Default is __( 'Choose from the most used tags' ) or null.", 'aione-app-builder' ),
                'label' => __('Most Used', 'aione-app-builder'),
            ),
            'menu_name' => array(
                'title' => __( 'Menu Name', 'aione-app-builder' ),
                'description' => __( "The menu name text. This string is the name to give menu items. Defaults to value of name.", 'aione-app-builder' ),
                'label' => __('Menu Name', 'aione-app-builder'),
            ),
        );

        $form = array();
        $form['table-1-open'] = array(
            '#type' => 'markup',
            '#markup' => '<table class="aione-form-table widefat striped fixed"><tbody>',
        );
        foreach ( $labels as $name => $label ) {
            $form['labels-' . $name] = array(
                '#type' => 'textfield',
                '#name' => 'ct[labels][' . $name . ']',
                '#title' => $label['label'],
                '#description' => $label['description'],
                '#value' => isset( $this->ct['labels'][$name] ) ? wp_kses_post($this->ct['labels'][$name]):'',
                '#inline' => true,
                '#pattern' => '<tr><td><LABEL></td><td><ELEMENT><DESCRIPTION></td></tr>',
                '#attributes' => array(
                    'class' => 'widefat',
                ),
            );
        }
        $form['table-1-close'] = array(
            '#type' => 'markup',
            '#markup' => '</tbody></table>',
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
    public function box_post_types()
    {
        global $aione; 
        $form = array();
        $post_types = get_post_types( '', 'objects' );
        $options = array();

        $supported = $this->taxonomies->get_post_types_supported_by_taxonomy($this->ct['slug']);
        
        foreach ( $post_types as $post_type_slug => $post_type ) {
            if ( in_array( $post_type_slug, $aione->excluded_post_types ) || !$post_type->show_ui ) {
                continue;
            }
            $options[$post_type_slug] = array(
                '#name' => 'ct[supports][' . $post_type_slug . ']',
                '#title' => $post_type->labels->name,
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
            '#name' => 'ct[supports]',
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
    public function box_taxonomy_type()
    {
        $form = array();
        $form['make-hierarchical'] = array(
            '#type' => 'radios',
            '#name' => 'ct[hierarchical]',
            '#default_value' => (empty( $this->ct['hierarchical'] ) || $this->ct['hierarchical'] == 'hierarchical') ? 'hierarchical' : 'flat',
            '#inline' => true,
            '#options' => array(
                sprintf(
                    '<b>%s</b> - %s',
                    __('Hierarchical', 'aione-app-builder'),
                    __('like post categories, with parent / children relationship and checkboxes to select taxonomy', 'aione-app-builder' )
                ) => 'hierarchical',
                sprintf(
                    '<b>%s</b> - %s',
                    __('Flat', 'aione-app-builder'),
                    __( 'like post tags, with a text input to enter terms', 'aione-app-builder' )
                ) => 'flat',
            ),
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
    private function save()
    {
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
            aione_admin_message_store( __( 'Please set taxonomy name', 'aione-app-builder' ), 'error' );
            return false;
        }

        if ( isset( $data[$this->get_id] ) ) {
            $update = true;
            $data[$this->get_id] = sanitize_title( $data[$this->get_id] );
        }
        if ( isset( $data['slug'] ) ) {
            $data['slug'] = sanitize_title( $data['slug'] );
        }
        if ( isset( $data['rewrite']['slug'] ) ) {
            $data['rewrite']['slug'] = remove_accents( $data['rewrite']['slug'] );
            $data['rewrite']['slug'] = strtolower( $data['rewrite']['slug'] );
            $data['rewrite']['slug'] = trim( $data['rewrite']['slug'] );
        }

        // Set tax name
        $tax = '';
        if ( !empty( $data['slug'] ) ) {
            $tax = $data['slug'];
        } else if ( !empty( $data[$this->get_id] ) ) {
            $tax = $data[$this->get_id];
        } else if ( !empty( $data['labels']['singular_name'] ) ) {
            $tax = sanitize_title( $data['labels']['singular_name'] );
        }

        if ( empty( $tax ) ) {
            aione_admin_message_store( __( 'Please set taxonomy name', 'aione-app-builder' ), 'error' );
            return false;
        }

        if ( empty( $data['labels']['singular_name'] ) ) {
            $data['labels']['singular_name'] = $tax;
        }

        $data['slug'] = $tax;
        $taxonomies = $this->taxonomies->get();

        /**
         * is built-in?
         */
        $tax_is_built_in = aione_is_builtin_taxonomy($tax);

        // Check reserved name
        $reserved = $this->aione_is_reserved_name( $tax, 'taxonomy' ) && !$tax_is_built_in;
        if ( is_wp_error( $reserved ) ) {
            aione_admin_message_store( $reserved->get_error_message(), 'error' );
            return false;
        }

        // Check if exists
        if ( $update && !array_key_exists( $data[$this->get_id], $taxonomies ) ) {
            aione_admin_message_store( __( "Taxonomy do not exist", 'aione-app-builder' ), 'error' );
            return false;
        }

        // Check overwriting
        if ( !$update && array_key_exists( $tax, $taxonomies ) ) {
            /**
             * set last edit author
             */

            $data[AIONE_AUTHOR] = get_current_user_id();

            aione_admin_message_store( __( 'Taxonomy already exists', 'aione-app-builder' ), 'error' );
            return false;
        }

        // Check if our tax overwrites some tax outside
        $tax_exists = get_taxonomy( $tax );
        if ( !$update && !empty( $tax_exists ) ) {
            aione_admin_message_store( __( 'Taxonomy already exists', 'aione-app-builder' ), 'error' );
            return false;
        }

        // Check if renaming
        if ( !$tax_is_built_in && $update && $data[$this->get_id] != $tax ) {
            global $wpdb;
            $wpdb->update(
                $wpdb->term_taxonomy,
                array(
                    'taxonomy' => esc_sql($tax)
                ),
                array(
                    'taxonomy' => esc_sql($data[$this->get_id]),
                ),
                array('%s'),
                array('%s')
            );
            // Sync action
            do_action( 'aione_taxonomy_renamed', $tax, $data[$this->get_id] );
            // Delete old type
            unset( $taxonomies[$data[$this->get_id]] );
        }

        // Check if active
        if ( isset( $taxonomies[$tax]['disabled'] ) ) {
            $data['disabled'] = $taxonomies[$tax]['disabled'];
        }

        /**
         * Sync with post types
         */
        $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $post_types = $post_type_option->get_components();
        foreach ( $post_types as $id => $type ) {
            if ( !empty( $data['supports'] ) && array_key_exists( $id, $data['supports'] ) ) {
                if ( empty($post_types[$id]['taxonomies'][$data['slug']]) ) {
                    $post_types[$id][AIONE_EDIT_LAST] = time();
                }
                $post_types[$id]['taxonomies'][$data['slug']] = 1;
            } else {
                if ( !empty($post_types[$id]['taxonomies'][$data['slug']]) ) {
                    $post_types[$id][AIONE_EDIT_LAST] = time();
                }
                unset( $post_types[$id]['taxonomies'][$data['slug']] );
            }
        }
        update_option(AIONE_OPTION_NAME_COMPONENTS, $post_types);

        /**
         * fix built-in
         */
        if ($tax_is_built_in) {
            $data['_builtin'] = true;
            unset($data['icon']);

            // make sure default labels are used for the built-in taxonomies
            // for the case a smart user enables disabled="disabled" inputs
            $data['labels'] = $taxonomies[$tax]['labels'];

            unset($data['aione-taxonomy-slug']);
        }

        $taxonomies[$tax] = $data;
        $taxonomies[$tax][AIONE_EDIT_LAST] = time();

        // set last edit author
        $taxonomies[$tax][AIONE_AUTHOR] = get_current_user_id();

        foreach( $taxonomies as $id => $taxonomy ) {
            // make sure "supports" field is saved for ALL taxonomies
            if( !isset( $taxonomy['supports'] ) && isset( $taxonomy['object_type'] ) ) {
                if( !empty( $taxonomy['object_type'] ) ) {
                    foreach( $taxonomy['object_type'] as $supported_post ) {
                        $taxonomy['supports'][$supported_post] = 1;
                    }
                }
            }

            // make sure "slug" field is set
            if( !isset( $taxonomy['slug'] ) )
                $taxonomy['slug'] = isset( $taxonomy['name'] )
                    ? $taxonomy['name']
                    : $id;

            // make sure "name" field is set
            if( !isset( $taxonomy['name'] ) )
                $taxonomy['name'] = isset( $taxonomy['slug '] );

            // make sure "supports" field is set
            if( !isset( $taxonomy['supports'] ) )
                $taxonomy['supports'] = array();


            $taxonomies[$id] = $taxonomy;
        }

        /**
         * save
         */
        update_option( AIONE_OPTION_NAME_TAXONOMIES, $taxonomies );

        // WPML register strings
        //wpcf_custom_taxonimies_register_translation( $tax, $data );

        $msg = $update
            ? __( 'Taxonomy saved.', 'aione-app-builder' )
            : __( 'New Taxonomy created.', 'aione-app-builder' );

        aione_admin_message_store(
            $msg,
            'updated notice notice-success is-dismissible'
        );

        // Flush rewrite rules
        flush_rewrite_rules();

        $args = array(
            'page' => 'aione-edit-taxonomy',
            $this->get_id => $tax,
            'aione-message' => get_user_option('aione-modal'),
            'flush' => 1
        );
        
        if( isset( $_GET['ref'] ) )
            $args['ref'] = $_GET['ref'];

        // Redirect
        wp_safe_redirect(
            esc_url_raw(
                add_query_arg(
                    $args,
                    admin_url( 'admin.php' )
                )
            )
        );
        die();
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
            && isset( $_POST['ct']['aione-taxonomy-slug'] ) // ... if it's an already saved taxonomy ...
            && $_POST['ct']['aione-taxonomy-slug'] == $name // ... and the slug didn't changed.
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

    function aione_ajax_delete_taxonomy_callback(){
        switch ($_REQUEST['aione_action']){
            case 'delete_taxonomy':
                $custom_taxonomy = $this->aione_ajax_helper_get_taxonomy();
                if ( empty($custom_taxonomy) ) {
                    aione_ajax_helper_print_error_and_die();
                }
                $custom_taxonomies = get_option(AIONE_OPTION_NAME_TAXONOMIES, array());
                unset($custom_taxonomies[$custom_taxonomy]);
                update_option(AIONE_OPTION_NAME_TAXONOMIES, $custom_taxonomies);
                aione_admin_deactivate_content('taxonomy', $custom_taxonomy);
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

    function aione_ajax_helper_get_taxonomy()    {
        if (!isset($_GET['aione-taxonomy-slug']) || empty($_GET['aione-taxonomy-slug'])) {
            return false;
        }
        $taxonomy = $_GET['aione-taxonomy-slug'];
        $custom_taxonomies = get_option(AIONE_OPTION_NAME_TAXONOMIES, array());
        if (
            isset($custom_taxonomies[$taxonomy])
            && isset($custom_taxonomies[$taxonomy]['slug'])
        ) {
            return $custom_taxonomies[$taxonomy]['slug'];
        }
        $taxonomy = get_taxonomy($taxonomy);
        if ( is_object($taxonomy) ) {
            return $taxonomy->name;
        }
        return false;
    }
    
    
}

