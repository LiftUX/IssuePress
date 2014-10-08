<?php
/**
 * Represents the view for the default post status field on the general settings tab.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */
?>


<?php

$statuses = array(
	'draft' => "Draft",
	'publish' => "Published"
);

?>


<select id="ip_default_post_status" name="<?php echo $this->plugin->get_options_key(); ?>[ip_default_post_status]">
	<option value=""><?php _e( 'Select Status', $this->name ); ?></option>

	<?php 


		foreach( $statuses as $key => $title ) :

		$selected = '';
		if( $key == $this->plugin->get_plugin_setting_by_key('ip_default_post_status') ) {
			$selected = 'selected';
		}

		$output = '<option value="' . $key . '" ' . $selected . '>' . $title . '</option>';

		echo $output;

		endforeach; 
	?>

</select>
<p class="description">Select the post status of support requests created via <code>[ip_support_form]</code> shortcode.</p>


<?php settings_errors('ip_default_post_status'); ?>
