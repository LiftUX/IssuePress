<?php

/**
* Create a sub-page for our IssuePress license key
*
* @uses add_submenu_page()
*
* @return void
*
* @since 0.1
*/
function upip_license_menu() {
    add_submenu_page('issuepress-options','License Key', 'License Key', 'manage_options', 'issuepress-license', 'upip_license_page');
}
add_action('admin_menu', 'upip_license_menu');

/**
* Display a license key management page
*
* @uses get_option()
* @uses settings_fields()
* @uses _e()
* @uses wp_nonce_field()
* @uses submit_button()
*
* @return void
*
* @since 0.1
*/
function upip_license_page() {
  $license  = get_option( 'upip_license_key' );
  $status   = get_option( 'upip_license_status' );
  ?>
  <div class="wrap">
    <h2><img src="<?php echo plugins_url("/assets/img/mark.svg", __FILE__ ); ?>" style="vertical-align:middle; top: -2px; position: relative;" width="32" height="32" alt=""> IssuePress License</h2>
    <form method="post" action="options.php">

      <?php settings_fields('upip_license'); ?>

      <table class="form-table">
        <tbody>
          <tr valign="top">
            <th scope="row" valign="top">
              <?php ('License Key'); ?>
            </th>
            <td>
              <input id="upip_license_key" name="upip_license_key" type="text" class="regular-text" value="<?php esc_attr_e( $license ); ?>" />
              <label class="description" for="upip_license_key"><?php _e('Enter your license key'); ?></label>
            </td>
          </tr>
          <?php if( $license != '' ) { ?>
            <tr valign="top">
              <th scope="row" valign="top">
                <?php _e('Activate License'); ?>
              </th>
              <td>
                <?php if( $status == 'valid' ) { ?>
                  <span style="color:green;"><?php _e('active'); ?></span>
                  <?php wp_nonce_field( 'upip_nonce', 'upip_nonce' ); ?>
                  <input type="submit" class="button-secondary" name="upip_license_deactivate" value="<?php _e('Deactivate License'); ?>"/>
                <?php } else {
                  wp_nonce_field( 'upip_nonce', 'upip_nonce' ); ?>
                  <input type="submit" class="button-secondary" name="upip_license_activate" value="<?php _e('Activate License'); ?>"/>
                <?php } ?>
              </td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <?php submit_button(); ?>

    </form>
  <?php
}

/**
* Register our license key options
*
* @uses register_setting()
*
* @return void
*
* @since 0.1
*/
function upip_register_option() {
  // creates our settings in the options table
  register_setting('upip_license', 'upip_license_key', 'upip_sanitize_license' );
}
add_action('admin_init', 'upip_register_option');

/**
* Sanitize the license key
*
* @uses get_option()
* @uses delete_option()
*
* @return string $new license key
*
* @since 0.1
*/
function upip_sanitize_license( $new ) {
  $old = get_option( 'upip_license_key' );
  if( $old && $old != $new ) {
    delete_option( 'upip_license_status' ); // new license has been entered, so must reactivate
  }
  return $new;
}

/**
* Activate license key on remote server
*
* We send a remote request to activate the license key
* being used on the current domain.
*
* @uses check_admin_referer()
* @uses get_option()
* @uses delete_option()
* @uses urlencode()
* @uses wp_remote_get()
* @uses add_query_arg()
* @uses is_wp_error()
* @uses json_decode()
* @uses wp_remote_retrieve_body()
* @uses update_option()
*
* @return void
*
* @since 0.1
*/
function upip_activate_license() {

  // listen for our activate button to be clicked
  if( isset( $_POST['upip_license_activate'] ) ) {

    // run a quick security check
    if( ! check_admin_referer( 'upip_nonce', 'upip_nonce' ) )
      return; // get out if we didn't click the Activate button

    // retrieve the license from the database
    $license = trim( get_option( 'upip_license_key' ) );

    if( !$license || $license == '' ){
      delete_option( 'upip_license_status' );
      return;
    }

    // data to send in our API request
    $api_params = array(
      'edd_action'=> 'activate_license',
      'license'   => $license,
      'item_name' => urlencode( IP_ITEM_NAME ) // the name of our product in EDD
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, IP_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    // make sure the response came back okay
    if ( is_wp_error( $response ) )
      return;

    // decode the license data
    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    // $license_data->license will be either "active" or "inactive"

    //echo $license_data->license;

    update_option( 'upip_license_status', $license_data->license );

  }
}
add_action('admin_init', 'upip_activate_license');


/**
* Deactivate license key on remote server
*
* We send a remote request to deactivate the license key
* being used on the current domain.
*
* @uses check_admin_referer()
* @uses get_option()
* @uses delete_option()
* @uses urlencode()
* @uses wp_remote_get()
* @uses add_query_arg()
* @uses is_wp_error()
* @uses json_decode()
* @uses wp_remote_retrieve_body()
* @uses update_option()
*
* @return void
*
* @since 0.1
*/
function upip_deactivate_license() {

  // listen for our activate button to be clicked
  if( isset( $_POST['upip_license_deactivate'] ) ) {

    // run a quick security check
    if( ! check_admin_referer( 'upip_nonce', 'upip_nonce' ) )
      return; // get out if we didn't click the Activate button

    // retrieve the license from the database
    $license = trim( get_option( 'upip_license_key' ) );

    if( !$license || $license == '' ){
      delete_option( 'upip_license_status' );
      return;
    }

    // data to send in our API request
    $api_params = array(
      'edd_action'=> 'deactivate_license',
      'license'   => $license,
      'item_name' => urlencode( IP_ITEM_NAME ) // the name of our product in EDD
    );

    // Call the custom API.
    $response = wp_remote_get( add_query_arg( $api_params, IP_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

    // make sure the response came back okay
    if ( is_wp_error( $response ) )
      return;

    // decode the license data
    $license_data = json_decode( wp_remote_retrieve_body( $response ) );

    // $license_data->license will be either "deactivated" or "failed"
    if( $license_data->license == 'deactivated' )
      delete_option( 'upip_license_status' );

  }
}
add_action('admin_init', 'upip_deactivate_license');


/**
* Check license on server
*
* We send a remote request to check the validity of the
* license and update the status based on the response.
*
* @global $wp_version
*
* @uses get_option()
* @uses wp_remote_get()
* @uses add_query_arg()
* @uses is_wp_error()
* @uses json_decode()
* @uses wp_remote_retrieve_body()
*
* @return string valid or invalid
*
* @since 0.1
*/
function upip_check_license() {

  global $wp_version;

  $license = trim( get_option( 'upip_license_key' ) );

  $api_params = array(
    'edd_action' => 'check_license',
    'license' => $license,
    'item_name' => urlencode( IP_ITEM_NAME )
  );

  // Call the custom API.
  $response = wp_remote_get( add_query_arg( $api_params, IP_STORE_URL ), array( 'timeout' => 15, 'sslverify' => false ) );

  if ( is_wp_error( $response ) )
    return false;

  $license_data = json_decode( wp_remote_retrieve_body( $response ) );

  if( $license_data->license == 'valid' ) {
    return 'valid';
    // this license is still valid
  } else {
    return 'invalid';
    // this license is no longer valid
  }
}

function upip_enforce_license(){
  $license_status = upip_check_license();

  if( $license_status !== 'valid' )
    delete_option( 'upip_license_status' );
}

function upip_license_expired( $plugin_data, $r ) {
    echo 'Your license key has expired. Please <a href="http://issuepress.co">upgrade IssuePress</a> to enable automatic updates.';
}