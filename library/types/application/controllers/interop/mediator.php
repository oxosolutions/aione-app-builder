<?php

/**
 * Provide interoperability with other plugins or themes when needed.
 *
 * Each plugin or a theme that Types needs to (actively) support should
 * have a dedicated "interoperability handler" that, when initialized,
 * will provide such support (preferably via actions and filters).
 *
 * Having everything located in one class will make it very easy to
 * handle and implement future compatibility issues and it will
 * reduce memory usage by loading the code only when needed.
 *
 * @since 2.2.7
 */
class Types_Interop_Mediator {

	private static $instance;

	public static function initialize() {
		if( null === self::$instance ) {
			self::$instance = new self();
		}

		// Not giving away the instance on purpose
	}


	private function __clone() { }

	private function __construct() {
		$this->initialize_interop_handlers();
	}


	/**
	 * Get definitions of all interop handlers.
	 *
	 * Each one has a method for checking whether the handler is needed
	 * and a name - there must be a corresponding class Types_Interop_Handler_{$name}
	 * implementing the Types_Interop_Handler_Interface.
	 *
	 * @return array
	 * @since 2.2.7
	 */
	private function get_interop_handler_definitions() {

		$interop_handlers = array(
			array(
				'is_needed' => array( $this, 'is_wpml_active' ),
				'class_name' => 'Wpml'
			),
			array(
				'is_needed' => array( $this, 'is_divi_active'),
				'class_name' => 'Divi'
			),
			array(
				'is_needed' => array( $this, 'is_use_any_font_active' ),
				'class_name' => 'Use_Any_Font'
			)
		);

		return $interop_handlers;
	}


	/**
	 * Load and initialize interop handlers if the relevant plugin/theme is active.
	 *
	 * @since 2.2.7
	 */
	private function initialize_interop_handlers() {

		$interop_handlers = $this->get_interop_handler_definitions();
		foreach( $interop_handlers as $handler_definition ) {
			$is_needed = call_user_func( $handler_definition['is_needed'] );

			if( $is_needed ) {
				$handler_class_name = 'Types_Interop_Handler_' . $handler_definition['class_name'];
				call_user_func( $handler_class_name . '::initialize' );
			}
		}
	}


	/**
	 * Check whether WPML is active and configured.
	 *
	 * @return bool
	 * @since 2.2.7
	 */
	protected function is_wpml_active() {

		global $sitepress;
		$is_wpml_active = (
			defined( 'ICL_SITEPRESS_VERSION' )
			&& ! ICL_PLUGIN_INACTIVE
			&& ! is_null( $sitepress )
			&& class_exists( 'SitePress' )
		);

		return $is_wpml_active;
	}


	/**
	 * Check whether the Divi theme is loaded.
	 *
	 * @return bool
	 */
	protected function is_divi_active() {
		return function_exists( 'et_setup_theme' );
	}


	protected function is_use_any_font_active() {
		return function_exists( 'uaf_activate' );
	}

}