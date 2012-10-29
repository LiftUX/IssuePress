<?php

add_action('admin_init', 'UPIP_init');
add_action('admin_menu', 'UPIP_add_menu');

function UPIP_init(){
  register_setting('upip_options', 'upip_options');
}

function UPIP_add_menu() {
  add_options_page('IssuePress Options', 'IssuePress', 'manage_options', 'issuepress-options', 'UPIP_draw_options_panel');
}


function UPIP_draw_options_panel() { ?>

<div class="wrap">
  <h2>IssuePress Options Panel</h2>
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

<?php if(!empty($options['u']) && !empty($options['p'])) {

  $client = new Github\Client();
  $client->authenticate($options['u'], $options['p'], Github\Client::AUTH_HTTP_PASSWORD);
  $gh_repos = $client->api('current_user')->repositories();
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

<?php foreach($gh_repos as $index => $item) {

  if(in_array($item['name'], $options['r'])){
    $checked = 'checked="checked"';
  } else { $checked = ''; }

  echo '<tr>';
  echo '<td><input type="checkbox" name="upip_options[r][]" value="'.$item['name'].'" '.$checked.'></td>';
  echo '<td><strong>'.$item['name'].'</strong></td>';
  echo '<td>'.$item['open_issues'].'</td>';
  if($item['private'])
    $private_repo = 'True';
  else
    $private_repo = 'False';
  echo '<td>'.$private_repo.'</td>';
  echo '</tr>';

} ?>

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

<?php }
