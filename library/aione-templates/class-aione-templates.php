<?php
/**
 * This plugin allows you to include templates with your plugin so that they can
 * be added with any theme.
 *
 * @package Page Template Example
 * @version 1.0.0
 * @since 	0.1.0
 */
class Page_Template_Plugin {
    /**
     * Plugin version, used for cache-busting of style and script file references.
     *
     * @since   1.0.0
     *
     * @var     string
     */
    const VERSION = '1.0.0';
    /**
     * Unique identifier for the plugin.
     *
     * The variable name is used as the text domain when internationalizing strings
     * of text.
     *
     * @since    1.0.0
     *
     * @var      string
     */
    protected $plugin_slug;
	/**
	 * A reference to an instance of this class.
	 *
	 * @since 0.1.0
	 *
	 * @var   Page_Template_Plugin
	 */
	private static $instance;
	/**
	 * The array of templates that this plugin tracks.
	 *
	 * @var      array
	 */
	protected $templates;
	/**
	 * Returns an instance of this class. An implementation of the singleton design pattern.
	 *
	 * @return   Page_Templae_Example    A reference to an instance of this class.
	 * @since    1.0.0
	 */
	public static function get_instance() {
		if( null == self::$instance ) {
			self::$instance = new Page_Template_Plugin();
		} // end if
		return self::$instance;
	} // end getInstance
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 *
	 * @version		1.0.0
     * @since 		1.0.0
	 */
	private function __construct() {
		$this->templates = array();
		$this->plugin_locale = 'pte';
		// Grab the translations for the plugin
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );
		// Add a filter to the page attributes metabox to inject our template into the page template cache.
		//add_filter('page_attributes_dropdown_pages_args', array( $this, 'register_project_templates' ) );
		
 if ( version_compare( floatval($GLOBALS['wp_version']), '4.7', '<' ) ) { // 4.6 and older
		            add_filter(
		                'page_attributes_dropdown_pages_args',
		                array( $this, 'register_project_templates' )
		            );
		      } else { // Add a filter to the wp 4.7 version attributes metabox
		            add_filter(
		                'theme_page_templates', array( $this, 'add_new_template' )
		            );
		      }


		// Add a filter to the save post in order to inject out template into the page cache
		add_filter('wp_insert_post_data', array( $this, 'register_project_templates' ) );
		// Add a filter to the template include in order to determine if the page has our template assigned and return it's path
		add_filter('template_include', array( $this, 'view_project_template'),99 );
		// Register hooks that are fired when the plugin is activated, deactivated, and uninstalled, respectively.
		register_deactivation_hook( __FILE__, array( $this, 'deactivate' ) );
		// Add your templates to this array.
		$this->templates = array(
			'template-single.php' => __( 'Single Template', $this->plugin_slug ),
			'archive-template.php' => __( 'Archive Template', $this->plugin_slug ),
			'template-compare.php' => __( 'Compare Template', $this->plugin_slug ),
		);
		// adding support for theme templates to be merged and shown in dropdown
		$templates = wp_get_theme()->get_page_templates();
		$templates = array_merge( $templates, $this->templates );
	} // end constructor
    /**
     * Load the plugin text domain for translation.
     *
     * @since    1.0.0
     */
    public function load_plugin_textdomain() {
	    $domain = $this->plugin_slug;
	    $locale = apply_filters( 'plugin_locale', get_locale(), $domain );
	    load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain, FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
    } // end load_plugin_textdomain
	/**
	 * Adds our template to the pages cache in order to trick WordPress
	 * into thinking the template file exists where it doens't really exist.
	 *
	 * @param   array    $atts    The attributes for the page attributes dropdown
	 * @return  array    $atts    The attributes for the page attributes dropdown
	 * @verison	1.0.0
	 * @since	1.0.0
	 */
	public function register_project_templates( $atts ) {
		// Create the key used for the themes cache
		$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
		// Retrieve the cache list. If it doesn't exist, or it's empty prepare an array
		$templates = wp_cache_get( $cache_key, 'themes' );
		if ( empty( $templates ) ) {
			$templates = array();
		} // end if
		// Since we've updated the cache, we need to delete the old cache
		wp_cache_delete( $cache_key , 'themes');
		// Now add our template to the list of templates by merging our templates
		// with the existing templates array from the cache.
		$templates = array_merge( $templates, $this->templates );
		// Add the modified cache to allow WordPress to pick it up for listing
		// available templates
		wp_cache_add( $cache_key, $templates, 'themes', 1800 );
		return $atts;
	} // end register_project_templates
	/**
	 * Checks if the template is assigned to the page
	 *
	 * @version	1.0.0
	 * @since	1.0.0
	 */
	public function view_project_template( $template ) {
		global $post;
		// If no posts found, return to
		// avoid "Trying to get property of non-object" error
		if ( !isset( $post ) ) return $template;
		if ( ! isset( $this->templates[ get_post_meta( $post->ID, '_wp_page_template', true ) ] ) ) {
			return $template;
		} // end if
		$file = plugin_dir_path( __FILE__ ) . 'templates/' . get_post_meta( $post->ID, '_wp_page_template', true );
		// Just to be safe, we check if the file exist first
		if( file_exists( $file ) ) {
			return $file;
		} // end if
		return $template;
	} // end view_project_template
	/*--------------------------------------------*
	 * deactivate the plugin
	*---------------------------------------------*/
	static function deactivate( $network_wide ) {
		foreach($this as $value) {
			aione-templates::delete_template( $value );
		}
		
	} // end deactivate
	/*--------------------------------------------*
	 * Delete Templates from Theme
	*---------------------------------------------*/
	public function delete_template( $filename ){				
		$theme_path = get_template_directory();
		$template_path = $theme_path . '/' . $filename;  
		if( file_exists( $template_path ) ) {
			unlink( $template_path );
		}
		// we should probably delete the old cache
		wp_cache_delete( $cache_key , 'themes');
	}
	/**
	 * Retrieves and returns the slug of this plugin. This function should be called on an instance
	 * of the plugin outside of this class.
	 *
	 * @return  string    The plugin's slug used in the locale.
	 * @version	1.0.0
	 * @since	1.0.0
	 */
	public function get_locale() {
		return $this->plugin_slug;
	} // end get_locale
	/**
     * Adds our template to the page dropdown for v4.7+
     *
     **/
    public function add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }
} // end class
