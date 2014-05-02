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

  private $test_mode = false;

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
    $vars[] = 'ip_org';
    $vars[] = 'ip_repo';
    $vars[] = 'ip_issue';
    $vars[] = 'ip_name';
    $vars[] = 'ip_search';
    $vars[] = 'ip_is_new';
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
    add_rewrite_rule('^' . IP_API_PATH .'search/?','index.php?__ip_api=1&ip_search=1','top');
    add_rewrite_rule('^' . IP_API_PATH .'([^/]*)/([^/]*)/([^/]*)/?','index.php?__ip_api=1&ip_org=$matches[1]&ip_repo=$matches[2]&ip_issue=$matches[3]','top');
    add_rewrite_rule('^' . IP_API_PATH .'([^/]*)/([^/]*)/?','index.php?__ip_api=1&ip_org=$matches[1]&ip_repo=$matches[2]','top');
    add_rewrite_rule('^' . IP_API_PATH .'?','index.php?__ip_api=1','top');

    flush_rewrite_rules();

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

    if(isset($wp->query_vars['ip_search'])){
      $is_search = true;
    }

    // Get the vars in variables if they exist
    if(isset($wp->query_vars['ip_org'])) {
      $org = json_encode($wp->query_vars['ip_org']);
    }

    if(isset($wp->query_vars['ip_repo'])) {
      $repo = json_encode($wp->query_vars['ip_repo']);
    }

    if(isset($wp->query_vars['ip_issue'])) {
      $issue = json_encode($wp->query_vars['ip_issue']);
    }

    if($method === 'POST') {
      $post_data = json_decode(file_get_contents('php://input'), true);
    }

    $msg = '200 OK';
    $data = array();

    if($method === "PUT" && isset($org) && isset($repo) && isset($issue)){
      $data['response'] = $this->put_issue(json_decode($org), json_decode($repo), json_decode($issue));

    } else if($method === "GET" && isset($org) && isset($repo) && isset($issue)){
      //      $data['repo'] = $this->get_repo(json_decode($repo)); // Necessary?
      $data['issue'] = $this->get_issue(json_decode($org), json_decode($repo), json_decode($issue));
      $data['comments'] = $this->get_issue_comments(json_decode($org), json_decode($repo), json_decode($issue));

    } else if($method === "GET" && isset($org) && isset($repo) && !isset($issue)){
      $data['repo'] = $this->get_repo(json_decode($org), json_decode($repo));
      $data['issues'] = $this->get_issues(json_decode($org), json_decode($repo));
      $data['activity'] = $this->get_repo_activity(json_decode($org), json_decode($repo));
//      $data['releases'] = $this->get_repo_releases(json_decode($org), json_decode($repo));  // Github undocumented releases API, currently taken down?

    } else if($method === "POST" && isset($is_search)){
      $data['response'] = $this->search($post_data);
    } else if($method === "POST" && isset($org) && isset($repo) && !isset($issue)){
      $data['response'] = $this->post_issue(json_decode($org), json_decode($repo), $post_data);
    } else if($method === "POST" && isset($org) && isset($repo) && isset($issue)){
      $data['reponse'] = $this->post_comment(json_decode($org), json_decode($repo), json_decode($issue), $post_data);
    }


    if( $this->test_mode ) {
      $data['test'] = array();

      if(isset($org))
        $data['test']['org'] = $org;

      if(isset($repo))
        $data['test']['repo'] = $repo;

      if(isset($issue))
        $data['test']['issue'] = $issue;

      if(isset($is_search))
        $data['test']['is_search'] = $is_search;

      if(isset($method))
        $data['test']['method'] = $method;

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
  private function get_repo($org, $repoName){
    $cacheKey = $org . '/' . $repoName . '-repo';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->show($org, $repoName));
    }

    return $cache;
  }

  /**
   * github API call to get all issues data from repo
   *
   * @return array of objects
   */
  private function get_issues($org, $repoName){
    $cacheKey = $org . '/' . $repoName . '-issues';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('issue')->all($org, $repoName, array('state' => 'open')));
    }

    return $cache;
  }

  /**
   * github API call to get releases of a repo
   *
   * * @return array of objects
   */
  private function get_repo_releases($org, $repoName){
    $cacheKey = $org . '/' . $repoName . '-releases';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->releases()->all($org, $repoName));
    }

    return $cache;
  }

  /**
   * github API call to get recent activity of a repo
   *
   * @return array of objects
   */
  private function get_repo_activity($org, $repoName){
    $cacheKey = $org . '/' . $repoName . '-activity';

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      $cache = $this->ip_cache_set($cacheKey, $client->api('repo')->events($org, $repoName));
    }

    return $cache;
  }

  /**
   * github API call to post new issue
   *
   * @return string (response)
   */
  private function post_issue($org, $repoName, $issue_data){

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
      $response = $client->api('issue')->create($org, $repoName, $issue_data);

      return $response;

    }
  }

  /**
   * github API call to put issue
   *
   * @return string
   */
  private function put_issue($org, $repoName, $issue){
    // Github API call to update a particular issue ($issue) in particular repo ($repoName)
    // $client->api('issue')->update($this->user['login'], 'php-github-api', 4, array('body' => 'The new issue body'));
  }

  /**
   * github API call to get issue
   *
   * @return string
   */
  private function get_issue($org, $repoName, $issue){
    $cacheKey = $org . '/' . $repoName . '-issue-' . $issue;

    $cache = $this->ip_cache_get($cacheKey);
    if($cache === FALSE) {
      $client = $this->get_client();
      
      $issue =  $this->filter_body($client->api('issue')->show($org, $repoName, $issue));
      $cache = $this->ip_cache_set($cacheKey, $issue);
    }

    return $cache;
  }

  /**
   * github API call to get an issue's comments
   *
   * @return string
   */
  private function get_issue_comments($org, $repoName, $issue){
    $cacheKey = $org . '/' . $repoName . '-comments';

    $cache = $this->ip_cache_get($cacheKey);

    if($cache === FALSE) { // No Comments cache for this repo, init
      $cache = array();
    }

    $client = $this->get_client();
    $cache[$issue] = $client->api('issue')->comments()->all($org, $repoName, $issue);
    foreach($cache[$issue] as $i => $comment) {
      $cache[$issue][$i] = $this->filter_body($comment);
    }

    $cache = $this->ip_cache_set($cacheKey, $cache);

    return $cache[$issue];
  }

  /**
   * github API call to post a new comment on issue in repo
   *
   * @return string (response)
   */
  private function post_comment($org, $repoName, $issue, $comment_data){

    $error = null;

    if( !isset($comment_data['body'] ) )
      $error = "Missing the comment body.";

    if(isset($error)) {
      return array('error' => true, 'message' => $error);
    } else {

      $comment_data = $this->process_issue($comment_data);

      $client = $this->get_client();
      // Github API call to post a new issue in particular repo ($repoName)
      $response = $client->api('issue')->comments()->create($org, $repoName, $issue, $comment_data);

      return $response;

    }
  }


  /**
   * github API call to run a search 
   *
   * @return string (response)
   */
  private function search($data){
    
    if($data['repo'] == 'all'){
      $repos = $this->get_repos();
    } else {
      $repos = array($data['repo']);
    }

    $q = $data['q'];
    $params = array();
    $params['repos'] = $repos;


    $client = $this->get_client();
    $response = $client->api('issue')->find($q, $params);


//    For Testing

//    $r = array();
//    $r['q'] = $data['q'];
//    $r['repo'] = $data['repo'];
//    $r['repos'] = $repos;
//    $r['req-data'] = $data;
//    $r['q'] = $q;
//    $r['params'] = $params;


    return $response;
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

    $issue["body"] = $this->add_issue_meta_to_body($issue['meta'], $issue['body']);

    unset($issue['meta']);

    $issue["labels"] = "issuepress";

    return $issue;

  }

  /**
   * Get Repos
   *
   * Returns array of the IP enabled repos
   *
   * @return repos Array
   */
  private function get_repos(){

    $options =  get_option('issuepress_options');

    if(!array_key_exists('upip_gh_repos', $options))
      return array('undefined');

    foreach($options['upip_gh_repos'] as $index => $item) {
      $repos[] = $item;
    }

    return $repos;
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

    $body .= "


***
IssuePress Data:

$meta_string

Sent via [IssuePress](http://issuepress.co)
";

    return $body;
  }

  /*
   * Filter Issue Body
   *
   * @param array issue Object from GitHub
   * @return 
   */
  private function filter_body($issue) {

    $element_regex = '/\*\*\*\sIssuePress Data:.*/ism';

    $issue['body'] = preg_replace($element_regex, "", $issue['body']);

    return $issue;

  }


  /*
   * Create IssuePress label for a repo
   *
   * @param string Repo
   * @param array Label
   * @return void
   */
  public function create_label($owner_repo_name, $label) {

    list($owner, $repo) = explode("/", $owner_repo_name);

    $client = $this->get_client();
    $labels = $client->api('issue')->labels()->all($owner, $repo);

    $has_label = false;

    foreach($labels as $l) {
      if($l['name'] == $label['name'])
        $has_label = true;
    }

    if($has_label == true)
      return;
    
    $client->api('issue')->labels()->create($owner, $repo, $label);

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

  /**
   * IP cache clear
   *
   * Utility function wrapping the delete_transient(),
   * first checks if IP cache is enabled, proceeds if so.
   *
   * @param $key STRING
   * @return TRUE if successful, FALSE otherwise 
   */
  private function ip_cache_clear($key='') {
    if(!$this->cacheIsOn || $key == '')
      return false;

    $clear = delete_transient('ip-'.$key); // Note the prepending 'ip-'
    return $clear;
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


  /**
   * IP Repo Cache Clear
   *
   * Utility function to clear all related transients for a repo
   *
   * @param $key STRING
   * @return void
   */
  public function ip_clear_repo_cache($repo=''){

    $cacheKeys = array(
      'repo',
      'issues',
      'activity',
      'comments',
//      'releases', Releases currently disabled
    );

    foreach($cacheKeys as $key){

      $this->ip_cache_clear($repo . '-' . $key);

    }

    return;

  }


}
