<?php
//require_once dirname( __FILE__ ) . '/class-aione-admin-page.php';
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
class Aione_Admin_Component extends Aione_Admin_Page
{
    public function __construct()
    {
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
    public function init_admin()
    {
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
    public function get_post_type($post_type_slug)
    {
        if ( empty($post_type_slug) ) {
            return aione_custom_types_default();
        }
	    $post_type_option = new Aione_App_Builder_Admin_Components_Utils();
        $custom_types = $post_type_option->get_components();
        if ( isset( $custom_types[$post_type_slug] ) ) {
            $post_type = $custom_types[$post_type_slug];
            $post_type['update'] = true;
        } else {
            $buildin_post_types = aione_get_builtin_in_post_types();
            if ( isset($buildin_post_types[$post_type_slug]) ) {
                $post_type = get_object_vars(get_post_type_object($post_type_slug));
                $post_type['labels'] = get_object_vars($post_type['labels']);
                $post_type['slug'] = esc_attr($post_type_slug);
                $post_type['_builtin'] = true;
            } else {
                return false;
            }
        }
        if ( !isset($post_type['update']) ) {
            $post_type['update'] = false;
        }
        return $post_type;
    }
}
