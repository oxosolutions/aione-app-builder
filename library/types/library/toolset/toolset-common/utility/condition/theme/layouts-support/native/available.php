<?php

/**
 * Toolset_Condition_Theme_Layouts_Support_Native_Available
 *
 * @since 2.3.0
 */
class Toolset_Condition_Theme_Layouts_Support_Native_Available implements Toolset_Condition_Interface {

	private static $is_met_result;

	public function is_met() {
		if( self::$is_met_result !== null  ) {
			// we have a cached result
			return self::$is_met_result;
		}

		if( ! function_exists( 'file_get_contents' ) || ! function_exists( 'glob' ) ) {
			// some "smart" providers disabling these functions for "security"
			error_log( 'Toolset_Condition_Theme_Layouts_Support_Native_Available Error: False result because' .
			           ' "file_get_contents" or "glob" not available.' );
			self::$is_met_result = false;
			return false;
		}

		// theme path (and child theme path if active)
		$theme_paths = array_unique( array( get_template_directory(), get_stylesheet_directory() ) );
		$search_pattern = '{' . implode( $theme_paths, ',' ) . '}/{index,single,archive}*.php';

		foreach( glob( $search_pattern, GLOB_BRACE ) as $file ) {
			$file_content = @file_get_contents( $file );
			if( is_string( $file_content ) && strpos( $file_content, 'the_ddlayout' ) !== false ) {
				// native Layouts support!
				self::$is_met_result = true;
				return true;
			}
		}

		// no native Layouts support
		self::$is_met_result = false;
		return false;
	}

}