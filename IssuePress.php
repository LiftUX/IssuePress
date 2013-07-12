<?php
/*
Plugin Name: IssuePress
Plugin URI: http://upthemes.com/plugins/issuepress
Description: Github Issues integration with WP - for support stuff
Version: 0.0.1
Author: Upthemes
Author URI: http://upthemes.com/ 
*/

require_once 'vendor/autoload.php';
require_once 'IP_admin.php';
require_once 'IP_api.php';

class UP_IssuePress {
  
  /** Print Scripts?
   *  @var
   */
  private $print_scripts = false;

  /** Hook WordPress
  * @return void
  */
  public function __construct(){
    add_action('template_redirect', array($this, 'load_IP_template'), 0);

    add_action('init', array($this, 'register_IP_scripts'), 0);
    add_action('ip_head', array($this, 'print_IP_scripts'), 20);

  } 

  

  /* Overwrite the default template with IssuePress Backbone App
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

  /* Actually load our template instead of the requested page
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

  /* Register scripts for IP
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
    wp_register_script('ip_header', plugins_url('src/app/header/header.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_dashboard', plugins_url('src/app/dashboard/dashboard.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_sections', plugins_url('src/app/sections/sections.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_repo', plugins_url('src/app/repo/repo.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_issue', plugins_url('src/app/issue/issue.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_create_issue', plugins_url('src/app/create-issue/create-issue.js', __FILE__), array(), '0.0.1', true);

    // The IP Angular app components
    wp_register_script('ip_c_message', plugins_url('src/app/components/message.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_breadcrumbs', plugins_url('src/app/components/breadcrumbs.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_recent_activity', plugins_url('src/app/components/recent-activity/recent-activity.js', __FILE__), array(), '0.0.1', true);

    // The IP Angular app bootstrap file
    wp_register_script(
      'issuepress', 
      plugins_url('src/app/issuepress.js', __FILE__), 
      array(
        'ip_angular',

        'ip_header',
        'ip_dashboard',
        'ip_sections',
        'ip_repo',
        'ip_issue',
        'ip_create_issue',

        'ip_c_message',
        'ip_c_breadcrumbs',
        'ip_c_recent_activity',
      ),
      '0.0.1', 
      true);
  }

  /* Print out our scripts
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

  /* Fetch the github repos IP tracks to initialize BB
   * @return [json] $IP_repos;
   */
  public function get_IP_repo_json(){

    $options =  get_option('upip_options');    
    foreach($options['r'] as $index => $item) {
      $IP_repos[]['name'] = $item;  
    }

    return json_encode($IP_repos);
  }
  
  /* Fetches the slug for the support page
   * @return string
   */
  public function get_IP_root(){
    $options =  get_option('upip_options');    
    return sanitize_title(get_the_title($options['landing']));
  }

  /* Utility function to output URL path of IP angular app for easy partials reference
   * @return string
   */
  public function get_IP_path(){
    return plugins_url('src', __FILE__);
  }
}
new UP_IssuePress();
