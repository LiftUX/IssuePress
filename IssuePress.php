<?php
/*
Plugin Name: IssuePress
Plugin URI: http://issuepress.co/
Description: Create a public support page for your private Github repositories, brought to you by UpThemes.
Version: 1.0.11
Author: UpThemes
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
  private $widget_locs = array(
    array(
      'IssuePress Dashboard Main',
      'ip-dashboard-main',
      'Control the content in the main column of the "Dashboard" page of IssuePress.',
    ),
    array(
      'IssuePress Dashboard Sidebar',
      'ip-dashboard-side',
      'Control the content in the sidebar column of the "Dashboard" page of IssuePress.',
    ),
    array(
      'IssuePress Sections Sidebar',
      'ip-sections-side',
      'Control the content in the sidebar of the "Sections" page of IssuePress.',
    ),
    array(
      'IssuePress Section Main',
      'ip-section-main',
      'Control the content in the main column of the "Section" page of IssuePress.',
    ),
    array(
      'IssuePress Section Sidebar',
      'ip-section-side',
      'Control the content in the sidebar column of the "Section" page of IssuePress.',
    ),
    array(
      'IssuePress Issue Sidebar',
      'ip-issue-side',
      'Control the content in the sidebar column of the "Issue Thread" page of IssuePress.',
    ),
  );

  private $ip_api;


  /**
  * Hook WordPress
  *
  * @return void
  */
  public function __construct(){

    $this->ip_api = new UPIP_api();

    add_action('template_include', array($this, 'load_IP_template'), 0);

    add_action('init', array($this, 'register_IP_scripts'), 0);
    add_action('ip_head', array($this, 'print_IP_scripts'), 20);
    add_action('update_option_issuepress_options', array($this, 'create_IP_labels'), 5, 2);
    add_action('update_option_issuepress_options', array($this, 'clear_IP_cache'), 5, 2);
    add_action('admin_print_styles', array($this, 'resize_icon'), 20);
    add_action('widgets_init', array($this, 'register_IP_sidebars'), 0);
    add_action('admin_init', array($this, 'theme_updater'),0);
    add_action('admin_notices', array($this, 'permalink_notice'),0);

    add_action('ip_head', array($this, 'print_custom_settings'), 30);

    register_activation_hook( __FILE__, array( $this, 'init_IP_sidebar_widgets' ) );  
    register_deactivation_hook( __FILE__, array( $this, 'flush_rewrites' ) );  

  }

  public function flush_rewrites(){

    flush_rewrite_rules();

  }


  public function get_version(){
    $plugin_data = get_plugin_data( IP_MAIN_PLUGIN_FILE );
    $plugin_version = $plugin_data['Version'];
    return $plugin_version;
  }

  /**
  * Overwrite the default template with IssuePress Angular App
  *
  * @return void
  */
  public function load_IP_template( $original_template ){

    // Check if we've got work to do.
    if( !get_query_var("pagename") && !get_query_var("page_id") )
      return $original_template;

    $IP_dir = dirname(IP_MAIN_PLUGIN_FILE);
    $IP_options = get_option('issuepress_options');
    $IP_landing_id = $IP_options['upip_support_page_id'];
    $IP_landing_name = sanitize_title(get_the_title($IP_landing_id));

    // Check if the page being served matches the name or ID of the one set in options
    if(get_query_var("pagename") == $IP_landing_name || get_query_var("page_id") == $IP_landing_id){

      $ip_tpl = $IP_dir . '/IP_template.php';
      if(file_exists($ip_tpl)){ // Does the template exist? 
        $this->print_scripts = true;
        return $ip_tpl; 
      } else { // Fallback to original template if something is terribly wrong
        return $original_template;
      }

    } else {
      return $original_template;
    }

  }


  /**
  * Register the IP sidebars
  *
  * @return void
  */
  public function register_IP_sidebars() {

    $widget_locs = $this->widget_locs;

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
  * Initialize the IP sidebars with widgets after activiation 
  *
  * @return void
  */
  public function init_IP_sidebar_widgets() {

    $sidebars_widgets = get_option( 'sidebars_widgets' );

    $widget_locs = $this->widget_locs;

    foreach($widget_locs as $widget_loc) {

      if(!empty($sidebars_widgets[$widget_loc[1]])){

        // Do nothing, this sidebar already has stuff in it

      } else if($widget_loc[1] == 'ip-dashboard-main') {

        $sidebars_widgets[$widget_loc[1]] = array(
          'ip_message-1',
        );
        $this->init_widget('ip_message', array(
          'title' => 'Welcome to IssuePress',
          'msg' => 'We\'ve added some introductory content, customize this in the wp-admin on the widget\'s page. It\'s the "IP Message Box" widget in the "IssuePress Dashboard Main" WordPress sidebar.'
        ));

      } else if($widget_loc[1] == 'ip-dashboard-side') {

        $sidebars_widgets[$widget_loc[1]] = array(
          'ip_sections-1',
        );
        $this->init_widget('ip_sections', array('title' => 'Sections'));

      } else if ($widget_loc[1] == 'ip-section-main') {

        $sidebars_widgets[$widget_loc[1]] = array(
          'ip_recent_activity-1',
        );
        $this->init_widget('ip_recent_activity', array('title' => 'Recent Activity'));

      }

    }

    update_option('sidebars_widgets', $sidebars_widgets);

  }


  /*
   * Init Widget Util Function
   *
   * Initializes the content of a widget at specified index with array of settings
   *
   * @param $widget STRING - Widget id
   * @param $settings STRING - Sidebar
   * @param $i INT - Index to update, defaults to 1
   * @return void
   */
  public function init_widget($widget, $settings, $i = 1) {

    $widget_data = get_option('widget_' . $widget);
    $widget_data[$i] = $settings;
    update_option('widget_' . $widget, $widget_data);

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
   * Add Customized data
   *
   * @return void
   */
  public function print_custom_settings() { 

    $opts = get_option('issuepress_options');

    if(isset($opts['upip_custom_header']) && $opts['upip_custom_header'] != 'http://'){ ?>
<script type="text/javascript">var IP_Custom_Header = <?php echo json_encode($opts['upip_custom_header']); ?></script>
<?php
    }

    if(isset($opts['upip_custom_color']) && $opts['upip_custom_color'] != '#936091'){ 
      $color = $opts['upip_custom_color']; 
      $l_color = lighten_color($color, 25);
?>
<style type="text/css">a, .breadcrumb a, .issue-thread .comment .author-name, .issue-thread .comment .comment-tags a, .issue-list .issue-list-item .issue-title { color: <?php echo $color; ?>; }a:hover, .breadcrumb a:hover, .issue-thread .comment .author-name:hover, .issue-thread .comment .comment-tags a:hover, .issue-list .issue-list-item .issue-title:hover { color: <?php echo $l_color; ?>; }.submit,.search .live-search-results .issue-list-item .issue-link, .support-sections .support-section .support-section-following, .issue-list .issue-list-item .issue-link{ background-color: <?php echo $color; ?>;}.submit:hover,.search .live-search-results .issue-list-item .issue-link:hover, .support-sections .support-section .support-section-following:hover, .issue-list .issue-list-item .issue-link:hover { background-color: <?php echo $l_color; ?> ;}</style>
<?php
    }
  }

  
  /**
  * Create Labels for selected IP repos
  *
  * @return void
  */
  public function create_IP_labels($old, $new) {

    if(empty($new['upip_gh_repos'])){ 
      return;
    }

    foreach($new['upip_gh_repos'] as $r){ 
      $this->ip_api->create_label($r, array( "name" => "issuepress", "color" => "936091"));
    }

    return;

  }


  /**
  * Clear IP cache  
  *
  * @return void
  */
  public function clear_IP_cache($old, $new) {

    if(empty($old['upip_gh_repos'])){ 
      return;
    }

    foreach($old['upip_gh_repos'] as $r){ 
      $this->ip_api->ip_clear_repo_cache($r);
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

    // Split full name
    foreach($options['upip_gh_repos'] as $index => $item) {
      list($owner, $repo) = explode("/", $item);
      $IP_repos[] = array(
        'name' => $repo,
        'owner' => $owner
      );

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
   * Fetch page data from WP for 'uip_support_page_id'
   *
   * @return WP Post object
   */
  public function get_IP_page_data(){
    $options =  get_option('issuepress_options');
    return get_post($options['upip_support_page_id']);
  }

  /**
  * Fetches the page data for the set IssuePress page and returns it in json for template vars
  *
  * @return json encoded string
  */
  public function get_IP_root(){
    return json_encode($this->get_IP_page_data());
  }

  /** 
   * Fetches the WP site data for use in the template
   *
   * @return json encoded string
   */
  public function get_site_data(){
    $name = get_bloginfo('name');
    $name = !empty($name) ? $name : 'Home Page';
    return json_encode(array(
      'name' => $name,
      'description' => get_bloginfo('description'),
      'url' => get_bloginfo('url'),
      'wpurl' => get_bloginfo('wpurl')
    ));
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
    $post = $this->get_IP_page_data();
    return wp_login_url(site_url( '/'. $post->post_name .'/'));
  }

  /**
  * Utility function to pass nonce to angular app
  *
  * @return string
  */
  public function get_IP_logout(){
    $post = $this->get_IP_page_data();
    $url = wp_logout_url(site_url( '/'. $post->post_name .'/'));
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

  public function permalink_notice() { 
    if ( get_option('permalink_structure') ) {
      return;
    }

  ?>
    <div class="error">
      <p><?php _e( 'IssuePress requires pretty permalinks to be set.', 'IssuePress' ); ?> <a href="<?php echo admin_url('options-permalink.php'); ?>" title="Update Permalinks Now">Update Permalinks Now</a></p>
    </div> 
  <?php
  }


}

$UP_IP = new UP_IssuePress();
