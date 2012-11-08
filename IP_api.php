<?php

/* 
 * In this file, we create the RESTful api that we use for the backbone app.
 * The api endpoint will reside at: 
 * {domain}/{slug of landing page}/issuepress/api
 * 
 * See the handle_request() method of api implementation.
 *
 *
 */

class UPIP_api{

  /** Hook WordPress
  * @return void
  */
  public function __construct(){
    add_filter('query_vars', array($this, 'add_query_vars'), 0);
    add_action('parse_request', array($this, 'check_requests'), 0);
    add_action('init', array($this, 'add_endpoint'), 0);
  }

  /** Add public query vars
  * @param array $vars List of current public query vars
  * @return array $vars 
  */
  public function add_query_vars($vars){
    $vars[] = '__ip_api';
    $vars[] = 'repo';
    $vars[] = 'issue';
    $vars[] = 'name';
    return $vars;
  }
  
  /** Add API Endpoint
  * This is where the magic happens - brush up on your regex skillz
  * @return void
  */
  public function add_endpoint(){

    $IP_options = get_option('upip_options');
    $IP_landing_name = sanitize_title(get_the_title($IP_options['landing']));

    // add api endpoint
    add_rewrite_rule('^'.$IP_landing_name.'/issuepress/api/([\w-]+?)/([\w-]+?)','index.php?__ip_api=1&repo=$matches[1]&issue=$matches[2]','top');
    add_rewrite_rule('^'.$IP_landing_name.'/issuepress/api/([\w-]+?)','index.php?__ip_api=1&repo=$matches[1]','top');
    add_rewrite_rule('^'.$IP_landing_name.'/issuepress/api/?','index.php?__ip_api=1','top');

//   add_rewrite_rule('^' . $IP_landing_name . '/(([\w-]+?)/?)+', '/support/' /*/'.$IP_landing_name.'/'.$matches[1]*/,'top');
  }

  /** Sniff Requests
  * This is where we hijack all API requests
  *   If $_GET['__ip_api'] is set, we kill WP and handle the request
  * @return die if API request
  */
  public function check_requests(){
    global $wp;
    // We found our api var, handle it
    if(isset($wp->query_vars['__ip_api'])){
      $this->handle_request();
      exit;
    }
  }
  
  /** Handle Requests
  * This is where we handle the request according to API
  * @return void 
  */
  protected function handle_request(){

    /** API Implementation:
    *
    * This is designed to be friendly with the github api for issues.
    *
    * List issues for a repo
    * GET /repos/:owner/:repo/issues
    *
    * get_issues()
    * /[landing]/[repo name]/issues
    *
    *
    * List specific issue for repo
    * GET /repos/:owner/:repo/issues/:number
    *
    * get_issue_num()
    * /[landing]/[repo name]/[issue name/number]
    *
    *
    * Create an Issue
    * POST /repos/:owner/:repo/issues
    *
    * post_issue()
    * /[landing]/[repo name]/new
    *
    *
    * Edit an Issue
    * PATCH /repos/:owner/:repo/issues/:number
    *
    * put_issue()
    * /[landing]/[repo name]/[issue name/number]
    *
    */


    global $wp;
    $options = get_option('upip_options');

    $method = $_SERVER['REQUEST_METHOD'];
   
    if(isset($wp->query_vars['repo'])) 
      $repoID = json_encode($wp->query_vars['repo']);
    if(isset($wp->query_vars['issue'])) 
      $issue = json_encode($wp->query_vars['issue']);

    // Repo id var has been set, let's figure out which repo they want & fetch it's data
    if(!empty($repoID)) {
      $repoName = $this->get_repoName_from_id($repoID);
      $data['repo'] = $this->get_repo_data($repoName);
    }

    // Issue id var has been set, let's ask github for that issue's data
    if(!empty($issue)) { 
      $data['issue'] = $this->get_issue_data($issue, $repoName);
    }

    $post_data = file_get_contents('php://input');
    if(!empty($post_data))
      $data['post'] = json_decode($post_data, true);
    $this->send_response('200 OK', $data);

  }
  
  /** Response Handler
  * This sends a JSON response to the browser
  */
  protected function send_response($msg, $data=''){
    $response['message'] = $msg;

    if($data)
      $response['data'] = $data;

    header('content-type: application/json; charset=utf-8');
    echo json_encode($response)."\n";
    exit;
  }

  /** Get Repo Name from ID
  * @return string
  */
  private function get_repoName_from_id($id){
    $options = get_option('upip_options');
    return $options['r'][json_decode($id)]; // Will fetch the repo name based on array index
  }

  /** Get Repo data from github
  * @return json (string)
  */
  private function get_repo_data($repoName){

    // Implement github api call for fetching repo data here
    return json_encode("repo data: " . $repoName);

  }

  /** Get Issue Data from github
  * @return json (string)
  */
  private function get_issue_data($issue, $repoName){

    // Implement github api call for fetching issue data here
    return json_encode("issue data: " . $issue . " from " . $repoName);

  }


}
new UPIP_api();
