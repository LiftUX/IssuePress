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
    $vars[] = 'is_new';
    return $vars;
  }
  
  /** Add API Endpoint
  * This is where the magic happens - brush up on your regex skillz
  * @return void
  */
  public function add_endpoint(){

    $IP_options = get_option('upip_options');
    $IP_landing_name = sanitize_title(get_the_title($IP_options['landing']));

    // Add API endpoints

    // IP, Repo & Issue
    add_rewrite_rule('^'.$IP_landing_name.'/issuepress/api/([\w-]+?)/([\w-]+?)','index.php?__ip_api=1&repo=$matches[1]&issue=$matches[2]','top');
    // IP & Repo
    add_rewrite_rule('^'.$IP_landing_name.'/issuepress/api/([\w-]+?)','index.php?__ip_api=1&repo=$matches[1]','top');
    // IP
    add_rewrite_rule('^'.$IP_landing_name.'/issuepress/api/?','index.php?__ip_api=1','top');

    // Add app rewrites for valid urls
//    add_rewrite_rule('^'.$IP_landing_name.'/([^/]*)/([^/]*)/new(/)?', 'index.php?pagename='.$IP_landing_name.'&repo=$matches[1]&issue=$matches[2]$is_new=true','top');
//    add_rewrite_rule('^'.$IP_landing_name.'/([^/]*)/([^/]*)(/)?', 'index.php?pagename='.$IP_landing_name.'&repo=$matches[1]&issue=$matches[2]','top');
//    add_rewrite_rule('^'.$IP_landing_name.'/([^/]*)(/)?', 'index.php?pagename='.$IP_landing_name.'&repo=$matches[1]','top');
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
    * GET - {repo}
    *
    *
    * List specific issue for repo
    * GET /repos/:owner/:repo/issues/:number
    *
    * get_issue_num()
    * /[landing]/[repo name]/[issue name/number]
    * GET - {repo} & {issue}
    *
    *
    * Create an Issue
    * POST /repos/:owner/:repo/issues
    *
    * post_issue()
    * /[landing]/[repo name]/new
    * POST - {repo}
    * 
    *
    * Edit an Issue
    * PATCH /repos/:owner/:repo/issues/:number
    *
    * put_issue()
    * /[landing]/[repo name]/[issue name/number]
    * PUT = {repo} & {issue}
    *
    */


    global $wp;

    // GET, POST, PUT, or DELETE
    $method = $_SERVER['REQUEST_METHOD'];
   

    // Get the vars in variables if they exist
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

    if($method === 'POST')
      $post_data = file_get_contents('php://input');


    if(!empty($post_data))
      $data['post'] = json_decode($post_data, true);



/*

if PUT & Repo $ Issue -> put_issue()
if GET & Repo & Issue -> get_issue()
if GET & Repo -> get_repo() & get_issues()
if POST & Repo -> post_issue()

*/
    // Route the request
    if($method === "PUT" && $repoName && $issue){
      $data['response'] = $this->put_issue($issue, $repoName);
    } else if($method === "GET" && $repoName && $issue){
      $data['issue'] = $this->get_issue($issue, $repoName);
    } else if($method === "GET" && $repoName && !$issue){
      $data['repo'] = $this->get_repo($repoName);
      $data['issues'] = $this->get_issues($repoName);
    } else if($method === "POST" && $repoName && !$issue){
      $data['response'] = $this->post_issue($repoName);
    }



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

  /** github API call to put issue
  * @return string
  */
  private function put_issue($issue, $repoName){
    // Github API call to update a particular issue ($issue) in particular repo ($repoName)
    // $client->api('issue')->update('KnpLabs', 'php-github-api', 4, array('body' => 'The new issue body'));
  }

  /** github API call to get issue
  * @return string
  */
  private function get_issue($issue, $repoName){
    // Github API call to get a particular issue ($issue) in particular repo ($repoName)
    // $issue = $client->api('issue')->show('KnpLabs', 'php-github-api', 1);
  }

  /** github API call to get repo
  * @return string
  */
  private function get_repo($repoName){
    // Github API call to get data for a particular repo ($repoName)
    // $repo = $client->api('repo')->show('KnpLabs', 'php-github-api')
  }

  /** github API call to get all issue data from repo
  * @return json encoded array of objects
  */
  private function get_issues($repoName){
    // Github API call to get issues in particular repo ($repoName)
    // $issues = $client->api('issue')->all('KnpLabs', 'php-github-api', array('state' => 'open'));
  }

  /** github API call to post new issue
  * @return string (response)
  */
  private function post_issue($repoName){
    // Github API call to post a new issue in particular repo ($repoName)
    // $client->api('issue')->create('KnpLabs', 'php-github-api', array('title' => 'The issue title', 'body' => 'The issue body');
  }

}
new UPIP_api();
