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
 * @author     OXO Solutions <contact@oxosolutions.com>
 */

class Aione_App_Builder_Admin_Components_Utils {


	/**
	 * Get the post types option.
	 *
	 * @return array
	 */
	public function get_components() { 
		$post_types = get_option( AIONE_OPTION_NAME_COMPONENTS, array() );

		if ( ! is_array( $post_types ) ) {
			$raw_value = $this->get_raw_option();
			if ( is_string( $raw_value ) && ! empty( $raw_value ) ) {
				// Now we know that something went seriously wrong AND we probably have post types to save.
				$post_types = $this->try_fix_serialized_array( $raw_value );
				$post_types = maybe_unserialize( $post_types );
				$post_types = $this->try_fix_post_type_labels( $post_types );
			}
		}

		if ( ! is_array( $post_types ) ) {
			return array();
		}

		return $post_types;
	}
	/**
	 * Get the raw AIONE_OPTION_NAME_COMPONENTS option from the database.
	 *
	 * @return null|string
	 */
	private function get_raw_option() {
		global $wpdb;

		$option_value = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT option_value FROM {$wpdb->options} WHERE option_name = %s",
				AIONE_OPTION_NAME_COMPONENTS
			)
		);

		return $option_value;
	}


	/**
	 * Restore a broken serialized array by fixing string lengths.
	 *
	 * @param $broken_serialized_array
	 * @return string
	 */
	private function try_fix_serialized_array( $broken_serialized_array ) {
		$output = preg_replace_callback(
			'!s:(\d+):([\\\\]?"[\\\\]?"|[\\\\]?"((.*?)[^\\\\])[\\\\]?");!',
			array( $this, 'preg_replace_callback' ),
			$broken_serialized_array
		);

		return $output;
	}


	/**
	 * Fix a string length for a single occurence.
	 *
	 * @param array $matches
	 * @return string
	 */
	private function preg_replace_callback( $matches ) {
		if ( count( $matches ) < 4 ) {
			// empty string
			return $matches[0];
		}

		$stored_string = $matches[3];
		$string_mysql_unescaped = $this->unescape_mysql( $stored_string );
		$string_length = strlen( $string_mysql_unescaped );
		$string_without_quotes = $this->unescape_quotes( $stored_string );

		$replacement = 's:' . $string_length . ':"' . $string_without_quotes . '";';

		return $replacement;
	}


	/**
	 * Update the post types option
	 * @param $post_types
	 */
	public function update_components( $post_types ) {
		update_option( AIONE_OPTION_NAME_COMPONENTS, $post_types, true );
	}


	/**
	 * Unescape to avoid dump-text issues.
	 *
	 * @param string $value
	 * @return string
	 */
	private function unescape_mysql( $value ) {
		return str_replace(
			array( "\\\\", "\\0", "\\n", "\\r", "\Z", "\'", '\"' ),
			array( "\\", "\0", "\n", "\r", "\x1a", "'", '"' ),
			$value
		);
	}


	/**
	 * Fix strange behaviour if you have escaped quotes in your replacement
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	private function unescape_quotes( $value ) {
		return str_replace( '\"', '"', $value );
	}


	/**
	 * @param array $post_types
	 * @return array
	 */
	private function try_fix_post_type_labels( $post_types ) {
		foreach ( $post_types as $key => $post_type ) {
			if ( ! array_key_exists( 'labels', $post_type ) ) {
				continue;
			}

			foreach ( $post_type['labels'] as $label_name => $label_value ) {
				$fixed_label = preg_replace( '/\{[a-f0-9]{8,}\}s/', '%s', $label_value );
				$post_types[ $key ]['labels'][ $label_name ] = $fixed_label;
			}
		}

		return $post_types;
	}

}

