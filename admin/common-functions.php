<?php
/**
 * Get buuild in taxonomies.
 */
function aione_get_builtin_in_taxonomies($output = 'names')
{
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

/**
 * Check is a build-in taxonomy
 * @parem string taxonomy slug
 * @return boolean is this build-in taxonomy
 */
function aione_is_builtin_taxonomy($taxonomy)
{
    switch($taxonomy) {
    case 'post_tag':
    case 'category':
        return true;
    }
    return in_array($taxonomy, aione_get_builtin_in_taxonomies());
}
function get_excluded_taxonomies() {
        return array( 'nav_menu', 'link_category', 'post_format' );
    }
function get_editable_taxonomies() {
    $custom_taxonomies = array_keys( aione_ensarr( get_option( AIONE_OPTION_NAME_TAXONOMIES, array() ) ) );
    $builtin_taxonomies = aione_get_builtin_in_taxonomies( 'names' );
    $allowed_taxonomies = array_merge( $custom_taxonomies, $builtin_taxonomies );

    $excluded_taxonomies = get_excluded_taxonomies();
    $allowed_taxonomies = array_diff( $allowed_taxonomies, $excluded_taxonomies );

    $taxonomies = get_taxonomies( '', 'objects' );
    foreach( $taxonomies as $taxonomy_slug => $taxonomy ) {
        if( ! in_array( $taxonomy_slug, $allowed_taxonomies ) ) {
            unset( $taxonomies[ $taxonomy_slug ] );
        }
    }

    return $taxonomies;
}

function aione_get_builtin_in_post_types(){
    static $post_types = array();
    if ( empty( $post_types ) ) {
        $post_types = get_post_types(array('public' => true, '_builtin' => true));
    }
    return $post_types;
}

function aione_is_builtin_post_types($post_type){
    $post_types = aione_get_builtin_in_post_types();
    return in_array($post_type, $post_types);
}

function aione_admin_calculate_menu_page_load_hook( $data ) {
	$load_hook = '';
	if ( array_key_exists( 'load_hook', $data ) ) {
		$load_hook = $data['load_hook'];
	} else if ( 
		array_key_exists( 'callback', $data ) 
		&& is_string( $data['callback' ] ) 
	) {
        $load_hook = sprintf( '%s_hook', $data['callback'] );
    }
	return $load_hook;
}



/**
 * Adds typical header on admin pages.
 *
 */
function aione_add_admin_header($title, $add_new = false, $add_new_title = false){
    echo '<div class="wrap">';
    echo '<h1>', $title;
    if ( !$add_new_title ) {
        $add_new_title = __('Add New', 'aione-app-builder');
    }
    if ( is_array($add_new) && isset($add_new['page']) ) {
        printf(
                ' <a href="%s" class="add-new-h2">%s</a>',
                esc_url(add_query_arg( $add_new, admin_url('admin.php'))),
                $add_new_title
            );
    }
    echo '</h2>';
    $current_page = sanitize_text_field( $_GET['page'] );
    do_action( 'aione_admin_header' );
    do_action( 'aione_admin_header_' . $current_page );
}

function aione_add_admin_footer(){
    $current_page = sanitize_text_field( $_GET['page'] );
	do_action( 'aione_admin_footer_' . $current_page );
    do_action( 'aione_admin_footer' );
    echo '</div>';
}

function aione_admin_components_list(){
    //Create an instance of our package class...
    $listTable = new Aione_App_Builder_Admin_Components_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $listTable->prepare_items();
    ?>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="component-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $listTable->search_box(__('Search Components', 'aione-app-builder'), 'search_id'); ?>
            <!-- Now we can render the completed list table -->
            <?php $listTable->display() ?>
        </form>
    <?php
}
function aione_admin_taxonomies_list(){
    //Create an instance of our package class...
    $listTable = new Aione_App_Builder_Admin_Taxonomies_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $listTable->prepare_items();
    ?>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="taxonomy-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $listTable->search_box(__('Search Taxonomies', 'aione-app-builder'), 'search_id'); ?>
            <!-- Now we can render the completed list table -->
            <?php $listTable->display() ?>
        </form>
    <?php
}

function aione_admin_templates_list(){
    //Create an instance of our package class...
    $listTable = new Aione_App_Builder_Admin_Templates_List_Table();
    //Fetch, prepare, sort, and filter our data...
    $listTable->prepare_items();
    ?>
        <!-- Forms are NOT created automatically, so you need to wrap the table in one to use features like bulk actions -->
        <form id="taxonomy-filter" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $listTable->search_box(__('Search Templates', 'aione-app-builder'), 'search_id'); ?>
            <!-- Now we can render the completed list table -->
            <?php $listTable->display() ?>
        </form>
    <?php
}

function aione_usort_reorder($a,$b){
    $orderby = (!empty($_REQUEST['orderby'])) ? sanitize_text_field( $_REQUEST['orderby'] ) : 'title'; //If no sort, default to title
    $order = (!empty($_REQUEST['order'])) ? sanitize_text_field( $_REQUEST['order'] ) : 'asc'; //If no order, default to asc
    if ( ! in_array( $order, array( 'asc', 'desc' ) ) ) {
        $order = 'asc';
    }
    if ('title' == $orderby || !isset($a[$orderby])) {
        $orderby = 'slug';
    }
    /**
     * sort by slug if sort field is the same
     */
    if ( $a[$orderby] == $b[$orderby] ) {
        $orderby = 'slug';
    }
    $result = strcmp($a[$orderby], $b[$orderby]); //Determine sort order
    return ($order==='asc') ? $result : -$result; //Send final sort direction to usort
}

function aione_form( $id, $form = array() ) {
	static $wpcf_forms = array();

	if ( isset( $wpcf_forms[ $id ] ) ) {
    return $wpcf_forms[ $id ];
	}

	require_once dirname(__FILE__) . '/form/forms.php';
	/** @noinspection PhpUndefinedClassInspection */

	$new_form = new Enlimbo_Forms_Aione();
	$new_form->autoHandle( $id, $form );

	$wpcf_forms[ $id ] = $new_form;

	return $wpcf_forms[ $id ];
}

function aione_admin_screen( $post_type, $form_output = ''){
?>
<div id="poststuff">
    <div id="post-body" class="metabox-holder columns-<?php echo 1 == get_current_screen()->get_columns() ? '1' : '2'; ?>">
<?php echo $form_output; ?>
        <div id="postbox-container-1" class="postbox-container <?php echo $post_type;?>">
            <?php do_meta_boxes($post_type, 'side', null); ?>
        </div>
        <div id="postbox-container-2" class="postbox-container <?php echo $post_type;?>">
<?php
    do_meta_boxes($post_type, 'normal', null);
    do_meta_boxes($post_type, 'advanced', null);
?>
        </div>
    </div>
</div>
<?php
}

function aione_form_add_js_validation( $element ) {
    static $validation = array();
    if ( $element == 'get' ) {
        $temp = $validation;
        $validation = array();
        return $temp;
    }
    $validation[$element['#id']] = $element;
}

if( !function_exists( 'aione_getpost' ) ) {

    function aione_getpost( $key, $default = '', $valid = null ) {
        return aione_getarr( $_POST, $key, $default, $valid );
    }

}


if( !function_exists( 'aione_getget' ) ) {

    function aione_getget( $key, $default = '', $valid = null ) {
        return aione_getarr( $_GET, $key, $default, $valid );
    }

}


if( !function_exists( 'aione_getarr' ) ) {

    function aione_getarr( &$source, $key, $default = '', $valid = null ) {
        if ( is_array( $source ) && array_key_exists( $key, $source ) ) {
            $val = $source[ $key ];
            if ( is_array( $valid ) && ! in_array( $val, $valid ) ) {
                return $default;
            }

            return $val;
        } else {
            return $default;
        }
    }

}


if( !function_exists( 'aione_ensarr' ) ) {

    function aione_ensarr( $array, $default = array() ) {
        return ( is_array( $array ) ? $array : $default );
    }

}


if( !function_exists( 'aione_wraparr' ) ) {

    function aione_wraparr( $input ) {
        return ( is_array( $input ) ? $input : array( $input ) );
    }

}


if( !function_exists( 'aione_getnest' ) ) {

    function aione_getnest( &$source, $keys = array(), $default = null ) {

        $current_value = $source;

        // For detecting if a value is missing in a sub-array, we'll use this temporary object.
        // We cannot just use $default on every level of the nesting, because if $default is an
        // (possibly nested) array itself, it might mess with the value retrieval in an unexpected way.
        $missing_value = new stdClass();

        while( ! empty( $keys ) ) {
            $current_key = array_shift( $keys );
            $is_last_key = empty( $keys );

            $current_value = aione_getarr( $current_value, $current_key, $missing_value );

            if ( $is_last_key ) {
                // Apply given default value.
                if( $missing_value === $current_value ) {
                    return $default;
                } else {
                    return $current_value;
                }
            } elseif ( ! is_array( $current_value ) ) {
                return $default;
            }
        }

        return $default;
    }

}

function aione_admin_common_only_show($form){
    foreach( $form as $key => $data ) {
        if ( !isset($data['#type'] ) ) {
            continue;
        }
        /**
         * remove draggable elements
         */
        if ( preg_match( '/^draggable/', $key ) ) {
            unset($form[$key]);
            continue;
        }

        switch( $data['#type'] ) {

        case 'select':
            $form[$key]['#markup'] = $form[$key]['#default_value'];
            break;

        case 'radios':
            $form[$key]['#markup'] = '';
            foreach ( $data['#options'] as $radio_key => $radio_value ) {
                if ( $data['#default_value'] == $radio_value ) {
                    $form[$key]['#markup'] = '<span class="dashicons-before dashicons-yes"></span>'.$radio_key;
                }
            }
            break;

        case 'checkbox':
        case 'radio':
            $form[$key]['#markup'] = aione_admin_common_only_show_checkbox_helper($data);
            break;

        case 'checkboxes':
            $markup = '';
            if ( isset($data['#options']) && is_array($data['#options']) ) {
                foreach( $data['#options'] as $option_key => $option_value ) {
                    $markup .= aione_admin_common_only_show_checkbox_helper($option_value);
                }
            }
            $form[$key]['#markup'] = $markup;
            break;

        case 'textarea':
        case 'textfield':
            $form[$key]['#markup'] = wpautop(empty($form[$key]['#value'])? __('[empty]', 'aione-app-builder'):stripcslashes($form[$key]['#value']));
            break;

            /**
             * do nothing
             */
        case 'markup':
        case 'button':
            break;

        case 'fieldset':
            $fieldset_form = array(
                'type' => array(
                    'value' => $data['type']['#value'],
                    'label' => __('Type', 'aione-app-builder'),
                ),
                'name' => array(
                    'value' => $data['name']['#value'],
                    'label' => __('Name', 'aione-app-builder'),
                ),
                'slug' => array(
                    'value' => $data['slug']['#value'],
                    'label' => __('Slug', 'aione-app-builder'),
                ),
                'description' => array(
                    'value' => $data['description']['#value']? $data['description']['#value']:__('[empty]', 'aione-app-builder'),
                    'label' => __('Description', 'aione-app-builder'),
                ),
                'repetitive' => array(
                    'value' => isset($data['repetitive']) && $data['repetitive']['#default_value']? __('Allow multiple-instances of this field', 'aione-app-builder'):__('This field can have only one value', 'aione-app-builder'),
                    'label' => __('Repetitive', 'aione-app-builder'),
                ),
            );
            foreach ( array_keys($data) as $data_key ) {
                if ( preg_match('/^#/', $data_key ) ) {
                    continue;
                }
                unset($form[$key][$data_key]);
            }
            $form[$key]['#markup'] = '<dl>';
            foreach( $fieldset_form as $fieldset_key => $fieldset_data ) {
                $form[$key]['#markup'] .= sprintf(
                    '<dt>%s</dt><dd>%s</dd>',
                    $fieldset_data['label'],
                    $fieldset_data['value']
                );
            }
            $form[$key]['#markup'] .= '</dl>';
            break;

            /**
             * remove unnesseasry elements
             */
        case 'submit':
        case 'hidden':
            unset($form[$key]);
            break;
        }
        $form[$key]['#type'] = 'markup';
    }
    return $form;
}

function aione_admin_common_only_show_checkbox_helper($data){
    return sprintf(
        '<p><span class="dashicons-before dashicons-%s"></span>%s%s</p>',
        empty($data['#default_value'])? 'no':'yes',
        $data['#title'],
        isset($data['#description']) && !empty($data['#description'])?  sprintf('<br /><span class="description">%s</span>', $data['#description']):''
    );
}

function get_reserved_terms(){
        $reserved_terms = array(
        'attachment',
        'attachment_id',
        'author',
        'author_name',
        'calendar',
        'cat',
        'category',
        'category__and',
        'category__in',
        'category__not_in',
        'category_name',
        'comments_per_page',
        'comments_popup',
        'custom',
        'customize_messenger_channel',
        'customized',
        'cpage',
        'day',
        'debug',
        'embed',
        'error',
        'exact',
        'feed',
        'hour',
        'link_category',
        'm',
        'minute',
        'monthnum',
        'more',
        'name',
        'nav_menu',
        'nonce',
        'nopaging',
        'offset',
        'order',
        'orderby',
        'p',
        'page',
        'page_id',
        'paged',
        'pagename',
        'pb',
        'perm',
        'post',
        'post__in',
        'post__not_in',
        'post_format',
        'post_mime_type',
        'post_status',
        'post_tag',
        'post_type',
        'posts',
        'posts_per_archive_page',
        'posts_per_page',
        'preview',
        'robots',
        's',
        'search',
        'second',
        'sentence',
        'showposts',
        'static',
        'subpost',
        'subpost_id',
        'tag',
        'tag__and',
        'tag__in',
        'tag__not_in',
        'tag_id',
        'tag_slug__and',
        'tag_slug__in',
        'taxonomy',
        'tb',
        'term',
        'terms',
        'theme',
        'title',
        'type',
        'w',
        'withcomments',
        'withoutcomments',
        'year'
    );
    return $reserved_terms;    
}

/**
 * Sanitize admin notice.
 *
 * @param string $message
 * @return string
 */
function aione_admin_message_sanitize( $message )
{
    $allowed_tags = array(
        'a' => array(
            'href' => array(),
            'title' => array()
        ),
        'br' => array(),
        'b' => array(),
        'div' => array(),
        'em' => array(),
        'i' => array(),
        'p' => array(),
        'strong' => array(),
    );
    $message = wp_kses($message, $allowed_tags);
    return stripslashes(html_entity_decode($message, ENT_QUOTES));
}

/**
 * Adds admin notice.
 *
 * @param string $message
 * @param string $class
 * @param string $mode 'action'|'echo'
 */
function aione_admin_message( $message, $class = 'updated', $mode = 'action' )
{ 
   
    if ( 'action' == $mode ) {
        
        // 5.2 support for Types pre m2m.
        // TODO: remove this after PHP5.2 support dropping.
        if (version_compare(phpversion(), '5.3', '<')) {
            add_action( 'admin_notices',
                create_function( '$a=1, $class=\'' . $class . '\', $message=\''
                        . htmlentities( $message, ENT_QUOTES ) . '\'',
                            '$screen = get_current_screen(); if (!$screen->is_network) echo "<div class=\"message $class\"><p>" . aione_admin_message_sanitize ($message) . "</p></div>";' ) );
        } else { 
            add_action( 'admin_notices', function() use ($class, $message) {
                $message = htmlentities( $message, ENT_QUOTES );
                $screen = get_current_screen();
                if ( ! $screen->is_network ) {
                    echo '<div class="message ' . $class . '"><p>' . aione_admin_message_sanitize ($message) . '</p></div>';
                }
            } );
        }
    } elseif ( 'echo' == $mode ) {
        printf(
            '<div class="message %s is-dismissible"><p>%s</p> <button type="button" class="notice-dismiss">
            <span class="screen-reader-text">
               '. __( 'Dismiss this notice.' ) .'
            </span>
        </button></div>',
            $class,
            aione_admin_message_sanitize($message)
        );
    }
}

/**
 * Shows stored messages.
 */
function aione_show_admin_messages($mode = 'action')
{
    $messages = get_option( 'aione-messages', array() );
    $messages_for_user = isset( $messages[get_current_user_id()] ) ? $messages[get_current_user_id()] : array();
    $dismissed = get_option( 'aione_dismissed_messages', array() );
    if ( !empty( $messages_for_user ) && is_array( $messages_for_user ) ) {
        foreach( $messages_for_user as $message_id => $message ) {
            if( ! in_array( $message['keep_id'], $dismissed ) ) {
                aione_admin_message( $message['message'], $message['class'], $mode );
            }
            if( empty( $message['keep_id'] )
                || in_array( $message['keep_id'], $dismissed )
            ) {
                unset( $messages[ get_current_user_id() ][ $message_id ] );
            }
        }
    }
    update_option( 'aione-messages', $messages );
}

/**
 * Stores admin notices if redirection is performed.
 *
 * @param string $message
 * @param string $class
 */
function aione_admin_message_store( $message, $class = 'updated', $keep_id = false )
{
    /**
     * Allow to store or note
     *
     * Filter allow to turn off storing messages in Types
     *
     * @since 1.6.6
     *
     * @param boolean $var default value is true to show messages
     */
    if (!apply_filters('aione_admin_message_store', true) ) {
        return;
    }
    $messages = get_option( 'aione-messages', array() );
    $messages[get_current_user_id()][md5( $message )] = array(
        'message' => $message,
        'class' => $class,
        'keep_id' => $keep_id ? $keep_id : false,
    );
    update_option( 'aione-messages', $messages );
}

/**
 * Admin notice with dismiss button.
 *
 * @param type $ID
 * @param string $message
 * @param type $store
 * @return boolean
 */
function aione_admin_message_dismiss( $ID, $message, $store = true ) {
    $dismissed = get_option( 'aione_dismissed_messages', array() );
    if ( in_array( $ID, $dismissed ) ) {
        return false;
    }
    $message = $message . '<div style="float:right; margin:-15px 0 0 15px;"><a onclick="jQuery(this).parent().parent().fadeOut();jQuery.get(\''
            . admin_url( 'admin-ajax.php?action=aione_ajax&amp;aione_action=dismiss_message&amp;id='
                    . $ID . '&amp;_wpnonce=' . wp_create_nonce( 'dismiss_message' ) ) . '\');return false;"'
            . 'class="button-secondary" href="javascript:void(0);">'
            . __( 'Dismiss', 'aione-app-builder' ) . '</a></div>';
    if ( $store ) {
        aione_admin_message_store( $message, 'updated', $ID );
    } else {
        aione_admin_message( $message );
    }
}

/**
 * Checks if message is dismissed.
 *
 * @param type $message_id
 * @return boolean
 */
function aione_message_is_dismissed( $message_id ) {
    return in_array( $message_id,
                    (array) get_option( '_aione_dismissed_messages', array() ) );
}

/**
 * Adds dismissed message to record.
 *
 * @param type $ID
 */
function aione_admin_message_set_dismissed( $ID ) {
    $messages = get_option( 'aione_dismissed_messages', array() );
    if ( !in_array( $ID, $messages ) ) {
        $messages[] = $ID;
        update_option( 'aione_dismissed_messages', $messages );
    }
}

/**
 * Removes dismissed message from record.
 *
 * @param type $ID
 */
function aione_admin_message_restore_dismissed( $ID ) {
    $messages = get_option( 'aione_dismissed_messages', array() );
    $key = array_search( $ID, $messages );
    if ( $key !== false ) {
        unset( $messages[$key] );
        update_option( 'aione_dismissed_messages', $messages );
    }
}

function aione_ajax_helper_print_error_and_die(){
    echo json_encode(array(
        'output' => __('Missing required data.', 'aione-app-builder'),
    ));
    die;
}
/**
 * Various delete/deactivate content actions.
 *
 * @param type $type
 * @param type $arg
 * @param type $action
 */
function aione_admin_deactivate_content($type, $arg, $action = 'delete'){
    switch ( $type ) {
        case 'post_type':
            // Clean tax relations
            if ( $action == 'delete' ) {
                $custom = get_option( AIONE_OPTION_NAME_TAXONOMIES, array() );
                foreach ( $custom as $post_type => $data ) {
                    if ( empty( $data['supports'] ) ) {
                        continue;
                    }
                    if ( array_key_exists( $arg, $data['supports'] ) ) {
                        unset( $custom[$post_type]['supports'][$arg] );
                        $custom[$post_type][AIONE_EDIT_LAST] = time();
                    }
                }
                update_option( AIONE_OPTION_NAME_TAXONOMIES, $custom );
            }
            break;

        case 'taxonomy':
            // Clean post relations
            if ( $action == 'delete' ) {
                $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
                $custom = $post_type_option->get_components();
                foreach ( $custom as $post_type => $data ) {
                    if ( empty( $data['taxonomies'] ) ) {
                        continue;
                    }
                    if ( array_key_exists( $arg, $data['taxonomies'] ) ) {
                        unset( $custom[$post_type]['taxonomies'][$arg] );
                        $custom[$post_type][AIONE_EDIT_LAST] = time();
                    }
                }
                update_option( AIONE_OPTION_NAME_COMPONENTS, $custom );
            }
            break;

        default:
            break;
    }
}

function aione_ajax_helper_verification_failed_and_die()
{
    echo json_encode(array(
        'output' => __('Verification failed.', 'aione-app-builder'),
    ));
    die;
}


function aione_admin_dashboard_boxes(){
    $dashboard = '<div class="aione-app-builder-dashboard ar s-columns-1 m-columns-1 l-columns-3">';
    $dashboard .= aione_admin_dashboard_component_box();
    $dashboard .= aione_admin_dashboard_taxonomy_box();
    $dashboard .= aione_admin_dashboard_template_box();
    $dashboard .= '</div>';
    echo $dashboard;
}

function aione_admin_dashboard_component_box(){
    $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
    $custom_components = $post_type_option->get_components();
    $buildin_components = aione_get_builtin_in_post_types();
    foreach ($buildin_components as $key => $value) {
        unset($custom_components[$key]);
    }

    $output = '';
    $output .= '
        <div class="ac">
            <div class="wrapper aione-border bg-white">
                <div class="aione-title aione-border-bottom p-5">
                    <h6 class="aione-float-left pl-5 aione-float-left">Components</h6>
                      ';
                      $output .= sprintf(' 
                            <a class="aione-button small circle white color bg-blue-grey bg-darken-4 aione-float-right"  href="%s">%s</a>',
                            esc_url(add_query_arg( array('page'=>'aione-edit-component'), admin_url('admin.php'))),
                            '<span class="icon"><i class="ion ion-md-add-circle-outline"></i></span><span class="text"> Add New </span>'
                        );
                      $output .= '
                    <div class="aione-clear"></div>
                </div>
                <div class="">
                ';

                if(!empty($custom_components)){
                    foreach ($custom_components as $key => $value) {
                        $edit_link = esc_url(
                            add_query_arg(
                                array(
                                    'page' => 'aione-edit-component',
                                    'aione-component-slug' => $value['slug'],
                                ),
                                admin_url('admin.php')
                            )
                        );
                        $output .= '<div>'.sprintf('<a href="%s" class="display-block pv-10 pl-10 aione-border-bottom">%s</a>', $edit_link, __($value['labels']['name'], 'aione-app-builder')).'</div>';
                    }
                } else{
                    $output .= '<p class="p-10">No components available.You can add new componant</p>';
                }

            $output .= '            
                </div>
            </div>
        </div>';

    return $output;
}
function aione_admin_dashboard_taxonomy_box(){
    $custom_taxonomies = get_option(AIONE_OPTION_NAME_TAXONOMIES, array());
    $builtin_taxonomies = aione_get_builtin_in_taxonomies();
    foreach ($builtin_taxonomies as $key => $value) {
        unset($custom_taxonomies[$key]);
    }

    $output = '';
    $output .= '
        <div class="ac">
            <div class="wrapper aione-border bg-white">
                <div class="aione-title aione-border-bottom p-5">
                    <h6 class="aione-float-left pl-5 aione-float-left">Taxonomies</h6>
                      ';
                      $output .= sprintf(' 
                            <a class="aione-button small circle white color bg-blue-grey bg-darken-4 aione-float-right"  href="%s">%s</a>',
                            esc_url(add_query_arg( array('page'=>'aione-edit-taxonomy'), admin_url('admin.php'))),
                            '<span class="icon"><i class="ion ion-md-add-circle-outline"></i></span><span class="text"> Add New </span>'
                        );
                      $output .= '
                    <div class="aione-clear"></div>
                </div>
                <div class="">
                ';

                if(!empty($custom_taxonomies)){
                    foreach ($custom_taxonomies as $key => $value) {
                        $edit_link = esc_url(
                            add_query_arg(
                                array(
                                    'page' => 'aione-edit-taxonomy',
                                    'aione-taxonomy-slug' => $value['slug'],
                                ),
                                admin_url('admin.php')
                            )
                        );
                        $output .= '<div>'.sprintf('<a href="%s" class="display-block pv-10 pl-10 aione-border-bottom">%s</a>', $edit_link, __($value['labels']['name'], 'aione-app-builder')).'</div>';
                    }
                } else{
                    $output .= '<p class="p-10">No taxonomies available. You can add new taxonomy</p>';
                }

            $output .= '            
                </div>
            </div>
        </div>';
        
    return $output;
}
function aione_admin_dashboard_template_box(){
    $custom_templates = get_option(AIONE_OPTION_NAME_TEMPLATES, array());

    $output = '';
    $output .= '
        <div class="ac">
            <div class="wrapper aione-border bg-white">
                <div class="aione-title aione-border-bottom p-5">
                    <h6 class="aione-float-left pl-5 aione-float-left">Templates</h6>
                      ';
                      $output .= sprintf(' 
                            <a class="aione-button small circle white color bg-blue-grey bg-darken-4 aione-float-right"  href="%s">%s</a>',
                            esc_url(add_query_arg( array('page'=>'aione-edit-template'), admin_url('admin.php'))),
                            '<span class="icon"><i class="ion ion-md-add-circle-outline"></i></span><span class="text"> Add New </span>'
                        );
                      $output .= '
                    <div class="aione-clear"></div>
                </div>
                <div class="">
                ';

                if(!empty($custom_templates)){
                    foreach ($custom_templates as $key => $value) {
                        $edit_link = esc_url(
                            add_query_arg(
                                array(
                                    'page' => 'aione-edit-template',
                                    'aione-template-slug' => $value['slug'],
                                ),
                                admin_url('admin.php')
                            )
                        );
                        $output .= '<div>'.sprintf('<a href="%s" class="display-block pv-10 pl-10 aione-border-bottom">%s</a>', $edit_link, __($value['name'], 'aione-app-builder')).'</div>';
                    }
                } else{
                    $output .= '<p class="p-10">No templates available. You can add new template</p>';
                }

            $output .= '            
                </div>
            </div>
        </div>';
        
    return $output;
}


function aione_admin_validation_messages( $method = false, $sprintf = '' ) {
    $messages = array(
        'required' => __( 'This field is required.', 'aione-app-builder' ),
        'email' => __( 'Please enter a valid email address.', 'aione-app-builder' ),
        'url' => __( 'Please enter a valid URL address.', 'aione-app-builder' ),
        'date' => __( 'Please enter a valid date.', 'aione-app-builder' ),
        'digits' => __( 'Please enter numeric data.', 'aione-app-builder' ),
        'number' => __( 'Please enter numeric data.', 'aione-app-builder' ),
        'alphanumeric' => __( 'Letters, numbers, spaces or underscores only please.', 'aione-app-builder' ),
        'nospecialchars' => __( 'Letters, numbers, spaces, underscores and dashes only please.', 'aione-app-builder' ),
        'rewriteslug' => __( 'Letters, numbers, slashes, underscores and dashes only please.', 'aione-app-builder' ),
        'negativeTimestamp' => __( 'Please enter a date after 1 January 1970.', 'aione-app-builder' ),
        'maxlength' => sprintf( __( 'Maximum of %s characters exceeded.', 'aione-app-builder' ), strval( $sprintf ) ),
        'minlength' => sprintf( __( 'Minimum of %s characters has not been reached.', 'aione-app-builder' ), strval( $sprintf ) ),
        
        'skype' => __( 'Letters, numbers, dashes, underscores, commas and periods only please.', 'aione-app-builder' ),
    );
    if ( $method ) {
        return isset( $messages[$method] ) ? $messages[$method] : '';
    }
    return $messages;
}



function aione_admin_import_export_components_boxes(){
    $components = '<div class="aione-app-builder-import-export-components ar s-columns-1 m-columns-1 l-columns-2">';
    $components .= aione_admin_export_component_box();
    $components .= aione_admin_import_component_box();
    $components .= '</div>';
    echo $components;

}

function aione_admin_export_component_box(){
    $output = '';
    $output .= '
        <div class="ac">
            <div class="wrapper aione-border bg-white">
                <div class="aione-title aione-border-bottom p-5">
                    <h6 class="aione-float-left pl-5 aione-float-left">Export Components, Taxonomies and Templates</h6>
                      ';
                        $output .= sprintf(' 
                            <button type="submit" name="action" class="button button-primary" value="download">Export File</button>'
                        );
                        $output .= '
                    <div class="aione-clear"></div>
                </div>
            </div>
        </div>';

    return $output;
}

function aione_admin_import_component_box(){
    $output = '';
    $output .= '
        <div class="ac">
            <div class="wrapper aione-border bg-white">
                <div class="aione-title aione-border-bottom p-5">
                    <h6 class="aione-float-left pl-5 aione-float-left">Import Components, Taxonomies and Templates</h6>
                      ';
                        $output .= '<form method="post" enctype="multipart/form-data">
                                        <p>Select the Advanced Custom Fields JSON file you would like to import. When you click the import button below, ACF will import the field groups.</p>
                                        <div class="acf-fields">
                                            <div class="acf-field acf-field-file" data-name="acf_import_file" data-type="file">
                                                <div class="acf-label">
                                                    <label for="acf_import_file">Select File</label>
                                                </div>
                                                <div class="acf-input">
                                                    <div class="acf-file-uploader" data-library="all" data-mime_types="" data-uploader="basic">
                                                        <input type="hidden" name="acf_import_file" value="0" data-name="id">   
                                                        <div class="show-if-value file-wrap">
                                                            <div class="file-icon">
                                                                <img data-name="icon" src="" alt="">
                                                            </div>
                                                            <div class="file-info">
                                                                <p>
                                                                    <strong data-name="title"></strong>
                                                                </p>
                                                                <p>
                                                                    <strong>File name:</strong>
                                                                    <a data-name="filename" href="" target="_blank"></a>
                                                                </p>
                                                                <p>
                                                                    <strong>File size:</strong>
                                                                    <span data-name="filesize"></span>
                                                                </p>
                                                            </div>
                                                            <div class="acf-actions -hover">
                                                                <a class="acf-icon -cancel dark" data-name="remove" href="#" title="Remove"></a>
                                                            </div>
                                                        </div>
                                                        <div class="hide-if-value">
                                                            <label class="acf-basic-uploader">
                                                                <input type="file" name="acf_import_file" id="acf_import_file">         
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <p class="acf-submit">
                                            <input type="submit" class="button button-primary" value="Import File">
                                        </p>
                                        <input type="hidden" name="_acf_nonce" value="5d56778e7f">      
                                    </form>';
                        $output .= '
                    <div class="aione-clear"></div>
                </div>
            </div>
        </div>';

    return $output;
}