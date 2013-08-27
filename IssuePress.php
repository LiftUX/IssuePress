<?php
/*
Plugin Name: IssuePress
Plugin URI: http://upthemes.com/plugins/issuepress
Description: Github Issues integration with WP - for support stuff
Version: 0.0.1
Author: Upthemes
Author URI: http://upthemes.com/
*/

if(!defined('IP_API_PATH'))
  define('IP_API_PATH', 'issuepress/api/');

$ip_api_url = home_url(IP_API_PATH);
if(!defined('IP_API_URL'))
  define('IP_API_URL', $ip_api_url);

require_once 'vendor/autoload.php';
require_once 'IP_admin.php';
require_once 'IP_api.php';
require_once 'IP_helpers.php';
require_once 'IP_license_admin.php';
require_once 'widgets/load.php';

if( !class_exists( 'IP_Plugin_Updater' ) ) {
  // load our custom updater if it doesn't already exist
  include( dirname( __FILE__ ) . '/IP_Plugin_Updater.php' );
}

define( 'IP_STORE_URL', 'http://issuepress.co' );
define( 'IP_ITEM_NAME', 'IssuePress' );

class UP_IssuePress {

  /**
  * Print Scripts?
  * @var $print_scripts
  */
  private $print_scripts = false;


  /**
  * Hook WordPress
  * @return void
  */
  public function __construct(){
    add_action('template_redirect', array($this, 'load_IP_template'), 0);

    add_action('init', array($this, 'register_IP_scripts'), 0);
    add_action('ip_head', array($this, 'print_IP_scripts'), 20);

    add_action('admin_print_styles', array($this, 'resize_icon'), 20);
    add_action('admin_init', array($this, 'set_plugin_version', 0));

    add_action('widgets_init', array($this, 'register_IP_sidebars'), 0);
  }


  /**
  * Overwrite the default template with IssuePress Backbone App
  * @return void
  */
  public function load_IP_template(){

    // Check if we've got work to do.
    if( !get_query_var("pagename") && !get_query_var("page_id") )
      return false;

    $IP_dir = dirname(__FILE__);
    $IP_options = get_option('upip_options');
    $IP_landing_id = $IP_options['landing'];
    $IP_landing_name = sanitize_title(get_the_title($IP_landing_id));

    // Check if the page being served matches the name or ID of the one set in options
    if(get_query_var("pagename") == $IP_landing_name || get_query_var("page_id") == $IP_landing_id){
      if(file_exists($IP_dir . '/IP_template.php')){
        $return_template = $IP_dir . '/IP_template.php';
        $this->print_scripts = true;
        $this->do_theme_direct($return_template);
      }
    }

  }


  /**
  * Actually load our template instead of the requested page
  * @return void
  */
  private function do_theme_direct($url){
    global $post, $wp_query;
    if(have_posts()){
      include($url);
      die();
    } else {
      $wp_query->is_404 = true;
    }
  }


  /**
  * Register the IP sidebars
  * @return void
  */
  public function register_IP_sidebars() {

    // Build out the various sidebar detail in an array for easy registering...
    $widget_locs = array(
      array(
        'IssuePress Dashboard Main',
        'ip-dashboard-main',
        'Control the content in the IssuePress "Dashboard" page.',
      ),
      array(
        'IssuePress Dashboard Sidebar',
        'ip-dashboard-side',
        'Control the content in the IssuePress "Dashboard" page\'s sidebar',
      ),
      array(
        'IssuePress Sections Sidebar',
        'ip-sections-side',
        'Control the content in the IssuePress "Sections" page\'s sidebar',
      ),
      array(
        'IssuePress Repo Sidebar',
        'ip-repo-side',
        'Control the content in the IssuePress "Repo" page\'s sidebar',
      ),
      array(
        'IssuePress Issue Sidebar',
        'ip-issue-side',
        'Control the content in the IssuePress "Issue Thread" page\'s sidebar',
      ),
    );

    // Loop through our sidebar details and register them
    foreach($widget_locs as $widget_loc){
      register_sidebar(array(
        'name'        => __($widget_loc[0], 'IssuePress'),
        'id'          => $widget_loc[1],
        'description' => __($widget_loc[2], 'IssuePress'),
      ));
    }
  }


  /**
  * Register scripts for IP
  * @return void
  */
  public function register_IP_scripts(){

    // IP Styles
    wp_register_style(
      'issuepress-css',
      plugins_url('assets/css/main.css', __FILE__),
      array(),
      '0.0.1',
      'all');

    // Google's Angular
    wp_register_script('ip_angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.1.5/angular.min.js');

    // The IP Angular app modules
    wp_register_script('ip_app_state', plugins_url('src/app/app-state/app-state.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_header', plugins_url('src/app/header/header.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_dashboard', plugins_url('src/app/dashboard/dashboard.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_sections', plugins_url('src/app/sections/sections.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_repo', plugins_url('src/app/repo/repo.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_issue', plugins_url('src/app/issue/issue.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_create_issue', plugins_url('src/app/create-issue/create-issue.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_user', plugins_url('src/app/user/user.js', __FILE__), array(), '0.0.1', true);

    // The IP Angular app components
    wp_register_script('ip_c_message', plugins_url('src/app/components/message.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_recent_activity', plugins_url('src/app/components/recent-activity/recent-activity.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_ticket_list', plugins_url('src/app/components/ticket-list/ticket-list.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_issue_thread', plugins_url('src/app/components/issue-thread/issue-thread.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_breadcrumbs', plugins_url('src/app/components/breadcrumbs/breadcrumbs.js', __FILE__), array('ip_u_breadcrumbs'), '0.0.1', true);

    // Util Angular modules
    wp_register_script('ip_u_md5', plugins_url('src/util/md5/md5.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_u_gravatar', plugins_url('src/util/gravatar/gravatar.js', __FILE__), array('ip_u_md5'), '0.0.1', true);
    wp_register_script('ip_u_breadcrumbs', plugins_url('src/util/breadcrumbs.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_u_timeago', plugins_url('src/util/timeago.js', __FILE__), array(), '0.0.1', true);

    // The IP Angular app bootstrap file
    wp_register_script(
      'issuepress',
      plugins_url('src/app/issuepress.js', __FILE__),
      array(
        'ip_angular',

        'ip_app_state',
        'ip_header',
        'ip_dashboard',
        'ip_sections',
        'ip_repo',
        'ip_issue',
        'ip_create_issue',
        'ip_user',

        'ip_c_message',
        'ip_c_breadcrumbs',
        'ip_c_recent_activity',
        'ip_c_ticket_list',
        'ip_c_issue_thread',

        'ip_u_gravatar',
        'ip_u_timeago',
      ),
      '0.0.1',
      true);
  }


  /**
  * Print out our scripts
  * @return void
  */
  public function print_IP_scripts(){
    if($this->print_scripts == false)
      return;

    // Print IP styles
    wp_print_styles('issuepress-css');
    // We only need this call since we've set up deps properly
    wp_print_scripts('issuepress');
  }


  /**
  * Fetch the github repos IP tracks to initialize BB
  * @return [json] $IP_repos;
  */
  public function get_IP_repo_json(){

    $options =  get_option('upip_options');

    if(!array_key_exists('r', $options))
      return 'undefined';

    foreach($options['r'] as $index => $item) {
      $IP_repos[]['name'] = $item;
    }

    return json_encode($IP_repos);
  }


  /**
  * Fetches the slug for the support page
  * @return string
  */
  public function get_IP_root(){
    $options =  get_option('upip_options');
    return sanitize_title(get_the_title($options['landing']));
  }


  /**
  * Utility function to output URL path of IP angular app for easy partials reference
  * @return string
  */
  public function get_IP_path(){
    return plugins_url('src', __FILE__);
  }

  /**
  * Utility function to pass the IP API base endpoint to angular app
  * @return string
  */
  public function get_IP_API_path(){
    return IP_API_URL;
  }

  /**
  * Utility function to output current user data safely
  * @return json_encoded objec
  */
  public function get_IP_user() {
    $user = wp_get_current_user();

    if( !($user instanceof WP_User) || $user->data == null )
      return 'null';

    $IP_user = array(
      'username' => $user->user_login,
      'email' => $user->user_email,
      'firstname' => $user->user_firstname,
      'lastname' => $user->user_lastname,
      'display_name' => $user->display_name,
      'ID' => $user->ID,
    );

    return json_encode($IP_user);
  }

  /**
  * Utility function to pass login page to angular app
  * @return string
  */
  public function get_IP_login(){
    return wp_login_url(site_url( '/'.$this->get_IP_root().'/'));
  }

  /**
  * Utility function to pass nonce to angular app
  * @return string
  */
  public function get_IP_logout(){
    $url = wp_logout_url(site_url( '/'.$this->get_IP_root().'/'));
    return str_replace('&amp;', '&', $url);
  }

  /**
  * Resizes the IssuePress menu icon (retina icon hack)
  */
  public function resize_icon(){
    echo '<style type="text/css">#toplevel_page_issuepress-options img{ width: 16px; height: 16px; margin-top: -2px; }</style>';
  }
}
new UP_IssuePress();
