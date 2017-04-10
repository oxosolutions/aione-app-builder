<?php
/**
 * Class Toolset_Controller_Admin_Notices
 * Controls all Toolset related admin notices
 *
 * @since 2.3.0 First release of Toolset_Admin_Notice_Dismissible
 *              All containing properties and methods without since tag are part of the initial release
 */
class Toolset_Controller_Admin_Notices {
	protected $current_screen;

	protected $tpl_path;

	protected $is_types_active;
	protected $is_views_active;
	protected $is_layouts_active;
	protected $is_cred_active;
	protected $is_access_active;
	protected $is_tbt_active;
	protected $is_tbt_inactive;

	public function __construct() {
		add_action( 'after_setup_theme', array( $this, 'init' ), 1000 );
	}

	public function init() {
		$this->tpl_path = TOOLSET_COMMON_PATH . '/templates/admin/notice';

		$condition = new Toolset_Condition_Plugin_Types_Active();
		$this->is_types_active = $condition->is_met();

		$condition = new Toolset_Condition_Plugin_Views_Active();
		$this->is_views_active = $condition->is_met();

		$condition = new Toolset_Condition_Plugin_Layouts_Active();
		$this->is_layouts_active = $condition->is_met();

		$condition = new Toolset_Condition_Plugin_Cred_Active();
		$this->is_cred_active = $condition->is_met();

		$condition = new Toolset_Condition_Plugin_Access_Active();
		$this->is_access_active = $condition->is_met();

		$condition = new Toolset_Condition_Theme_Toolset_Based_Active();
		$this->is_tbt_active = $condition->is_met();

		$condition = new Toolset_Condition_Theme_Toolset_Based_Inactive();
		$this->is_tbt_inactive = $condition->is_met();

		add_action('current_screen', array( $this, 'init_screens') );
	}

	/**
	 * Initialize where to show the notices
	 * Fired on hook 'current_screen'
	 */
	public function init_screens() {
		if( ! function_exists( 'get_current_screen' ) ) {
			// loaded to early
			return;
		}

		$this->current_screen = get_current_screen();

		$this->screen_any();
		$this->screen_toolset_dashboard();
		$this->screens_toolset_toplevel_pages();
		$this->screen_wordpress_dashboard();
		$this->screen_wordpress_plugins();
		$this->screen_wordpress_themes();
	}

	/**
	 * Notices for any page on the admin screen
	 */
	protected function screen_any() {
		if( ! is_admin() ) {
			return;
		}
	}

	/**
	 * Get current screen id
	 * @return bool
	 */
	protected function get_current_screen_id() {
		if( is_object( $this->current_screen ) and property_exists( $this->current_screen, 'id' ) ) {
			return $this->current_screen->id;
		}

		return false;
	}

	/**
	 * Notices for all Toolset toplevel pages
	 */
	protected function screens_toolset_toplevel_pages() {
		if( ! $current_screen_id = $this->get_current_screen_id() ) {
			// no screen id
			return;
		}

		if(
			$current_screen_id == 'toolset_page_wpcf-cpt'                // Post Types
			|| $current_screen_id == 'toolset_page_wpcf-ctt'                // Taxonomies
			|| $current_screen_id == 'toolset_page_wpcf-cf'                 // Post Fields
			|| $current_screen_id == 'toolset_page_wpcf-termmeta-listing'   // Term Fields
			|| $current_screen_id == 'toolset_page_wpcf-um'                 // User Fields
			|| $current_screen_id == 'toolset_page_types_access'            // Access Control
			|| $current_screen_id == 'toolset_page_views'                   // Views
			|| $current_screen_id == 'toolset_page_view-templates'          // Content Templates
			|| $current_screen_id == 'toolset_page_view-archives'           // WordPress Archives
			|| $current_screen_id == 'toolset_page_CRED_Forms'              // Post Forms
			|| $current_screen_id == 'toolset_page_CRED_User_Forms'         // User Forms
			|| $current_screen_id == 'toolset_page_dd_layouts'              // Layouts
			|| $current_screen_id == 'toolset_page_dd_layout_CSS_JS'        // Layouts CSS JS
			// || $current_screen_id == 'toolset_page_toolset-settings'        // Toolset Settings
			// || $current_screen_id == 'toolset_page_toolset-export-import'   // Toolset Settings

		) {
			$this->notices_compilation_introduction();
		}
	}

	/**
	 * Notices for the themes screen
	 */
	protected function screen_wordpress_themes() {
		if( $this->get_current_screen_id() != 'themes' ) {
			return;
		}

		$this->notices_compilation_introduction();
	}

	/**
	 * Notices for the plugins screen
	 */
	protected function screen_wordpress_plugins() {
		if( $this->get_current_screen_id() != 'plugins' ) {
			return;
		}

		if( $this->is_tbt_active ) {
			// active Toolset Based Theme
			$notice = new Toolset_Admin_Notice_Dismissible( 'tbt-active-dashboard' );
			$notice->set_content( $this->tpl_path . '/toolset-based-themes/active/plugin.phtml' );
			Toolset_Admin_Notices_Manager::add_notice( $notice );
			return;
		}

		if( $this->is_tbt_inactive ) {
			// inactive Toolset Based Theme
			$notice = new Toolset_Admin_Notice_Dismissible( 'tbt-inactive-dashboard' );
			$notice->set_content( $this->tpl_path . '/toolset-based-themes/inactive/plugin.phtml' );
			Toolset_Admin_Notices_Manager::add_notice( $notice );
			return;
		}

		$this->notices_compilation_introduction();
	}

	/**
	 * Notices for the Toolset Dashboard Page
	 */
	protected function screen_wordpress_dashboard() {
		if( $this->get_current_screen_id() != 'dashboard' ) {
			return;
		}

		$this->notices_compilation_introduction();
	}

	/**
	 * Notices for the Toolset Dashboard Page
	 */
	protected function screen_toolset_dashboard() {
		if( $this->get_current_screen_id() != 'toplevel_page_toolset-dashboard' ) {
			return;
		}

		if( $this->is_tbt_active ) {
			// active Toolset Based Theme
			$notice = new Toolset_Admin_Notice_Dismissible( 'tbt-active-dashboard' );
			$notice->set_content( $this->tpl_path . '/toolset-based-themes/active/dashboard.phtml' );
			Toolset_Admin_Notices_Manager::add_notice( $notice );
			return;
		}

		if( $this->is_tbt_inactive ) {
			// inactive Toolset Based Theme
			$notice = new Toolset_Admin_Notice_Dismissible( 'tbt-inactive-dashboard' );
			$notice->set_content( $this->tpl_path . '/toolset-based-themes/inactive/dashboard.phtml' );
			Toolset_Admin_Notices_Manager::add_notice( $notice );
			return;
		}

		// no Toolset Based Theme
		$this->notices_compilation_introduction();
	}

	/**
	 * These are our "Toolset Introduction Messages"
	 * for further information see toolsetcommon-136
	 */
	protected function notices_compilation_introduction() {
		if( $this->only_types_active() ) {
			// notice: theme we have an integration plugin for
			$this->notice_customize_page_with_toolset();

			// notice: theme we have no integration plugin for
			$this->notice_how_to_design_with_toolset();

			// notice: theme has native layout support
			$this->notice_theme_works_best_with_toolset();

			return;
		}

		// commercial plugin active + theme we have an integration plugin for
		$this->layouts_theme_integration_available();
		$this->layouts_theme_integration_not_active();
		$theme_integration_active_but_theme_not = $this->layouts_theme_integration_theme_not_active();
		$this->integration_run_installer();

		// no integration plugin installed, for which theme exists (but not active)
		// + commercial plugin active
		// + types || views || layouts missing
		// + theme active we have no integration plugin for
		if( ( ! $theme_integration_active_but_theme_not instanceof Toolset_Condition_Interface
		      || ! $theme_integration_active_but_theme_not->conditions_met() )
		    && ( ! $this->is_views_active
		         || ! $this->is_types_active
		         || ! $this->is_layouts_active
		    )
		) {
			$this->layouts_no_theme_integration_available();
		}
	}

	/**
	 * True if only types is active
	 * False if one commercial toolset plugin is active
	 *
	 * @return bool
	 */
	protected function only_types_active() {
		if( $this->is_views_active || $this->is_layouts_active || $this->is_access_active || $this->is_cred_active ) {
			return false;
		}

		return true;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function notice_customize_page_with_toolset() {
		$notice = new Toolset_Admin_Notice_Dismissible( 'customize-page-with-toolset' );
		$notice->set_content( $this->tpl_path . '/only-types-installed/layouts-support-available.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Missing() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Available() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function notice_how_to_design_with_toolset() {
		$notice = new Toolset_Admin_Notice_Dismissible( 'how-to-design-with-toolset' );
		$notice->set_content( $this->tpl_path . '/only-types-installed/layouts-support-missing.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Missing() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Missing() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function notice_theme_works_best_with_toolset() {
		$notice = new Toolset_Admin_Notice_Dismissible( 'theme-works-best-with-toolset' );
		$notice->set_content( $this->tpl_path . '/only-types-installed/layouts-support-native.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Available() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function layouts_theme_integration_available() {
		if( defined( 'TOOLSET_INTEGRATION_PLUGIN_THEME_NAME' ) ) {
			// don't show this message if already a integration plugin is running
			// return;
		}
		$notice = new Toolset_Admin_Notice_Dismissible( 'layouts-theme-integration' );
		$notice->set_content( $this->tpl_path . '/commercial-plugin-installed/layouts-support-available.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Missing() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Available() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Not_Installed() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function layouts_theme_integration_not_active() {
		$notice = new Toolset_Admin_Notice_Dismissible( 'layouts-theme-integration-not-active' );
		$notice->set_content( $this->tpl_path . '/commercial-plugin-installed/layouts-support-plugin-not-active.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Missing() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Available() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Not_Active() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function layouts_theme_integration_theme_not_active() {
		if( defined( 'LAYOUTS_INTEGRATION_THEME_NAME' ) ) {
			// Layouts define this constant if there is an active integration (plugin and theme installed and active)
			return false;
		}

		$notice = new Toolset_Admin_Notice_Dismissible( 'layouts-theme-integration-theme-not-active' );
		$notice->set_content( $this->tpl_path . '/commercial-plugin-installed/layouts-support-theme-not-active.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Missing() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Active() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Theme_Installed() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Theme_Not_Active() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function layouts_no_theme_integration_available() {
		$notice = new Toolset_Admin_Notice_Dismissible( 'layouts-no-theme-integration' );
		$notice->set_content( $this->tpl_path . '/commercial-plugin-installed/layouts-support-missing.phtml' );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Native_Missing() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Plugin_Missing() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function integration_run_installer() {
		$is_integration_plugin_active = new Toolset_Condition_Theme_Layouts_Support_Plugin_Active();

		if( ! $is_integration_plugin_active->is_met() ) {
			// no theme itegration plugin active
			return;
		}

		$theme_slug = sanitize_title( $is_integration_plugin_active->get_supported_theme_name() );

		$notice = new Toolset_Admin_Notice_Dismissible( 'integration-run-installer-for-' . $theme_slug );
		$notice->set_content( $this->tpl_path . '/installer/integration-run-installer.phtml' );
		$notice->add_condition( new Toolset_Condition_Plugin_Layouts_No_Items() );
		$notice->add_condition( new Toolset_Condition_Theme_Layouts_Support_Theme_Active() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}

	/**
	 * @return Toolset_Admin_Notice_Dismissible
	 */
	protected function plugin_encrypted_no_valid_theme() {
		$notice = new Toolset_Admin_Notice_Dismissible( 'plugin-encrypted-no-valid-theme' );
		$notice->set_content( $this->tpl_path . '/toolset-based-themes/plugin-encrypted-no-valid-theme.phtml' );
		$notice->add_condition( new Toolset_Condition_Plugin_Encrypted_No_Valid_Theme() );
		Toolset_Admin_Notices_Manager::add_notice( $notice );

		return $notice;
	}
}