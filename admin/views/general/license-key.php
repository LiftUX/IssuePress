<?php
/**
 * Represents the view for the license key field on the general settings tab.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */
?>

<input type="text" name="<?php echo $this->options_key; ?>[ip_license_key]" value="<?php echo esc_attr( $this->settings['ip_license_key'] ); ?>" />

<?php settings_errors('ip_license_key'); ?>
