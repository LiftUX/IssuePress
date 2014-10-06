<?php
/**
 * Represents the view for the default support section field on the general settings tab.
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
	$sections = get_ip_sections(); 

	if( !empty($sections) ) : ?>

<select id="ip_default_support_section" name="<?php echo $this->plugin->get_options_key(); ?>[ip_default_support_section]">
	<option value=""><?php _e( 'Select Section', $this->name ); ?></option>

	<?php 

		foreach( $sections as $section ) :

		$selected = '';
		if( $section->term_id == get_ip_default_section() ) {
			$selected = 'selected';
		}

		$output = '<option value="' . $section->term_id . '" ' . $selected . '>' . $section->name . '</option>';

		echo $output;

		endforeach; 
	?>

</select>

	<?php if( !$this->plugin->get_plugin_setting_by_key('ip_default_support_section') ) : ?>

	<!--
	<button class="button secondary" id="create-new-support-section"><?php _e('+ Create New', $this->name); ?></button>
	-->

	<?php else: ?>

	<span><a href="<?php echo admin_url('edit-tags.php?taxonomy=ip_support_section&post_type=ip_support_request'); ?>" target="_blank">Edit Support Sections</a></span>

	<?php endif; ?>

<?php else: ?>

<a href="<?php echo admin_url('edit-tags.php?taxonomy=ip_support_section&post_type=ip_support_request'); ?>" target="_blank"><?php _e( "Edit Support Sections", $this->name ); ?></a>

<?php endif; ?>


<?php settings_errors('ip_default_support_section'); ?>
