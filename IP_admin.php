<?php

class UPIP_admin {

  public function __construct(){
    add_action('admin_init', array($this, 'UPIP_init'));
    add_action('admin_menu', array($this, 'UPIP_add_menu'));
    add_action('admin_enqueue_scripts', array($this, 'UPIP_admin_scripts'));
  }

  public function UPIP_init(){
    register_setting('upip_options', 'upip_options');

    // retrieve our license key from the DB
    $license_key  = get_option( 'upip_license_key' );

    // setup the updater
    $ip_updater = new IP_Plugin_Updater( IP_STORE_URL, __FILE__, array(
        'version'   => plugin_name_get_version(),     // current version number
        'license'   => $license_key,  // license key (used get_option above to retrieve from DB)
        'item_name' => IP_ITEM_NAME,  // name of this plugin
        'author'    => 'UpThemes'  // author of this plugin
      )
    );
  }

  function UPIP_admin_scripts($hook) {

      if( !isset($_GET['page']) || ('admin.php' != $hook && $_GET['page'] != 'issuepress-options' ) )
          return;

      wp_enqueue_style( 'ip-admin', plugins_url('/assets/css/admin.css', __FILE__) );
  }

  public function UPIP_add_menu() {
    add_menu_page('IssuePress Options', 'IssuePress', 'manage_options', 'issuepress-options', array($this, 'UPIP_draw_options_panel'), plugins_url("/assets/img/issuepress-wordpress-icon-32x32.png", __FILE__ ), 140);
  }

  public function UPIP_draw_options_panel() { ?>

  <div class="wrap">
    <h2><img src="<?php echo plugins_url("/assets/img/mark.svg", __FILE__ ); ?>" style="vertical-align:middle; top: -2px; position: relative;" width="32" height="32" alt=""> IssuePress Options Panel</h2>
    <form action="options.php" method="post">
      <?php settings_fields('upip_options'); ?>
      <?php $options = get_option('upip_options'); ?>
      <table class="form-table">
        <tr valign="top"><th scope="row">Github Access Token:</th>
          <td><input type="text" name="upip_options[oauth_key]" value="<?php echo $options['oauth_key']; ?>" />
          <?php if( !$options['oauth_key'] ){ ?>
           <p><a target="_blank" href="https://github.com/settings/tokens/new">Generate an access token</a> and paste it here. We recommend setting up an IssuePress-specific Github account (with proper access to your repositories) for most installations.</p>
           <?php } ?>
          </td>
        </tr>
        <tr valign="top"><th scope="row">Support Landing Page:</th>
          <td>
            <select id="upip_options" name="upip_options[landing]">
            <option value="">Select Page</option>
            <?php // fetch current pages & their ids as values
            $pages = get_pages(array(
              'sort_order' => 'ASC',
              'post_status' => 'publish'
            ));

            foreach($pages as $page) {
              $selected = '';
              if($page->ID == $options['landing']){
                $selected = 'selected="selected" ';
                $selected_ID = $page->ID;
              }
              echo '<option value="'.$page->ID.'" '.$selected.'>'.$page->post_title.'</option>';
            }
            ?>
            </select>

            <?php if( !$selected_ID ){ ?>
            <button class="button secondary" id="create-new-support-page">+ Create New</button>
            <script type="text/javascript">
            jQuery(document).ready(function($){
              $("#create-new-support-page").on("click",function(e){
                e.preventDefault();

                console.log('request started');

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
                    $('#upip_options')
                      .append($('<option>', {
                          value: response.page_ID,
                          text: response.page_title
                      }))
                      .val(response.page_ID);

                    $('<span>')
                      .insertAfter( $('#upip_options') )
                      .addClass('ip-alert')
                      .text('Please save options below.');

                    $('#create-new-support-page')
                      .remove();
                  }
                );
                });
            });
            </script>
            <?php } ?>

          <td>
        </tr>
  <?php if( !empty($options['oauth_key']) ) {

    $client = new Github\Client();
    $client->authenticate($options['oauth_key'], null, Github\Client::AUTH_HTTP_TOKEN);

    $gh_repos = $client->api('current_user')->repositories(array('per_page' => 100));

    $current_repos = array();
    if(!empty($options['r']))
      $current_repos = $options['r'];
  ?>
        <tr valign="top"><th scope="row">Repos (Check the ones you want added to IssuePress)</th>
          <td>
            <table>
              <thead>
                <td></td>
                <td>Repo Name</td>
                <td>Open Issues</td>
                <td>Private</td>
              </thead>
              <tr><td span="3"><?php echo count($gh_repos); ?></td></tr>

  <?php foreach($gh_repos as $index => $item) {

    if(in_array($item['name'], $current_repos)){
      $checked = 'checked="checked"';
    } else { $checked = ''; }

    echo '<tr>';
    echo '<td><input type="checkbox" name="upip_options[r][]" id="'.$item['name'].'" value="'.$item['name'].'" '.$checked.'></td>';
    echo '<td><strong><label for="' . $item['name'] . '" >'.$item['name'].'</label></strong></td>';
    echo '<td>'.$item['open_issues'].'</td>';
    if($item['private'])
      $private_repo = 'True';
    else
      $private_repo = 'False';
    echo '<td>'.$private_repo.'</td>';
    echo '</tr>';

  }  ?>

            </table>
          </td>
        </tr>
  <?php } ?>

      </table>

      <?php submit_button(); ?>

    </form>
  </div>

<?php
  }
}
new UPIP_admin();

function upip_create_page() {
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

  // IMPORTANT: don't forget to "exit"
  exit;
}

add_action( 'wp_ajax_upip-create-page', 'upip_create_page' );
