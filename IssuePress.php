<?php
/**
 * IssuePress
 *
 * Simple support, right on WordPress!
 *
 * @package   IssuePress
 * @author    Matthew Simo <matthew.simo@liftux.com>
 * @license   GPL-2.0+
 * @link      http://issuepress.co
 * @copyright 2014 Matthew Simo
 *
 * @wordpress-plugin
 * Plugin Name:       IssuePress
 * Plugin URI:        http://issuepress.co
 * Description:       Simple support, right on WordPress!
 * Version:           1.0.0
 * Author:            Matthew Simo
 * Author URI:        
 * Text Domain:       issuepress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Shared Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'includes/class-issuepress.php' );
include_once( plugin_dir_path( __FILE__ ) . 'includes/class-issuepress-extension.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'IssuePress', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'IssuePress', 'deactivate' ) );

add_action( 'plugins_loaded', array( 'IssuePress', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
 * @TODO:
 *
 * If you want to include Ajax within the dashboard, change the following
 * conditional to:
 *
 * if ( is_admin() ) {
 *   ...
 * }
 *
 * The code below is intended to to give the lightest footprint possible.
 */
if ( is_admin() && ( ! defined( 'DOING_AJAX' ) || ! DOING_AJAX ) ) {

	require_once( plugin_dir_path( __FILE__ ) . 'admin/class-issuepress-admin.php' );
	add_action( 'plugins_loaded', array( 'IssuePress_Admin', 'get_instance' ) );

} else if ( !is_admin() ) {

	require_once( plugin_dir_path( __FILE__ ) . 'public/class-issuepress-public.php' );
	add_action( 'plugins_loaded', array( 'IssuePress_Public', 'get_instance' ) );

}
