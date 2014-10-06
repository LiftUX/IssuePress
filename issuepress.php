<?php

/**
 * The IssuePress bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * Dashboard. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              http://issuepress.co
 * @since             1.0.0
 * @package           IssuePress
 *
 * @wordpress-plugin
 * Plugin Name:       IssuePress
 * Plugin URI:        http://issuepress.co/
 * Description:       Professional Support right in WordPress.
 * Version:           1.0.0
 * Author:            UpThemes
 * Author URI:        http://upthemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       issuepress
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'ISSUEPRESS_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'ISSUEPRESS_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );

/**
 * The code that runs during plugin activation.
 * It's action is documented in includes/class-issuepress-activator.php 
 */
require_once ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress-activator.php';
register_activation_hook( __FILE__, array( 'IssuePress_Activator', 'activate' ) );

/**
 * The code that runs during plugin deactivation.
 * It's action is documented in includes/class-issuepress-deactivator.php
 */
require_once  ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress-deactivator.php';
register_deactivation_hook( __FILE__, array( 'IssuePress_Deactivator', 'deactivate' ) );

/**
 * The core plugin class that is used to define internationalization,
 * dashboard-specific hooks, and public-facing site hooks.
 */
require_once ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress.php';

/**
 * The extension parent class, used for easy extension creation.
 */
include_once( ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress-extension.php' );

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_issuepress() {

	$plugin = new IssuePress();
	$plugin->run();
	return $plugin;

}
$IssuePress = run_issuepress();
