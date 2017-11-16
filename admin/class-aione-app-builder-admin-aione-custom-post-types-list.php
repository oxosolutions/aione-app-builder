<?php
if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Aione_App_Builder_Admin_Custom_Post_Types_List_Table extends WP_List_Table{

	var $custom_types;
	var $bulk_action_field_name = 'wpcf_cpt_ids';

	function __construct()
    {
        //Set parent defaults
        $args = wp_parse_args( $args, array(
            'plural' => '',
            'singular' => '',
            'ajax' => false,
            'screen' => null,
        ) );

        $this->custom_types = get_option('aione_custom_post_types', array());
    }

    function column_default($item, $column_name)
    {
        switch($column_name){
            case 'title':
            case 'description':
                return stripslashes($item[$column_name]);
            /*case 'taxonomies':
                $rows = array();
                if (!empty($item[$column_name])) {
                    foreach ($item[$column_name] as $taxonomy_slug => $taxonomy_name) {
                        $rows[] = stripslashes(wpcf_translate($taxonomy_name . ' name', $taxonomy_name, 'Types-TAX'));
                    }
                }
                return empty($rows)? __('None', 'wpcf'):implode(', ', $rows);*/
            case 'status':
                return 'active' == $item[$column_name]? __('Yes', 'wpcf'):__('No', 'wpcf');
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb($item)
    {
        /**
         * do not show checkbox for built-in post types
         */
        if ( isset($item['_builtin']) && $item['_builtin'] ) {
            return '';
        }
       
        return sprintf(
                '<input type="checkbox" name="%s[]" value="%s" />',
                $this->bulk_action_field_name,
                $item['slug']
            );
    }

    function get_columns()
    {
        $columns = array(
            'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
            'title'       => __('Name', 'wpcf'),
            'description' => __('Description', 'wpcf'),
            'status'      => __('Active', 'wpcf'),
            /*'taxonomies'  => __('Taxonomies', 'wpcf'),*/
        );
        return $columns;
    }

    function get_sortable_columns()
    {
        $sortable_columns = array(
            'title'       => array('title',true),     //true means it's already sorted
            'description' => array('description',false),
            'status'      => array('status',false)
        );
        return $sortable_columns;
    }

    function get_bulk_actions()
    {
        $actions = array(
            'delete'     => __('Delete', 'wpcf'),
        );
        return $actions;
    }

    function process_bulk_action()
    {
        $action = $this->current_action();

        /**
         * check nounce
         */
        if (!empty($action)) {
            $nonce = '';
            if ( isset($_REQUEST['_wpnonce'] ) ) {
                $nonce = sanitize_text_field( $_REQUEST['_wpnonce'] );
            }
            /*if ( ! wp_verify_nonce( $nonce, 'bulk-posttypes' ) ) {
                die( 'Security check' );
            }*/
        }

        //Detect when a bulk action is being triggered...
        if (
            !empty($this->custom_types)
            && isset($_POST[$this->bulk_action_field_name])
            && !empty($_POST[$this->bulk_action_field_name])
        ) {
            $slugs_to_delete = array();
            foreach( $_POST[$this->bulk_action_field_name] as $key ) {
                
                switch($action) {
                case 'delete':
                    unset($this->custom_types[$key]);
                    $slugs_to_delete[] = $key;
                    break;
                }
            }
            /**
             * update post types
             */
            update_option('aione_custom_post_types', $this->custom_types);
            
        }
    }

    function prepare_items()
    {
        /**
         * First, lets decide how many records per page to show
         */
        $per_page = $this->get_items_per_page('wpcf_cpt_per_page', 10);;
        //$per_page = 10;
        /**
         * REQUIRED. Now we need to define our column headers. This includes a complete
         * array of columns to be displayed (slugs & titles), a list of columns
         * to keep hidden, and a list of columns that are sortable. Each of these
         * can be defined in another method (as we've done here) before being
         * used to build the value for our _column_headers property.
         */
        $columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();

        /**
         * REQUIRED. Finally, we build an array to be used by the class for column
         * headers. The $this->_column_headers property takes an array which contains
         * 3 other arrays. One for all columns, one for hidden columns, and one
         * for sortable columns.
         */
        $this->_column_headers = array($columns, $hidden, $sortable);

        /**
         * Optional. You can handle your bulk actions however you see fit. In this
         * case, we'll handle them within our package just to keep things clean.
         */
        $this->process_bulk_action();

        
        

        /**
         * Instead of querying a database, we're going to fetch the example data
         * property we created for use in this plugin. This makes this example
         * package slightly different than one you might build on your own. In
         * this example, we'll be using array manipulation to sort and paginate
         * our data. In a real-world implementation, you will probably want to
         * use sort and pagination data to build a custom query instead, as you'll
         * be able to use your precisely-queried data immediately.
         */

        $s = isset($_POST['s'])? mb_strtolower(trim($_POST['s'])):false;

        $data = array();
        if ( !empty($this->custom_types) ){
            foreach( array_values($this->custom_types) as $type ) {
                if (empty($type) || empty($type['slug'])) {
                    continue;
                }
                $one = array(
                    'description' => isset($type['description'])? $type['description']:'',
                    //'taxonomies' => isset($map_taxonomies_by_post_type[$type['slug']])? $map_taxonomies_by_post_type[$type['slug']]:array(),
                    'slug' => $type['slug'],
                    'status' => isset($type['disabled'])? 'inactive':'active',
                    'title' => stripslashes($type['labels']['name']),
                    'type' => 'cpt',
                    '_builtin' => false,
                    'author' => isset($type['_wpcf_author_id'])? intval($type['_wpcf_author_id']):0,
                );
                $add_one = true;
                if ( $s ) {
                    $add_one = false;
                    foreach( array('description', 'slug', 'title' ) as $key ) {
                        if ( $add_one || empty( $one[$key] ) ) {
                            continue;
                        }
                        if ( is_numeric(strpos(mb_strtolower($one[$key]), $s))) {
                            $add_one = true;
                        }
                    }
                }
                if ( $add_one ) {
                    $data[$one['slug']] = $one;
                }
            }
        }


        /**
         * This checks for sorting input and sorts the data in our array accordingly.
         */
        usort( $data, array( &$this, 'sort_data' ) );

        /**
         * REQUIRED for pagination. Let's figure out what page the user is currently
         * looking at. We'll need this later, so you should always include it in
         * your own package classes.
         */
        $current_page = $this->get_pagenum();

        /**
         * REQUIRED for pagination. Let's check how many items are in our data array.
         * In real-world use, this would be the total number of items in your database,
         * without filtering. We'll need this later, so you should always include it
         * in your own package classes.
         */
        $this->screen = get_current_screen();
        $total_items = count($data);

        /**
         * The WP_List_Table class does not handle pagination for us, so we need
         * to ensure that the data is trimmed to only the current page. We can use
         * array_slice() to
         */
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);

        /**
         * REQUIRED. Now we can add our *sorted* data to the items property, where
         * it can be used by the rest of the class.
         */
        $this->items = $data;

        /**
         * REQUIRED. We also have to register our pagination options & calculations.
         */
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    public function single_row($item)
    {
        static $row_class = '';
        $row_class = ( $row_class == '' ? 'alternate' : '' );

        printf('<tr class="%s status-%s">', $row_class, $item['status']);
        $this->single_row_columns( $item );
        echo '</tr>';
    }

    public function no_items()
    {
        if ( isset($_POST['s']) ) {
            _e('No post types found.','wpcf');
            return;
        }
        //wpcf_admin_ctt_list_header();
        printf(
            '<a class="button-primary" href="%s">%s</a>',
            esc_url(
                add_query_arg(
                    array(
                        'page' => 'aione-edit-cpt',
                    ),
                    admin_url('admin.php')
                )
            ),
            __('Add New', 'wpcf')
        );
    }

    function column_title($item)
    { 
        $edit_link = esc_url(
                add_query_arg(
                    array(
                        'page' => 'aione-edit-cpt',
                        'aione-post-type' => $item['slug'],
                    ),
                    admin_url('admin.php')
                )
            );
        
        //Build row actions
        $actions = array();
        $actions['edit'] = sprintf('<a href="%s">%s</a>', $edit_link, __('Edit', 'wpcf'));
        if ( 'cpt' == $item['type'] ) {
            $a = array(
                'delete'     => sprintf(
                    '<a href="%s" class="submitdelete wpcf-ajax-link" id="wpcf-list-delete-%s">%s</a>',
                    esc_url(
                        add_query_arg(
                            array(
                                'action' => 'aione_cpt_delete_post_type',
                                'wpcf-post-type' => $item['slug'],
                                'wpcf_ajax_update' => 'wpcf_list_ajax_response_'.$item['slug'],
                                '_wpnonce' => wp_create_nonce('delete_post_type'),
                                'wpcf_warning' => urlencode(__('Are you sure?', 'wpcf')),
                            ),
                            admin_url('admin-ajax.php')
                        )
                    ),
                    $item['slug'],
                    __('Delete', 'wpcf')
                ),
            );
            $actions += $a;
        } 

        //Return the title contents
        return sprintf(
            '<strong><a href="%s" class="row-title">%s</strong>%s',
            $edit_link,
            $item['title'],
            $this->row_actions($actions)
        );
    }


    private function sort_data( $a, $b )
    {
        // Set defaults
        $orderby = 'title';
        $order = 'asc';
        // If orderby is set, use this as the sort column
        if(!empty($_GET['orderby']))
        {
            $orderby = $_GET['orderby'];
        }
        // If order is set use this as the order
        if(!empty($_GET['order']))
        {
            $order = $_GET['order'];
        }
        $result = strcmp( $a[$orderby], $b[$orderby] );
        if($order === 'asc')
        {
            return $result;
        }
        return -$result;
    }


}

?>