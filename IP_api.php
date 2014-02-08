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

  /**
   * Hook WordPress
   *
   * @return void
   */
  public function __construct(){
    $options = get_option('issuepress_options');
    if( isset( $options['upip_gh_token'] ) && $options['upip_gh_token'] ){
      $client = $this->new_client( $options['upip_gh_token'] );
      $this->client = $client;

      $user = $client->api('current_user')->show();
      $this->set_user($user);
      $this->user = $user;

      add_filter('query_vars', array($this, 'add_query_vars'), 0);
      add_action('parse_request', array($this, 'check_requests'), 0);
      add_action('init', array($this, 'add_endpoint'), 0);
    }
  }

  public function new_client($oauth_key) {
    $client = new Github\Client();
    $client->authenticate($oauth_key, null, Github\Client::AUTH_HTTP_TOKEN);
    return $client;
  }

  public function get_client() {
    return $this->client;
  }

  private function get_user() {

    $user = get_transient('ip-user');

    if( $user )
      return $user;


    $client = $this->get_client();
    if( $client ){
      $user = $client->api('current_user')->show();
    }

    if( $user ) {
      $this->set_user($user);
      return $user;
    } else
      return '';
  }

  private function set_user($user) {
    set_transient('ip-user', $user);
    return;
  }

  /**
   * Add public query vars
   *
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

  /**
   * Add API Endpoint
   *
   * This is where the magic happens - brush up on your regex skillz
   * @return void
   */
  public function add_endpoint(){

    // Add API endpoints 
    add_rewrite_rule('^' . IP_API_PATH .'([^/]*)/([^/]*)/?','index.php?__ip_api=1&repo=$matches[1]&issue=$matches[2]','top');
    add_rewrite_rule('^' . IP_API_PATH .'([^/]*)/?','index.php?__ip_api=1&repo=$matches[1]','top');
    add_rewrite_rule('^' . IP_API_PATH .'?','index.php?__ip_api=1','top');

  }

  /**
   * Sniff Requests
   *
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

  /**
   * Handle Requests
   *
   * This is where we handle the request according to API
   * @return void
   */
  protected function handle_request(){

    /**
     * API Implementation:
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

    if($method === 'POST') {
      $post_data = json_decode(file_get_contents('php://input'), true);
    }

    $msg = '200 OK';
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
      $data['activity'] = $this->get_repo_activity(json_decode($repo));
//      $data['releases'] = $this->get_repo_releases(json_decode($repo));  // Github undocumented releases API, currently taken down?
    } else if($method === "POST" && isset($repo) && !isset($issue)){
      $data['response'] = $this->post_issue(json_decode($repo), $post_data);
    } else if($method === "POST" && isset($repo) && isset($issue)){
      $data['reponse'] = $this->post_comment(json_decode($issue), json_decode($repo));
    }

    $this->send_response($msg, $data);

  }

  /**
   * Response Handler
   *
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


  /*** IPAPI refs to Github API ***/


  /**
   * github API call to get repo
   *
   * @return object
   */
  private function get_repo($repoName){
    $cacheKey = $repoName . '-repo';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->show($this->user['login'], $repoName));
    }

    return $cache;
  }

  /**
   * github API call to get all issues data from repo
   *
   * @return array of objects
   */
  private function get_issues($repoName){
    $cacheKey = $repoName . '-issues';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->all($this->user['login'], $repoName, array('state' => 'open')));
    }

    return $cache;
  }

  /**
   * github API call to get releases of a repo
   *
   * * @return array of objects
   */
  private function get_repo_releases($repoName){
    $cacheKey = $repoName . '-releases';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->releases()->all($this->user['login'], $repoName));
    }

    return $cache;
  }

  /**
   * github API call to get recent activity of a repo
   *
   * @return array of objects
   */
  private function get_repo_activity($repoName){
    $cacheKey = $repoName . '-activity';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->events($this->user['login'], $repoName));
    }

    return $cache;
  }

  /**
   * github API call to post new issue
   *
   * @return string (response)
   */
  private function post_issue($repoName, $issue_data){

    $error = null;

    if( !isset($issue_data['title']) )
      $error = "Missing the issue title.";
  
    if( !isset($issue_data['body'] ) )
      $error = "Missing the issue body.";

    if(isset($error)) {
      return array('error' => true, 'message' => $error);
    } else {

      $issue_data = $this->process_issue($issue_data);

      $client = $this->get_client();
      // Github API call to post a new issue in particular repo ($repoName)
      $response = $client->api('issue')->create($this->user['login'], $repoName, $issue_data);
//      $client->api('issue')->labels()->add($this->user['login'], $repoName, $response['number'], array('issuepress'));

      return $response;

    }
  }

  /**
   * github API call to put issue
   *
   * @return string
   */
  private function put_issue($issue, $repoName){
    // Github API call to update a particular issue ($issue) in particular repo ($repoName)
    // $client->api('issue')->update($this->user['login'], 'php-github-api', 4, array('body' => 'The new issue body'));
  }

  /**
   * github API call to get issue
   *
   * @return string
   */
  private function get_issue($issue, $repoName){
    $cacheKey = $repoName . '-issue-' . $issue;

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->show($this->user['login'], $repoName, $issue));
    }

    return $cache;
  }

  /**
   * github API call to get an issue's comments
   *
   * @return string
   */
  private function get_issue_comments($issue, $repoName){
    $cacheKey = $repoName . '-comments';

    $cache = $this->ip_cache_get($cacheKey);

    if($cache === FALSE) { // No Comments cache for this repo, init
      $client = $this->get_client();
      $data = array(); 
      $data[$issue] = $client->api('issue')->comments()->all($this->user['login'], $repoName, $issue);
      
      $cache = $this->ip_cache_set($cacheKey, $data);
    } else if( !isset($cache[$issue]) ) { // Comments cache, but not for this issue
      $client = $this->get_client();
      $cache[$issue] = $client->api('issue')->comments()->all($this->user['login'], $repoName, $issue);

      $cache = $this->ip_cache_set($cacheKey, $cache);
    }


    return $cache[$issue];
  }

  /**
   * github API call to post a new comment on issue in repo
   *
   * @return string (response)
   */
  private function post_comment($issue, $repoName){
    // $client->api('issue')->comments()->create($this->user['login'], 'php-github-api', 4, array('body' => 'My new comment'));
  }

  /*** END IPAPI refs to Github API ***/

  /*** IPAPI Util Functions ***/

  /**
   * Process Issue 
   *
   * Does some things to the issue so that it's clear it is an IssuePress issue in github
   * (Add Tags, body header/footer info)
   *
   * @var issue Array
   * @return issue Array
   */
  private function process_issue($issue) {

    $issue['body'] = $this->add_issue_meta_to_body($issue['meta'], $issue['body']);

    unset($issue['meta']);

//    $issue['labels'] = array('issuepress');

    return $issue;

  }


  /**
   * Add Isse Meta to Body 
   *
   * Adds WP user/time meta to github issue body
   *
   * @var body string
   * @return body string
   */
  private function add_issue_meta_to_body($meta, $body) {

    $meta_string = '';
    foreach($meta as $key => $value) {
      $meta_string .= "$key: $value\n";
    }

    $body = "* * *
Hello! IssuePress has processed an issue. Begin transmission...
* * *

$body


* * *
User Meta: 
$meta_string
* * *";

    return $body;
  }


  /*
   * Create IssuePress label for a repo
   *
   * @param string Repo
   * @param array Label
   * @return void
   */
  public function create_label($repo, $label) {

    $client = $this->get_client();
    $labels = $client->api('issue')->labels()->all($this->user['login'], $repo);

    $has_label = false;

    foreach($labels as $l) {
      if($l['name'] == $label['name'])
        $has_label = true;
    }

    if($has_label == true)
      return;
    
    $client->api('issue')->labels()->create($this->user['login'], $repo, $label);

  }

  /*** END IPAPI Util Functions ***/


  /*** IP Cache Functions ***/

  /**
   * IP cache get
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

  /**
   * IP cache set
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


  /**
   * IP Repo Cache Get
   *
   * Utility function to get all related transients for a repo
   *
   * @param $key STRING
   */
  public function ip_get_repo_cache($repo=''){

    $cacheKeys = array(
      'repo',
      'issues',
      'activity',
      'comments',
//      'releases', Releases currently disabled
    );

    $repoCache = array();

    foreach($cacheKeys as $key){

      $tmp = $this->ip_cache_get($repo . '-' . $key);
      
      // If there isn't a cache, create empty containters for proper json encoding
      if($tmp == false) {

        if($key == 'repo') {
          $tmp = new ArrayObject(); // new ArrayObject looks like empty JS Object Literal
        } else {
          $tmp = array(); // array() looks like empty array
        }
        
      }

      $repoCache[$key] = $tmp;
    }

    return $repoCache;

  }

}
