<?php

if ( ! class_exists( 'Toolset_WPML_Compatibility', false ) ) {


	/**
	 * Handle the shared interoperability tasks between WPML and any/all Toolset plugins.
	 *
	 * @since unknown
	 */
	class Toolset_WPML_Compatibility {

		private static $instance;


		public static function get_instance() {
			if ( null == self::$instance ) {
				self::$instance = new self();
			}

			return self::$instance;
		}


		public static function initialize() {
			self::get_instance();
		}


		private function __clone() { }


		private function __construct() {

			add_action( 'init', array( $this, 'maybe_add_wpml_string_stub_shortcode' ), 100 );

			/**
			 * toolset_is_wpml_active_and_configured
			 *
			 * Check whether WPML core is active and configured properly.
			 *
			 * Note: Beware when calling this early, especially before 'init'. The behaviour depends
			 * on WPML and hasn't been tested.
			 *
			 * @since 2.3
			 */
			add_filter( 'toolset_is_wpml_active_and_configured', array( $this, 'filter_is_wpml_active_and_configured' ) );

		}


		/**
		 * In case WPML ST isn't active, add a stub "wpml-string" shortcode that will only
		 * return its content.
		 *
		 * This is to avoid printing of the unprocessed shortcode.
		 *
		 * @since unknown
		 */
		public function maybe_add_wpml_string_stub_shortcode() {
			if ( ! $this->is_wpml_st_active() ) {
				add_shortcode( 'wpml-string', array( $this, 'stub_wpml_string_shortcode' ) );
			}
		}


		/**
		 * Stub for the wpml-string shortcode.
		 *
		 * Make it as if the shortcode wasn't there.
		 *
		 * @param $atts
		 * @param string $value
		 *
		 * @return string
		 * @since unknown
		 */
		public function stub_wpml_string_shortcode( /** @noinspection PhpUnusedParameterInspection */ $atts, $value ) {
			return do_shortcode( $value );
		}


		/**
		 * Check whether WPML core is active and configured.
		 *
		 * The result is cached for better performance.
		 *
		 * @param bool $use_cache
		 *
		 * @return bool
		 * @since 2.3
		 */
		public function is_wpml_active_and_configured( $use_cache = true ) {

			static $result = null;

			if( null === $result || ! $use_cache ) {
				global $sitepress;
				$is_wpml_active = (
					defined( 'ICL_SITEPRESS_VERSION' )
					&& ! ICL_PLUGIN_INACTIVE
					&& ! is_null( $sitepress )
					&& class_exists( 'SitePress' )
				);

				$is_wpml_configured = apply_filters( 'wpml_setting', false, 'setup_complete' );

				$result = ( $is_wpml_active && $is_wpml_configured );
			}

			return $result;
		}


		/**
		 * Callback for toolset_is_wpml_active_and_configured.
		 *
		 * Instead of calling this directly, use is_wpml_configured_and_active().
		 *
		 * @param mixed $default_value Ignored.
		 * @return bool
		 * @since 2.3
		 */
		public function filter_is_wpml_active_and_configured( /** @noinspection PhpUnusedParameterInspection */ $default_value ) {
			return $this->is_wpml_active_and_configured();
		}


		/**
		 * Check whether WPML ST is active.
		 *
		 * This will return false when WPML is not configured.
		 *
		 * @return bool
		 * @since 2.3
		 */
		public function is_wpml_st_active() {

			if ( ! $this->is_wpml_active_and_configured() ) {
				return false;
			}

			return ( defined( 'WPML_ST_VERSION') );
		}


		/**
		 * Get the version of WPML core, if it's defined.
		 *
		 * @return null|string
		 * @since 2.3
		 */
		public function get_wpml_version() {
			return ( defined( 'ICL_SITEPRESS_VERSION' ) ? ICL_SITEPRESS_VERSION : null );
		}

	}

}