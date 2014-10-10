<?php

/**
 * Create or Check for errors 
 *
 * @link       http://issuepress.co
 * @since      1.0.0
 *
 * @package    IssuePress
 * @subpackage IssuePress/includes
 */

/**
 * Error Handling Object.
 *
 * Creates an instance of WP_Error to use for IP Error Handling
 *
 * @package    IssuePress
 * @subpackage IssuePress/includes
 * @author     Matthew Simo <matthew.simo@liftux.com>
 */
class IssuePress_Error_Handling {

	private $errors;

	public function __construct ( ) {

		$this->errors = new WP_Error();

	}

	/**
	 * Add an error to the IP Error Handler
	 *
	 * @since			1.0.0
	 */
	public function add_error( $code = '', $message = '', $data = 'ip-error' ) {

		$this->errors->add( $code, $message, $data );

	}

	/**
	 * Returns if there are any errors
	 *
	 * @since			1.0.0
	 * @return		Bool
	 */
	public function has_errors() {

		$has_errors = $this->errors->get_error_codes() ? true : false;
		return apply_filters( 'ip_has_errors',  $has_errors, $this->errors );

	}

	/**
	 * Returns the WP_Error IP Instance
	 *
	 * @since			1.0.0
	 * @return		Class
	 */
	public function get_errors() {
		return $this->errors;
	}

}
