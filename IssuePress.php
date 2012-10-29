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
    add_action('parse_request', array($this, 'sniff_requests'), 0);
    add_action('init', array($this, 'add_endpoint'), 0);
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
