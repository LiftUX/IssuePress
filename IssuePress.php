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

if(!defined('IP_STORE_URL'))
  define( 'IP_STORE_URL', 'http://issuepress.co' );

if(!defined('IP_ITEM_NAME'))
  define( 'IP_ITEM_NAME', 'IssuePress' );

define('IP_MAIN_PLUGIN_FILE', __FILE__ );

include_once 'vendor/autoload.php';
include_once 'IP_api.php';
include_once 'IP_helpers.php';
include_once 'widgets/load.php';
include_once 'IP_admin.php';
include_once 'IP_license_admin.php';


if( !class_exists( 'IP_Plugin_Updater' ) ) {
  // load our custom updater
  include( dirname( IP_MAIN_PLUGIN_FILE ) . '/IP_Plugin_Updater.php' );
}

class UP_IssuePress {

  /**
  * Print Scripts?
  *
  * @var $print_scripts
  */
  private $print_scripts = false;
  private $widget_locs = null;

  private $ip_api;


  /**
  * Hook WordPress
  *
  * @return void
  */
  public function __construct(){

    $this->ip_api = new UPIP_api();

    add_action('template_redirect', array($this, 'load_IP_template'), 0);

    add_action('init', array($this, 'register_IP_scripts'), 0);
    add_action('ip_head', array($this, 'print_IP_scripts'), 20);

    add_action('update_option_issuepress_options', array($this, 'create_IP_labels'), 5, 2);

    add_action('admin_print_styles', array($this, 'resize_icon'), 20);

    add_action('widgets_init', array($this, 'register_IP_sidebars'), 0);

    add_action('admin_init', array($this, 'theme_updater'),0);

  }

  public function testing_updates($option, $new, $old, $another){
    echo "TESTING\n";
    var_dump($option);
    echo "\n";
    var_dump($new);
    echo "\n";
    var_dump($old);
    echo "\n";
    var_dump($another);

  }

  public function get_version(){
    $plugin_data = get_plugin_data( IP_MAIN_PLUGIN_FILE );
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
  }

  /**
  * Overwrite the default template with IssuePress Backbone App
  *
  * @return void
  */
  public function load_IP_template(){

    // Check if we've got work to do.
    if( !get_query_var("pagename") && !get_query_var("page_id") )
      return false;

    $IP_dir = dirname(IP_MAIN_PLUGIN_FILE);
    $IP_options = get_option('issuepress_options');
    $IP_landing_id = $IP_options['upip_support_page_id'];
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
  *
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
  *
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
        'IssuePress Section Main',
        'ip-section-main',
        'Control the content in the IssuePress "Section" page.',
      ),
      array(
        'IssuePress Section Sidebar',
        'ip-section-side',
        'Control the content in the IssuePress "Section" page\'s sidebar',
      ),
      array(
        'IssuePress Issue Sidebar',
        'ip-issue-side',
        'Control the content in the IssuePress "Issue Thread" page\'s sidebar',
      ),
    );

    $this->widget_locs = $widget_locs;

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
   * Get IP Sidebars
   *
   * Fetch the IP sidebars, which renders angular templates with configured IP widgets.
   *
   * @return STRING
   */
  public function get_IP_sidebars(){
    $ip_sidebars = $this->widget_locs;

    $html = '';

    foreach($ip_sidebars as $sidebar){

      $sb = get_dynamic_sidebar($sidebar[1]);
      $html .= '

<script type="text/ng-template" id="'.$sidebar[1].'">
'. $sb .'
</script>

';

    }

    return $html;

  }


  /**
  * Register scripts for IP
  *
  * @return void
  */
  public function register_IP_scripts(){

    // IP Styles
    wp_register_style(
      'issuepress-css',
      plugins_url('assets/css/main.css', IP_MAIN_PLUGIN_FILE),
      array(),
      '0.0.1',
      'all');

    // Google's Angular
    wp_register_script('ip_angular', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.12/angular.min.js');
    wp_register_script('ip_angular_route', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.12/angular-route.min.js', array('ip_angular'));

    // The IP built file 
    wp_register_script(
      'issuepress',
      plugins_url('build/main.js', IP_MAIN_PLUGIN_FILE),
      array('ip_angular_route'),
      '0.0.1',
      true
    );
  }


  /**
  * Print out our scripts
  *
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
  * Create Labels for selected IP repos
  *
  * @return void
  */
  public function create_IP_labels($old, $new) {

    foreach($new['upip_gh_repos'] as $r){ 
      $this->ip_api->create_label($r, array( "name" => "issuepress", "color" => "936091"));
    }

    return;

  }


  /**
  * Fetch the github repos IP tracks to initialize
  *
  * @return [json] $IP_repos;
  */
  public function get_IP_repo_json(){

    $options =  get_option('issuepress_options');

    if(!array_key_exists('upip_gh_repos', $options))
      return 'undefined';

    foreach($options['upip_gh_repos'] as $index => $item) {
      $IP_repos[]['name'] = $item;
    }

    return json_encode($IP_repos);
  }


  /**
  * Fetch the IP data currently in the cache
  *
  * @return [json] $IP_data;
  */
  public function get_IP_data(){

    $IP_data = new ArrayObject(); 
    $options =  get_option('issuepress_options');

    if(!array_key_exists('upip_gh_repos', $options))
      return 'undefined';

    foreach($options['upip_gh_repos'] as $index => $item) {

      $repoCache = $this->ip_api->ip_get_repo_cache($item);
      
      $IP_data["$item"] = array(
        'name' => $item,
        'repo' => $repoCache['repo'],
        'issues' => $repoCache['issues'],
        'activity' => $repoCache['activity'],
        'comments' => $repoCache['comments'],
        // 'releases' => $repoCache['releases'],
      );

    }


    return json_encode($IP_data);
  }


  /**
  * Fetches the slug for the support page
  *
  * @return string
  */
  public function get_IP_root(){
    $options =  get_option('issuepress_options');
    return sanitize_title(get_the_title($options['upip_support_page_id']));
  }


  /**
  * Utility function to output URL path of IP angular app for easy partials reference
  *
  * @return string
  */
  public function get_IP_path(){
    return plugins_url('src', IP_MAIN_PLUGIN_FILE);
  }

  /**
  * Utility function to pass the IP API base endpoint to angular app
  *
  * @return string
  */
  public function get_IP_API_path(){
    return IP_API_URL;
  }

  /**
  * Utility function to output current user data safely
  *
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
  *
  * @return string
  */
  public function get_IP_login(){
    return wp_login_url(site_url( '/'.$this->get_IP_root().'/'));
  }

  /**
  * Utility function to pass nonce to angular app
  *
  * @return string
  */
  public function get_IP_logout(){
    $url = wp_logout_url(site_url( '/'.$this->get_IP_root().'/'));
    return str_replace('&amp;', '&', $url);
  }

  /**
   * Utility function to pass authenticated github username to angular app
   *
   * @return string
   */
  public function get_IP_auth_user(){
    $user = get_transient('ip-user');
    if($user)
      return json_encode($user['login']);
    else
      return 'undefined';
  }

  /**
  * Resizes the IssuePress menu icon (retina icon hack)
  *
  * @return void
  */
  public function resize_icon(){
    echo '<style type="text/css">#toplevel_page_issuepress_options img{ width: 16px; height: 16px; margin-top: 0; }</style>';
    return;
  }

  public function theme_updater(){

    global $pagenow;

    $license_status = get_option('upip_license_status');

    if( $license_status == 'valid' ){
      // retrieve our license key from the DB
      $license_key = trim( get_option( 'upip_license_key' ) );

      $version_number = $this->get_version();

      // setup the updater
      $edd_updater = new IP_Plugin_Updater( IP_STORE_URL, IP_MAIN_PLUGIN_FILE, array(
          'version'   => $version_number, // current version number
          'license'   => $license_key,      // license key (used get_option above to retrieve from DB)
          'item_name' => IP_ITEM_NAME,  // name of this plugin
          'author'  => 'Lift'       // author of this plugin
        )
      );
    } else {

      if( $pagenow == 'admin.php' && ( $_GET['page'] == 'issuepress_options' || $_GET['page'] == 'issuepress_license' ) )
        add_action( 'admin_notices', array( $this, 'license_invalid') );

    }

  }

  public function license_invalid() { ?>
        <?php if( $_GET['page'] == 'issuepress_license' ): ?>
        <div class="updated">
          <p>Your IssuePress license has not been activated. Please enter/activate your license key or <a href="http://issuepress.co">renew your subscription</a> to enable automatic updates and support.</p>
        </div>
        <?php else: ?>
        <div class="error">
          <p>Your IssuePress license has not been activated. Please <a href="<?php echo admin_url('admin.php?page=issuepress_license'); ?>">enter/activate your license key</a> or <a href="http://issuepress.co">renew your subscription</a> to enable automatic updates and support.</p>
        </div>
        <?php endif; ?>
  <?php
  }
}
new UP_IssuePress();
