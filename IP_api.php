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

  private $client;
  private $user;

  /* Cache vars */
  private $cacheIsOn = TRUE;
  private $cacheExpire = 480; // Expires every 8 mins: (60*8)

  /** Hook WordPress
  * @return void
  */
  public function __construct(){
    $this->client = $this->new_client();
    $this->user = $this->get_user();

    add_filter('query_vars', array($this, 'add_query_vars'), 0);
    add_action('parse_request', array($this, 'check_requests'), 0);
    add_action('init', array($this, 'add_endpoint'), 0);
  }

  public function new_client() {
    $options = get_option('upip_options');    
    $client = new Github\Client();
    $client->authenticate($options['u'], $options['p'], Github\Client::AUTH_HTTP_PASSWORD);
    return $client;
  }

  public function get_Client() {
    return $this->client;
  }

  private function get_user() {
    $options = get_option('upip_options');    
    if($options['u'])
      return $options['u'];
    else
      return '';
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
    add_rewrite_rule('^' . IP_API_PATH .'([^/]*)/([^/]*)/?','index.php?__ip_api=1&repo=$matches[1]&issue=$matches[2]','top');
    // IP & Repo
    add_rewrite_rule('^' . IP_API_PATH .'([^/]*)/?','index.php?__ip_api=1&repo=$matches[1]','top');
    // IP
    add_rewrite_rule('^' . IP_API_PATH .'?','index.php?__ip_api=1','top');

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
    *
    * List specific issue for repo
    * GET /repos/:owner/:repo/issues/:number
    *
    * get_issue_num()
    * /[landing]/[repo name]/[issue number]
    * GET - {repo} 
    * GET - {issue}
    *
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
      $repo = json_encode($wp->query_vars['repo']);
    if(isset($wp->query_vars['issue'])) 
      $issue = json_encode($wp->query_vars['issue']);

    if($method === 'POST')
      $post_data = file_get_contents('php://input');


    if(!empty($post_data))
      $data['post'] = json_decode($post_data, true);



    /*
     * Route the request:
     *
     * if PUT & Repo & Issue -> put_issue()
     * if GET & Repo & Issue -> get_issue()
     * if GET & Repo -> get_repo() & get_issues()
     * if POST & Repo -> post_issue()
     *
     */
    if($method === "PUT" && isset($repo) && isset($issue)){
      $data['response'] = $this->put_issue(json_decode($issue), json_decode($repo));
    } else if($method === "GET" && isset($repo) && isset($issue)){
//      $data['repo'] = $this->get_repo(json_decode($repo)); // Necessary?
      $data['issue'] = $this->get_issue(json_decode($issue), json_decode($repo));
      $data['comments'] = $this->get_issue_comments(json_decode($issue), json_decode($repo));
    } else if($method === "GET" && isset($repo) && !isset($issue)){
      $data['repo'] = $this->get_repo(json_decode($repo));
      $data['issues'] = $this->get_issues(json_decode($repo));
//      $data['releases'] = $this->get_repo_releases(json_decode($repo));
      $data['activity'] = $this->get_repo_activity(json_decode($repo));
    } else if($method === "POST" && isset($repo) && !isset($issue)){
      $data['response'] = $this->post_issue(json_decode($repo));
    } else if($method === "POST" && isset($repo) && isset($issue)){
      $data['reponse'] = $this->post_comment(json_decode($issue), json_decode($repo));
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


  /*** UPAPI refs to Github API ***/


  /** github API call to get repo
  * @return object
  */
  private function get_repo($repoName){
    $cacheKey = $repoName . '-repo';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->show($this->user, $repoName)); 
    }

    return $cache;
  }

  /** github API call to get all issues data from repo
  * @return array of objects
  */
  private function get_issues($repoName){
    $cacheKey = $repoName . '-issues';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->all($this->user, $repoName, array('state' => 'open'))); 
    }

    return $cache;
  }

  /** github API call to get releases of a repo
   * * @return array of objects
   */
  private function get_repo_releases($repoName){
    $cacheKey = $repoName . '-releases';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->releases()->all($this->user, $repoName)); 
    }

    return $cache;
  }

  /** github API call to get recent activity of a repo
   * * @return array of objects
   */
  private function get_repo_activity($repoName){
    $cacheKey = $repoName . '-activity';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->events()->all($this->user, $repoName)); 
    }

    return $cache;
  }

  /** github API call to post new issue
  * @return string (response)
  */
  private function post_issue($repoName){
    // Github API call to post a new issue in particular repo ($repoName)
    // $client->api('issue')->create($this->user, 'php-github-api', array('title' => 'The issue title', 'body' => 'The issue body');
  }

  /** github API call to put issue
  * @return string
  */
  private function put_issue($issue, $repoName){
    // Github API call to update a particular issue ($issue) in particular repo ($repoName)
    // $client->api('issue')->update($this->user, 'php-github-api', 4, array('body' => 'The new issue body'));
  }

  /** github API call to get issue
  * @return string
  */
  private function get_issue($issue, $repoName){
    $cacheKey = $repoName . '-issue-' . $issue;

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->show($this->user, $repoName, $issue));
    }

    return $cache;
  }

  /** github API call to get an issue's comments
   * @return string
   */
  private function get_issue_comments($issue, $repoName){
    $cacheKey = $repoName . '-comments-' . $issue;

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->comments()->all($this->user, $repoName, $issue)); 
    }

    return $cache;
  }

  /** github API call to post a new comment on issue in repo
   * @return string (response)
   */
  private function post_comment($issue, $repoName){
    // $client->api('issue')->comments()->create($this->user, 'php-github-api', 4, array('body' => 'My new comment'));
  }

  /*** END UPAPI refs to Github API ***/

  /*** IP Cache Functions ***/

  /** IP cache get
   *
   * Utility function wrapping the get_transient(),
   * first checks if IP cache is enabled, proceeds if so.
   *
   * @param $key STRING
   * @return cache OBJ or FALSE if cache empty or disabled
   */
  private function ip_cache_get($key='') {
    if(!$this->cacheIsOn || $key == '')
      return false;

    $cache = get_transient('ip-'.$key); // Note the prepending 'ip-'
    return $cache;
  }

  /** IP cache set
   *
   * Utility function wrapping the set_transient(),
   * first checks if IP cache is enabled, proceeds if so.
   *
   * @param $key STRING
   * @param $data OBJECT
   * @return $data OBJECT
   */
  private function ip_cache_set($key, $data){
    if(!$this->cacheIsOn || $key == '')
      return false;

    set_transient('ip-'.$key, $data, $this->cacheExpire); // Note the prepending 'ip-'
    return $data;
  }

  /*** END IP Cache Functions ***/

}
new UPIP_api();
