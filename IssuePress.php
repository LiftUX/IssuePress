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

class UPIP_API_Endpoint{
  
  /**
  * @var string Pug Bomb Headquarters
  */
  protected $api = 'http://api.github.com/repos/LiftUX/';
  
  
  /** Hook WordPress
  * @return void
  */
  public function __construct(){
    add_filter('query_vars', array($this, 'add_query_vars'), 0);
    add_action('init', array($this, 'add_endpoint'), 0);
    add_action('parse_request', array($this, 'sniff_requests'), 0);

    add_action('init', array($this, 'create_cpt_repo'), 0);
    add_action('init', array($this, 'create_cpt_issue'), 0);
    add_action('init', array($this, 'create_repo_tax'), 0);

  } 
  
  /** Add public query vars
  * @param array $vars List of current public query vars
  * @return array $vars 
  */
  public function add_query_vars($vars){
    $vars[] = '__api';
    $vars[] = 'repo';
    $vars[] = 'issue';
    return $vars;
  }
  
  /** Add API Endpoint
  * This is where the magic happens - brush up on your regex skillz
  * @return void
  */
  public function add_endpoint(){
    add_rewrite_rule('^api/repo/?([0-9]+)?/?','index.php?__api=1&repo=$matches[1]','top');
  }

  /** Sniff Requests
  * This is where we hijack all API requests
  *   If $_GET['__api'] is set, we kill WP and serve up pug bomb awesomeness
  * @return die if API request
  */
  public function sniff_requests(){
    global $wp;
    if(isset($wp->query_vars['__api'])){
      $this->handle_request();
      exit;
    }
  }
  
  /** Creates The WP Custom Post Type for Repos
  * @return void
  */
  public function create_cpt_repo(){
    register_post_type( 'repos',
      array(
        'labels' => array(
          'name' => 'Repos',
          'singular_name' => 'Repo',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New Repo',
          'edit' => 'Edit',
          'edit_item' => 'Edit Repo',
          'new_item' => 'New Repo',
          'search_items' => 'Search Repos',
          'not_found' => 'No Repos found',
          'not_found_in_trash' => 'No Repos found in Trash',
          'parent' => 'Parent Repo'
        ),
        'public' => true,
        'show_ui' => true,
        'menu_position' => 15,
        'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'taxonomies' => array( '' ),
        'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
        'has_archive' => false
      )
    );
  }


  /** Creates The WP Custom Post Type for Issues
  * @return void
  */
  public function create_cpt_issue(){
    register_post_type( 'issues',
      array(
        'labels' => array(
          'name' => 'Issues',
          'singular_name' => 'Issue',
          'add_new' => 'Add New',
          'add_new_item' => 'Add New Issue',
          'edit' => 'Edit',
          'edit_item' => 'Edit Issue',
          'new_item' => 'New Issue',
          'search_items' => 'Search Issue',
          'not_found' => 'No Issues found',
          'not_found_in_trash' => 'No Issues found in Trash',
          'parent' => 'Parent Issue'
        ),
        'public' => true,
        'show_ui' => true,
        'menu_position' => 15,
        'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
        'taxonomies' => array( 'repo' ),
        'menu_icon' => plugins_url( 'images/image.png', __FILE__ ),
        'has_archive' => false
      )
    );
  }

  /** Register Repo Taxonomy
   * This taxonomy will be used on issues and will associate them to their respective repo
   */
  public function create_repo_tax(){
    register_taxonomy('repo', 'issues', array(
      'labels' => array(
        'name' => _x('Repos', 'taxonomy general name', 'IssuePress'),
        'singular_nam' => _x('Repo', 'taxonomy singular name', 'IssuePress'),
        'search_items' => __('Search Repos', 'IssuePress'),
        'all_items' => __( 'All Repos' , 'IssuePress' ),
        'parent_item' => __( 'Parent Repo' , 'IssuePress' ),
        'parent_item_colon' => __( 'Parent Repo:' , 'IssuePress' ),
        'edit_item' => __( 'Edit Repo' , 'IssuePress' ),
        'update_item' => __( 'Update Repo' , 'IssuePress' ),
        'add_new_item' => __( 'Add New Repo' , 'IssuePress' ),
        'new_item_name' => __( 'New Repo Name' , 'IssuePress' ),
        'menu_name' => __( 'Repos' , 'IssuePress' ),
      ),
      'rewrite' => array(
        'slug' => 'repo',
        'with_front' => false,
        'hierarchical' => false,
      ),
      'hierarchical' => false,
      'public' => true,
      'show_in_menus' => true,
    ));
  }


  /** Handle Requests
  * This is where we send off for an intense pug bomb package
  * @return void 
  */
  protected function handle_request(){
    global $wp;
    $repo = $wp->query_vars['repo'];
    $issue = $wp->query_vars['issue'];


    if(!$repo)
      $this->send_response('Please tell us a repo.');
    
    if($repo)
      $this->send_response('200 OK', json_decode($repo.$issues));
    else
      $this->send_response('Failed response');
  }
  
  /** Response Handler
  * This sends a JSON response to the browser
  */
  protected function send_response($msg, $req = ''){
    $response['message'] = $msg;
    if($req)
      $response['req'] = $req;
    header('content-type: application/json; charset=utf-8');
      echo json_encode($response)."\n";
      exit;
  }
}
new UPIP_API_Endpoint();
