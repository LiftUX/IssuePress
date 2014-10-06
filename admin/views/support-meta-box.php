<?php
/**
 * Represents the view for the support request meta box.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */


		$status = get_support_request_status( $support_request->ID );

		// Support Request Status - defaults to 'open'
?>
		<p>
			<label for="ip_support_status"><?php _e( 'Status:', $this->name ); ?></label>
			<select name="ip_support_status">
				<option value="open" ><?php _e( 'Open', $this->name ); ?></option>
				<option value="closed" ><?php _e( 'Closed', $this->name ); ?></option>
			</select>
		</p>

<?php
		// Support Request Section - Default From Settings

		$sections = get_ip_sections();
		$default_section = get_ip_default_section();
		$current_section = get_ip_sections_by_id( $support_request->ID );

		if( empty( $current_section ) ) {
			$current_section = $default_section;
		} else {
			$current_section = $current_section[0]->term_id;
		}

?>
		<p>
			<label for="ip_support_section"><?php _e( 'Section:', $this->name ); ?></label>
			<select name="ip_support_section">
				<option value="">-- Select a Section --</option>
<?php foreach( $sections as $section ): 
	$section->term_id == $current_section ? $selected = 'selected' : $selected = ''; ?>

		<option value="<?php echo $section->term_id; ?>" <?php echo $selected; ?>><?php echo $section->name; ?></option>

<?php endforeach; ?>
			</select>
		</p>

<?php

		wp_nonce_field( 'ip_support_meta_box', 'ip_support_meta_box_nonce' );

