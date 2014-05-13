<?php

class UPIP_admin {

  private $client;

  public function __construct(){
    add_action('admin_init', array($this, 'init'));
    add_action('admin_menu', array($this, 'add_menu'));
    add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    add_action('wp_ajax_upip-create-page', array($this,'ajax_create_page' ) );

    add_filter('plugin_action_links', array($this, 'action_links'), 10, 2);
    add_filter('plugin_row_meta', array($this, 'meta_links'), 10, 2);
   
    $this->client = new Github\Client();

    //print_r($_POST);
  }

  public function init(){

    register_setting('issuepress_options', 'issuepress_options', array($this,'issuepress_options_validate'));

    add_settings_section('initial-setup-section',
                         'Initial Setup',
                         array($this,'initial_setup'),
                         'issuepress_options');

    add_settings_field('upip_gh_token',
                       'Github Token',
                       array($this,'github_token_field'),
                       'issuepress_options',
                       'initial-setup-section');

    add_settings_field('upip_support_page_id',
                       'Support Page',
                       array($this,'support_page_id_field'),
                       'issuepress_options',
                       'initial-setup-section');

    add_settings_field('upip_gh_repos',
                       'Github Repositories',
                       array($this,'github_repos_field'),
                       'issuepress_options',
                       'initial-setup-section');

    // retrieve our license key from the DB
    $license_key  = get_option( 'upip_license_key' );

    // setup the updater
    $ip_updater = new IP_Plugin_Updater( IP_STORE_URL, __FILE__, array(
        'version'   => issuepress_get_version(),     // current version number
        'license'   => $license_key,  // license key (used get_option above to retrieve from DB)
        'item_name' => IP_ITEM_NAME,  // name of this plugin
        'author'    => 'UpThemes'  // author of this plugin
      )
    );
  }


  /**
   * Add plugin action links
   */
  public function action_links($links, $file) {

    if($file == plugin_basename(IP_MAIN_PLUGIN_FILE)) {

      $ip_settings = get_admin_url('', '/admin.php?page=issuepress_options');
      array_unshift($links, "<a href='$ip_settings'>Settings</a>");

    }

    return $links;
  }

  /**
   * Add plugin meta links
   */
  public function meta_links($links, $file) {

    if($file == plugin_basename(IP_MAIN_PLUGIN_FILE)) {

      array_push($links, '<a target="_blank" href="http://issuepress.co/documentation">Documentation</a>');

    }

    return $links;
  }


  function admin_scripts($hook) {

      if( !isset($_GET['page']) || ('admin.php' != $hook && $_GET['page'] != 'issuepress_options' ) )
          return;

      wp_enqueue_style( 'ip-admin', plugins_url('/assets/css/admin.css', __FILE__) );
  }

  public function add_menu() {
    add_menu_page( __('IssuePress Options','IssuePress'), 'IssuePress', 'manage_options', 'issuepress_options', array($this, 'draw_options_panel'), plugins_url("/assets/img/issuepress-wordpress-icon-32x32.png", __FILE__ ), 140);
  }

  public function draw_options_panel() { ?>

  <div class="wrap">
    <h2><img src="<?php echo plugins_url("/assets/img/mark.svg", __FILE__ ); ?>" style="vertical-align:middle; top: -2px; position: relative;" width="32" height="32" alt=""> <?php _e("IssuePress Options Panel","issuepress"); ?></h2>
    <form action="options.php" method="post">
    <?php

    settings_fields( 'issuepress_options' );

    do_settings_sections( 'issuepress_options' );

    submit_button();

    ?>
    </form>
  </div>

<?php
  }

  function initial_setup() {
    echo '<script type="text/javascript" src="' . plugins_url('vendor/chosen/chosen.jquery.js', __FILE__ ) . '"></script>';
    echo '<style type="text/css"> @import url("' . plugins_url('vendor/chosen/chosen.min.css', __FILE__ ) . '"); </style>';
    ?>

    <p><?php _e( 'Enter your Github personal token and select your support page and repositories below. <a href="http://issuepress.co/documentation/" target="_blank" title="Read the documentation">Read the documentation</a>.','IssuePress'); ?></p>
    <script type="text/javascript">
      jQuery(document).ready(function($){
        $(".chosen").chosen();
        $("#create-new-support-page").on("click",function(e){
          e.preventDefault();

          $.post(
            // see tip #1 for how we declare global javascript variables
            ajaxurl,
            {
              // here we declare the parameters to send along with the request
              // this means the following action hooks will be fired:
              // wp_ajax_nopriv_myajax-submit and wp_ajax_myajax-submit
              action : 'upip-create-page',

              // other parameters can be added along with "action"
              support_page_nonce : '<?php echo wp_create_nonce( "create-support-page" ); ?>'
            },
            function( response ) {
              $('#upip_support_page')
                .append($('<option>', {
                    value: response.page_ID,
                    text: response.page_title
                }))
                .val(response.page_ID);

              $('<span>')
                .insertAfter( $('#upip_support_page') )
                .addClass('ip-alert')
                .text('<?php _e("Please save options below.","IssuePress"); ?>');

              $('#create-new-support-page')
                .remove();
            }
          );
          });
      });
    </script>
<?php
  }

  function github_token_field() {
    $issuepress_options = get_option('issuepress_options');
    $github_token = $issuepress_options['upip_gh_token'];
    echo '<input type="text" name="issuepress_options[upip_gh_token]" value="' . $github_token . '" />';
    if( !$github_token ){
      echo '<p>';
      echo sprintf( __('%1$sGenerate an access token%2$s and paste it here. We recommend setting up an IssuePress-specific Github account (with proper access to your repositories) for most installations.','issuepress') , '<a target="_blank" href="https://github.com/settings/tokens/new">', '</a>');
      echo '</p>';
    }
    settings_errors('upip_gh_token');
  }

  function support_page_id_field() {
    $issuepress_options = get_option('issuepress_options');
    $support_page_id = $issuepress_options['upip_support_page_id'];

    $output = '<select id="upip_support_page" name="issuepress_options[upip_support_page_id]">';
    $output .= '<option value="">' . __('Select Page','IssuePress') . '</option>';

    $pages = get_pages(array(
      'sort_order' => 'ASC',
      'post_status' => 'publish'
    ));

    foreach($pages as $page) {
      $selected = '';
      if($page->ID == $support_page_id){
        $selected = 'selected';
      }
      $output .= '<option value="'.$page->ID.'" '.$selected.'>'.$page->post_title.'</option>';
    }
    $output .= '</select> ';

    if( !$support_page_id ){
      $output .= '<button class="button secondary" id="create-new-support-page">' . __('+ Create New','IssuePress') . '</button>';
    }

    echo $output;

    settings_errors('upip_gh_repos');

  }

  function github_repos_field(){
    $issuepress_options = get_option('issuepress_options');

    /**
     * Check if valid Github token in place
     */
    if(!empty($issuepress_options['upip_gh_token'])) {
      $github_token = $issuepress_options['upip_gh_token'];
    } else {
      echo '<p>';
      _e("No Github token entered. Please enter a valid personal token above to select repositories.","IssuePress");
      echo '</p>';
      return;
    }

    /**
     * Fetch current ip repo data or init blank array
     */
    if(isset($issuepress_options['upip_gh_repos'])) {
      $selected_repos = $issuepress_options['upip_gh_repos'];
    } else {
      $selected_repos = array();
    }

    try {
      $client = $this->client;
      $client->authenticate($github_token, null, Github\Client::AUTH_HTTP_TOKEN);
      $gh_repos = $client->api('current_user')->repositories(array('per_page' => 100));
      $gh_orgs = $client->api('current_user')->orgs(array('per_page' => 100));



      $all_repos = array();

      $t_repos = array();

      foreach($gh_repos as $index => $repo) {
        if(!array_key_exists($repo['owner']['login'], $all_repos))
          $all_repos[$repo['owner']['login']] = array();

        $all_repos[$repo['owner']['login']][] = $repo['name'];
      }

      foreach($gh_orgs as $org) {
        $_o_repos = $client->api('organization')->repositories($org['login'], array('per_page' => 100));
        foreach($_o_repos as $index => $repo) {
          if(!array_key_exists($repo['owner']['login'], $all_repos))
            $all_repos[$repo['owner']['login']] = array();

          $all_repos[$repo['owner']['login']][] = $repo['name'];
        }
      }
         
      echo '<select data-placeholder="' . __('Select One or More Repositories ...','IssuePress') . '" name="issuepress_options[upip_gh_repos][]" class="chosen chosen-multiple" multiple>';

      foreach($all_repos as $owner => $repos) {
        echo "\n\n";
        echo '<optgroup label="' . $owner . '">';
        echo "\n";

        foreach($repos as $repo) {
          echo '<option value="' . $owner . '/' . $repo . '"' . selected(in_array($owner . '/' . $repo,$selected_repos),1, false). '>'. $owner . '/' .$repo.'</option>';
          echo "\n";
        }

        echo '</optgroup>';
        echo "\n\n";
      }


      echo '</select></td>';

    } catch( Exception $e ){
      echo "Cannot read repositories: " . $e->getMessage();
    }

  }

  function issuepress_options_validate($input) {

    if(isset($input['upip_gh_token']))
      $github_token = $input['upip_gh_token'];

    if(isset($input['upip_gh_repos']))
      $github_repos = $input['upip_gh_repos'];

    // Ensure page ID is an integer
    $input['upip_support_page_id'] = intval($input['upip_support_page_id']);

    // Ensure our Github token actually works
    try{
      $client = $this->client;
      $client->authenticate($github_token, null, Github\Client::AUTH_HTTP_TOKEN);
      $gh_repos = $client->api('current_user')->repositories(array('per_page' => 100));
      $github_token = 'valid';
    } catch( Exception $e ){
      $message = sprintf( __("Github Token Error: %s","IssuePress"), $e->getMessage() );
      add_settings_error( 'upip_gh_token', 'token-error', $message, 'error' );
      $github_token = 'invalid';
    }

    // Ensure the user has selected one or more repositories
    if( empty($github_repos) && $github_token == 'valid' ){
      $message = __("Please select one or more repositories to complete IssuePress setup.","IssuePress");
      add_settings_error( 'upip_gh_repos', 'no-repository-selected', $message, 'error' );
    }

    return $input;
  }

  function ajax_create_page() {
    header( "Content-Type: application/json" );

    $nonce = $_POST['support_page_nonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'create-support-page' ) ){
      $response = json_encode( array( 'message' => 'Invalid nonce', 'nonce' => $nonce ) );
      echo $response;
      exit;
    }

    // ignore the request if the current user doesn't have
    // sufficient permissions
    if ( current_user_can( 'edit_posts' ) ) {

      $page_title = 'Support';

      // create support page
      $support_page_args = array(
        'post_name'      => 'support',
        'post_status'    => 'publish',
        'post_title'     => $page_title,
        'post_type'      => 'page'
      );

      $page_ID = wp_insert_post($support_page_args);

      // generate the response
      $response = json_encode( array( 'success' => true, 'page_ID' => $page_ID, 'page_title' => $page_title ) );

      // response output
      echo $response;
    }
    exit;
  }

}

$issuepress_admin = new UPIP_admin();
