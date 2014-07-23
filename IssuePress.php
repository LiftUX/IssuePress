<?php
/**
 * IssuePress - Simple Support On WordPress
 *
 * Provide simple support to your users right on your WordPress site.
 *
 * @package   IssuePress
 * @author    Matthew Simo <matthew.simo@liftux.com>
 * @license   GPL-2.0+
 * @link      http://example.com
 * @copyright 2014 Your Name or Company Name
 *
 * @wordpress-plugin
 * Plugin Name:       IssuePress 
 * Plugin URI:        http://issuepress.co/ 
 * Description:       Simple support for your users, brought to you by UpThemes.
 * Version:           1.0.0
 * Author:            UpThemes 
 * Author URI:        http://upthemes.com/
 * Text Domain:       issuepress
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/UpThemes/IssuePress
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*----------------------------------------------------------------------------*
 * Public-Facing Functionality
 *----------------------------------------------------------------------------*/

require_once( plugin_dir_path( __FILE__ ) . 'public/class-issuepress.php' );

/*
 * Register hooks that are fired when the plugin is activated or deactivated.
 * When the plugin is deleted, the uninstall.php file is loaded.
 *
 */
register_activation_hook( __FILE__, array( 'IssuePress', 'activate' ) );
register_deactivation_hook( __FILE__, array( 'IssuePress', 'deactivate' ) );

/*
 * Plugins Loaded Action
 *
 */
add_action( 'plugins_loaded', array( 'IssuePress', 'get_instance' ) );

/*----------------------------------------------------------------------------*
 * Dashboard and Administrative Functionality
 *----------------------------------------------------------------------------*/

/*
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

}
