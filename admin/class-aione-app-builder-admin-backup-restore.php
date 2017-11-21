<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       www.oxosolutions.com
 * @since      1.0.0
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Aione_App_Builder
 * @subpackage Aione_App_Builder/admin
 * @author     AmritDeep <amritdeepkaur@gmail.com>
 */
class Aione_App_Builder_Admin_Backup_Restore{

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;
		add_action( 'admin_menu', array( $this, 'aione_backup_restore_register_menu' ) );
		$this->plugin_admin_aione_backup_list = new Aione_App_Builder_Admin_Backup_List_Table( $this->plugin_name, $this->version );
	}

	function aione_backup_restore_register_menu(){
		add_submenu_page( 'aione_app_builder', 'Backup/Restore', 'Backup/Restore', 'manage_options', 'aione-backup-restore', array( $this,'aione_admin_menu_summary_backup_restore') );
	}

	function aione_admin_menu_summary_backup_restore(){
		
		if(isset( $_POST['_wpnonce_aione_backup'] ) && wp_verify_nonce( $_POST['_wpnonce_aione_backup'], plugin_basename( __FILE__ ) )){
			$raw_array = array();
			$theme_options = get_option('theme_options', array());
			$theme_content = get_option('theme_content', array());
			$timestamp = current_time('timestamp');
			$raw_array[$timestamp]['theme_options'] = $theme_options;
			$raw_array[$timestamp]['theme_content'] = $theme_content;
			//echo "<pre>";print_r($previous_backup);echo "</pre>";
			//echo "<pre>";print_r($raw_array);echo "</pre>";
			$backup = update_option('aione-backup-'.$timestamp, $raw_array);

			if($backup){
				show_message("Backup stored in database successfully!");
			} else {
				show_message("Something went wrong !");
			}
		} 
		?>
		<div class="wrap">
		<h1> Backup Design Settings </h1>
		<form method="post">
		<p class="submit"><input type="submit" id="submit_button" name="aione-backup" class="button button-primary" value="Backup"></p>
		<?php wp_nonce_field( plugin_basename( __FILE__ ), '_wpnonce_aione_backup' ); ?>
		</form>

		<h1>List of available Backup</h1>
		<?php
		$listTable = $this->plugin_admin_aione_backup_list;
        $listTable->prepare_items();
        ?>
        <form id="aione-backup" method="post">
            <!-- For plugins, we also need to ensure that the form posts back to our current page -->
            <input type="hidden" name="page" value="<?php echo esc_attr($_REQUEST['page']); ?>" />
            <?php $listTable->search_box(__('Search Backup', 'aione'), 'search_id'); ?>
            <!-- Now we can render the completed list table -->
            <?php $listTable->display(); ?>
        </form>
		</div>
		<?php
	}

}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}
class Aione_App_Builder_Admin_Backup_List_Table extends WP_List_Table{
	var $backup;
	function __construct() {
        //Set parent defaults
        $args = wp_parse_args( $args, array(
            'plural' => '',
            'singular' => '',
            'ajax' => false,
            'screen' => null,
        ) );

        global $wpdb;
        $options_table = $wpdb->prefix . "options";
		$this->backup = $wpdb->get_results( 'SELECT * FROM '.$options_table.' WHERE option_name LIKE "%aione-backup-%"' );
       
        //echo "<pre>";print_r($this->backup);echo "</pre>";
    }

    function column_default($item, $column_name){
        switch($column_name){
            case 'id':
            return $item['id'];
            case 'name':
                return $item['name'];
            case 'date':
                return $item['date'];
            default:
                return print_r($item,true); //Show the whole array for troubleshooting purposes
        }
    }

    function column_cb($item){
        return sprintf(
                '<input type="checkbox" name="%s[]" value="%s" />',
                $this->bulk_action_field_name,
                $item['slug']
            );
    }

    function get_columns(){
        $columns = array(
            'cb'          => '<input type="checkbox" />', //Render a checkbox instead of text
            'id'       => __('ID', 'aione'),
            'name' => __('Name', 'aione'),
            'date'      => __('Date', 'aione'),
        );
        return $columns;
    }

    function get_sortable_columns(){
        $sortable_columns = array(
            'id'       => array('id',true)
        );
        return $sortable_columns;
    }

    function prepare_items(){
    	$columns = $this->get_columns();
        $hidden = array();
        $sortable = $this->get_sortable_columns();
        $this->_column_headers = array($columns, $hidden, $sortable);

        $s = isset($_POST['s'])? mb_strtolower(trim($_POST['s'])):false;

        $data = array();
        if ( !empty($this->backup) ){
        	foreach( $this->backup as $backup_data ){ 
        		$option_date = str_replace("aione-backup-","",$backup_data->option_name);
        		$option_date = date("Y-M-d h:i:s",$option_date);
        		$one = array(
                    'id' => $backup_data->option_id,
                    'name' => $backup_data->option_name,
                    'date' => $option_date,
                );
                $data[] = $one;
        	}
        } 
        usort( $data, array( &$this, 'sort_data' ) );
        $per_page = 10;
        $current_page = $this->get_pagenum();
        $this->screen = get_current_screen();
        $total_items = count($data);
        $data = array_slice($data,(($current_page-1)*$per_page),$per_page);
        $this->items = $data;
        $this->set_pagination_args( array(
            'total_items' => $total_items,                  //WE have to calculate the total number of items
            'per_page'    => $per_page,                     //WE have to determine how many items to show on a page
            'total_pages' => ceil($total_items/$per_page)   //WE have to calculate the total number of pages
        ) );
    }

    public function single_row($item){
        static $row_class = '';
        $row_class = ( $row_class == '' ? 'alternate' : '' );

        printf('<tr class="%s status-%s">', $row_class, $item['status']);
        $this->single_row_columns( $item );
        echo '</tr>';
    }

     private function sort_data( $a, $b ) {
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