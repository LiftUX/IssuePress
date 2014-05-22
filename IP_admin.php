<?php

/**
 * IssuePress Admin Class
 * Handles all WP Admin integration for IssuePress
 * (Settings page & plugins listing page links)
 */
class UPIP_admin {

  private $client;
  private $options_key = 'issuepress_options';
  private $general_settings_key = 'general';
  private $customize_settings_key = 'customize';

  public function __construct(){

    add_action('admin_menu', array($this, 'add_menu'));

    add_action('init', array($this, 'load_settings'));

    // Register Settings for each tab
    add_action('admin_init', array($this, 'register_general_settings'));
    add_action('admin_init', array($this, 'register_customization_settings'));

    add_action('admin_enqueue_scripts', array($this, 'admin_scripts'));
    add_action('wp_ajax_upip-create-page', array($this,'ajax_create_page' ) );

    // Add links to Plugin Listings Admin Page
    add_filter('plugin_action_links', array($this, 'add_action_links'), 10, 2);
    add_filter('plugin_row_meta', array($this, 'add_meta_links'), 10, 2);
   
    // Initialize a GitHub Client
    $this->client = new Github\Client();

    //print_r($_POST);
  }

  /**
   * Loads tabs settings from DB into their own arrays.
   */
  public function load_settings(){

    $this->general_settings = (array) get_option( $this->general_settings_key );
    $this->customize_settings = (array) get_option( $this->customize_settings_key );
    
//    No defaults for general settings
//    $this->general_settings = array_merge( array(
//    ), $this->general_settings );
    
    $this->customize_settings = array_merge( array(
      'upip_customize_color' => '#936091'
    ), $this->customize_settings );

  }

  /**
   * Registers the general settings section & fields
   */
  public function register_general_settings(){

    $this->settings_tabs[$this->general_settings_key] = 'General';

    $section_key = 'section-general';
    register_setting($this->general_settings_key, $this->general_settings_key, array($this,'general_settings_validate'));

    add_settings_section(
      $section_key,
      'General Settings',
      array($this,'general_initial_setup'),
      $this->general_settings_key
    );

    add_settings_field(
      'upip_gh_token',
      'Github Token',
      array($this,'general_gh_token'),
      $this->general_settings_key,
      $section_key
    );

    add_settings_field(
      'upip_support_page_id',
      'Support Page',
      array($this,'general_page_id'),
      $this->general_settings_key,
      $section_key
    );

    add_settings_field(
      'upip_gh_repos',
      'Github Repositories',
      array($this,'general_gh_repos'),
      $this->general_settings_key,
      $section_key
    );

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
   * Register the Customization section & fields
   */
  public function register_customization_settings(){
    $this->settings_tabs[$this->customize_settings_key] = 'Customize';

    $section_key = 'section-customize';
    register_setting($this->customize_settings_key, $this->customize_settings_key, array($this,'customize_settings_validate'));

    add_settings_section(
      $section_key,
      'Customization Settings',
      array($this,'customize_initial_setup'),
      $this->customize_settings_key
    );

    add_settings_field(
      'upip_customize_header',
      'Header Image',
      array($this,'customize_header'),
      $this->customize_settings_key,
      $section_key
    );

    add_settings_field(
      'upip_customize_color',
      'Main Color',
      array($this,'customize_color'),
      $this->customize_settings_key,
      $section_key
    );

  }

  /**
   * Test for our admin page to load styles
   */
  public function admin_scripts($hook) {

      if( !isset($_GET['page']) || ('admin.php' != $hook && $_GET['page'] != 'issuepress_options' ) )
          return;

      wp_enqueue_style( 'ip-admin', plugins_url('/assets/css/admin.css', __FILE__) );
  }

  /**
   * Add the IssuePress Page to the Admin Menu
   */
  public function add_menu() {
    add_menu_page( __('IssuePress Options','IssuePress'), 'IssuePress', 'manage_options', 'issuepress_options', array($this, 'draw_options_panel'), plugins_url("/assets/img/issuepress-wordpress-icon-32x32.png", __FILE__ ), 140);
  }

  /**
   * Draw the Options Panel
   */
  public function draw_options_panel() { 
    $tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general';

  ?>

  <div class="wrap">
    <h2><img src="<?php echo plugins_url("/assets/img/mark.svg", __FILE__ ); ?>" style="vertical-align:middle; top: -2px; position: relative;" width="32" height="32" alt=""> <?php _e("IssuePress Options Panel","issuepress"); ?></h2>
    <?php $this->render_admin_tabs(); ?>
    <form action="options.php" method="post">
    <?php
      settings_fields( $tab );
      do_settings_sections( $tab );
      submit_button();
    ?>
    </form>
  </div>

<?php
  }

  /**
   * Render the Tabs for the admin page
   */
  public function render_admin_tabs() {
    $current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
    echo '<h2 class="nav-tab-wrapper">';
    foreach ( $this->settings_tabs as $tab_key => $tab_caption ) {
      $active = $current_tab == $tab_key ? 'nav-tab-active' : '';
      echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>'; 
    }
    echo '</h2>';
  }

  /**
   * General Settings Initial Setup
   * Binds some stuff so we can use ajax to create a page
   */
  public function general_initial_setup() {
    echo '<script type="text/javascript" src="' . plugins_url('vendor/chosen/chosen.jquery.js', __FILE__ ) . '"></script>';
    echo '<style type="text/css"> @import url("' . plugins_url('vendor/chosen/chosen.min.css', __FILE__ ) . '"); </style>';
    ?>

    <p><?php _e( 'Enter your Github personal token and select your support page and repositories below. <a href="http://issuepress.co/docs/" target="_blank" title="Read the documentation">Read the documentation</a>.','IssuePress'); ?></p>
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



  /**
   * Begin Field output
   */


  /**
   * Build the GitHub token field
   */
  public function general_gh_token() {
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

  /**
   * Build the Support Page ID field
   */
  public function general_page_id() {
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

  /**
   * Build the repos field
   */
  public function general_gh_repos(){
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

  public function customize_initial_setup() {
    wp_enqueue_media();
    wp_enqueue_script('my-admin-js');

    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script(
      'iris',
      admin_url( 'js/iris.min.js' ),
      array( 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ),
      false,
      1
    ); 
?>

    <p><?php _e( 'Customize the look of your IssuePress templates below. <a href="http://issuepress.co/docs/" target="_blank" title="Read the documentation">Read the documentation</a> for more information.','IssuePress'); ?> </p>

<?php
  }

  public function customize_header() { ?>

    <script type="text/javascript">
      jQuery(document).ready(function($){

        var custom_uploader;
     
        $('#image-upload').click(function(e) {
          e.preventDefault();
   
          //If the uploader object has already been created, reopen the dialog
          if (custom_uploader) {
            custom_uploader.open();
            return;
          }
   
          //Extend the wp.media object
          custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose Image For IssuePress Header',
            button: {
              text: 'Choose Image'
            },
            multiple: false
          });
   
          //When a file is selected, grab the URL and set it as the text field's value
          custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#image-url').val(attachment.url);
          });
   
          //Open the uploader dialog
          custom_uploader.open();
   
        });
      });
    </script>
    <label for="image-upload">Enter a URL or upload an image</label><br>
    <input id="image-url" type="text" size="36" name="image-url" value="http://" /> 
    <input id="image-upload" class="button" name="image-upload" type="button" value="Upload Image" />

<?php
  }

  public function customize_color() { ?>

    <script type="text/javascript">
      jQuery(document).ready(function($){
        $('.color-picker').iris({
          palettes: ["#936091", "#5270cb", "#32a84e", "#d18023"]
        });
      });
    </script>
    <input type="text" name="color" id="color" class="color-picker" value="#936091" />

<?php
  }

  /**
   * End Field  Output
   */


  /**
   * Validate the IssuePress General Settings
   */
  public function general_settings_validate($input) {

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

  /**
   * Validate IssuePress Customization Settings
   */
  public function customize_settings_validate($input) {
    return $input;
  }



  /** 
   * Method to create a WP page from the IssuePress admin settings page
   */
  public function ajax_create_page() {
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


  /**
   * Add plugin action links
   */
  public function add_action_links($links, $file) {

    if($file == plugin_basename(IP_MAIN_PLUGIN_FILE)) {

      $ip_settings = get_admin_url('', '/admin.php?page=issuepress_options');
      array_unshift($links, "<a href='$ip_settings'>Settings</a>");

    }

    return $links;
  }

  /**
   * Add plugin meta links
   */
  public function add_meta_links($links, $file) {

    if($file == plugin_basename(IP_MAIN_PLUGIN_FILE)) {

      array_push($links, '<a target="_blank" href="http://issuepress.co/docs/">Documentation</a>');

    }

    return $links;
  }


}

$issuepress_admin = new UPIP_admin();
