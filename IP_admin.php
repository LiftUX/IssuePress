<?php

class UPIP_admin {

  public function __construct(){
    add_action('admin_init', array($this, 'UPIP_init'));
    add_action('admin_menu', array($this, 'UPIP_add_menu'));
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
        <tr valign="top"><th scope="row">Github Username</th>
          <td><input type="text" name="upip_options[u]" value="<?php echo $options['u']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Github Password</th>
          <td><input type="password" name="upip_options[p]" value="<?php echo $options['p']; ?>" /></td>
        </tr>
        <tr valign="top"><th scope="row">Support Landing Page</th>
          <td>
            <select name="upip_options[landing]">
            <option value="">Select Page</option>
            <?php // fetch current pages & their ids as values
            $pages = get_pages(array(
              'sort_order' => 'ASC',
              'post_status' => 'publish'
            ));

            foreach($pages as $page) {
              $selected = '';
              if($page->ID == $options['landing'])
                $selected = 'selected="selected" ';
              echo '<option value="'.$page->ID.'" '.$selected.'>'.$page->post_title.'</option>';
            }
            ?>
            </select>
          <td>
        </tr>
  <?php if(!empty($options['u']) && !empty($options['p'])) {

    $client = new Github\Client();
    $client->authenticate($options['u'], $options['p'], Github\Client::AUTH_HTTP_PASSWORD);
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

      <p class="submit">
        <input type="submit" class="button-primary" value="<?php _e('Save Changes'); ?>" />
      </p>
    </form>
  </div>

<?php
  }
}
new UPIP_admin();
