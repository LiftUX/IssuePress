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
    add_action('wp_footer', array($this, 'print_IP_scripts'), 20);

  } 

  

  /* Overwrite the default template with IssuePress Backbone App
   * @return void
   */
  public function load_IP_template(){
    global $wp;
    $IP_dir = dirname(__FILE__);
    $IP_options = get_option('upip_options');
    $IP_landing_id = $IP_options['landing'];
    $IP_landing_name = sanitize_title(get_the_title($IP_landing_id));

    // Check if the page being served matches the name or ID of the one set in options
    if($wp->query_vars["pagename"] == $IP_landing_name || $wp->query_vars["page_id"] == $IP_landing_id){
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
    // Google jQuery, backbone, it's deps
    wp_deregister_script('jquery');
//    wp_register_script('jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js');
    wp_register_script('jquery', plugins_url('assets/js/jquery.min.js', __FILE__), array(), '1.8.2', true);
    wp_register_script('underscore', plugins_url('assets/js/underscore.js', __FILE__), array(), '1.4.2', true);
    wp_register_script('backbone', plugins_url('assets/js/backbone.js', __FILE__), array('underscore', 'jquery', 'json2'), '0.9.2', true);

    // The IP Backbone app deps
    wp_register_script('ip_m_repo', plugins_url('src/m/repo.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_m_issue', plugins_url('src/m/issue.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_repos', plugins_url('src/c/repos.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_c_issues', plugins_url('src/c/issues.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_v_app', plugins_url('src/v/issuepress.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_v_repo', plugins_url('src/v/repo.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_v_issue', plugins_url('src/v/issue.js', __FILE__), array(), '0.0.1', true);
    wp_register_script('ip_router', plugins_url('src/r/router.js', __FILE__), array(), '0.0.1', true);

    // The IP Backbone app file
    wp_register_script(
      'issuepress', 
      plugins_url('src/issuepress.js', __FILE__), 
      array(
        'backbone',
        'ip_m_repo',
        'ip_m_issue',
        'ip_c_repos',
        'ip_c_issues',
        'ip_v_app',
        'ip_v_repo',
        'ip_v_issue',
        'ip_router'
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
    return sanitize_title(get_the_title($IP_options['landing']));
  }

}
new UP_IssuePress();
