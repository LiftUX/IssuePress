<?php
/**
 * Represents the view for the landing page ID field on the general settings tab.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */
?>

<select id="ip_support_page_id" name="<?php echo $this->plugin->get_options_key(); ?>[ip_support_page_id]">
	<option value=""><?php _e( 'Select Page', $this->name ); ?></option>

	<?php 

		$pages = get_pages(array(
			'sort_order' => 'ASC',
			'post_status' => 'publish'
		));

		foreach( $pages as $page ) :

		$selected = '';
		if( $page->ID == get_ip_support_page_id() ) {
			$selected = 'selected';
		}

		$output = '<option value="' . $page->ID . '" ' . $selected . '>' . $page->post_title . '</option>';

		echo $output;

		endforeach; 
	?>

</select>

<?php if( !$this->plugin->get_plugin_setting_by_key('ip_support_page_id') ) : ?>

<button class="button secondary" id="create-new-support-page"><?php _e('+ Create New', $this->name); ?></button>

<?php else: ?>

<span><a href="<?php echo admin_url('post.php?action=edit&post=') . get_ip_support_page_id(); ?>" target="_blank">Edit Support Page</a></span>

<?php endif; ?>

<?php settings_errors('ip_support_page_id'); ?>
