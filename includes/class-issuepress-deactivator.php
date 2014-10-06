<?php

/**
 * Fired during plugin deactivation
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    IssuePress
 * @subpackage IssuePress/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    IssuePress
 * @subpackage IssuePress/includes
 * @author     Matthew Simo <matthew.simo@liftux.com>
 */
class IssuePress_Deactivator {

	/**
	 * Deactivation Method 
	 *
	 * Fired during plugin deactivation.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		delete_option('ip_deferred_admin_notices'); 

	}

}
