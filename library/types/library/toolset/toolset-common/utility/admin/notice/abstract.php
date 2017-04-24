<?php

/**
 * Class Toolset_Admin_Notice_Abstract
 *
 * @since 2.3.0 First release of Toolset_Admin_Notice_Abstract
 *            All containing properties and methods without since tag are part of the initial release
 */
abstract class Toolset_Admin_Notice_Abstract implements Toolset_Admin_Notice_Interface {
	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $content;

	/**
	 * @var int
	 */
	protected $priority = 0;

	/**
	 * @var Toolset_Condition_Interface[]
	 */
	protected $conditions;

	/**
	 * Temporary message
	 * @var bool
	 */
	protected $is_temporary = false;

	/**
	 * By default every of our messages is permanent dismissible
	 *
	 * @var bool
	 */
	protected $is_dismissible_permanent = true;

	/**
	 * $is_dismissible_globally if the message is per user or per installation
	 * e.g. our toolset installer should be per installation (makes no sense to let every user install the site)
	 *
	 * @var bool
	 */
	protected $is_dismissible_globally = false;

	/**
	 * template file
	 */
	protected $template_file;

	/**
	 * Toolset_Admin_Notice constructor.
	 *
	 * @param string $id
	 *
	 * @param string $message
	 *
	 * @throws Exception
	 */
	public function __construct( $id, $message = '' ) {
		if( ! function_exists( 'sanitize_title' ) ) {
			// abort, called to early
			throw new Exception( 'Toolset_Admin_Notice_Abstract Error: "sanitize_title()" does not exists. ' .
			                     'Toolset_Admin_Notice_Abstract::create_notice() was called too early.' );
		}

		if( ! is_string( $id ) ) {
			// no string given
			throw new Exception( 'Toolset_Admin_Notice_Abstract Error: $id must be a string.' );
		}

		if( ! empty( $message ) ) {
			$this->set_content( $message );
		}

		$this->id = sanitize_title( $id );

		// set default template file
		$this->set_default_template_file();
	}

	/**
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * @param string $title
	 */
	public function set_title( $title ) {
		$this->title = $title;
	}

	/**
	 * @return string
	 */
	public function get_title() {
		return $this->title;
	}

	/**
	 * @param string $content
	 *
	 * @return bool
	 */
	public function set_content( $content ) {
		if( ! is_string( $content ) ) {
			return false;
		}

		$this->content = $content;
	}

	/**
	 * @return string
	 */
	public function get_content() {
		return $this->content;
	}

	/**
	 * Output of string
	 */
	public function render_content() {
		if( is_file( $this->content ) ) {
			include( $this->content );
			return;
		}

		echo $this->content;
	}

	/**
	 * Adds a condition
	 *
	 * @param Toolset_Condition_Interface $condition
	 */
	public function add_condition( Toolset_Condition_Interface $condition ) {
		$this->conditions[] = $condition;
	}

	/**
	 * Sets priority of the message
	 *
	 * @param int $priority
	 */
	public function set_priority( $priority ) {
		if( is_numeric( $priority ) ) {
			$this->priority = $priority;
		}
	}

	/**
	 * @return int
	 */
	public function get_priority( ) {
		return $this->priority;
	}

	/**
	 * True or false
	 * @param bool $bool
	 */
	public function set_is_dismissible_permanent( $bool ) {
		$this->is_dismissible_permanent = $bool === false
			? false
			: true;
	}

	/**
	 * @return bool
	 */
	public function is_dismissible_permanent() {
		return $this->is_dismissible_permanent;
	}

	/**
	 * True or false
	 * @param bool $bool
	 */
	public function set_is_dismissible_globally( $bool ) {
		$this->is_dismissible_globally = $bool === false
			? false
			: true;

		if( $this->is_dismissible_globally ) {
			$this->is_dismissible_permanent = true;
		}
	}

	/**
	 * @return bool
	 */
	public function is_dimissibile_globally() {
		return $this->is_dismissible_globally;
	}

	/**
	 * Getter of is_temporary
	 * @return bool
	 */
	public function is_temporary(){
		return $this->is_temporary;
	}

	/**
	 * Print Notice
	 */
	public function render() {
		if( ! file_exists( $this->template_file ) ) {
			error_log( 'Toolset_Admin_Notice_Abstract Error: Template "'. $this->template_file . '" could not be found.' );
			return;
		}

		include( $this->template_file );
	}

	abstract protected function set_default_template_file();

	public function conditions_met() {
		if( empty( $this->conditions ) ) {
			// this notice has no conditions
			return true;
		}

		foreach( $this->conditions as $condition ) {
			if( ! $condition->is_met() ) {
				return false;
			}
		}

		// all conditions met
		return true;
	}

	/**
	 * Dismiss notice
	 */
	public function dismiss() {
		if( ! $this->is_dismissible_permanent() ) {
			error_log( 'Notice with id "' . $this->get_id() . '" is not dismissible.' );
			return;
		}

		Toolset_Admin_Notices_Manager::dismiss_notice_by_id( $this->get_id(), $this->is_dimissibile_globally() );
	}
}