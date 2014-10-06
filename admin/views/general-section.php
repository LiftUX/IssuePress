<?php
/**
 * Represents the view for the general settings tab. It includes the fields for this settings section.
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
	echo '<script type="text/javascript" src="' . plugins_url('vendor/chosen/chosen.jquery.js', ISSUEPRESS_PLUGIN_BASENAME ) . '"></script>';
	echo '<style type="text/css"> @import url("' . plugins_url('vendor/chosen/chosen.min.css', ISSUEPRESS_PLUGIN_BASENAME ) . '"); </style>';
?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$(".chosen").chosen();
	$("#create-new-support-page").on("click",function(e){
		e.preventDefault();

		$.post(
			// see tip #1 for how we declare global javascript variables
			ajaxurl,
			{
				action : 'ip-create-page',
				support_page_nonce : '<?php echo wp_create_nonce( "create-support-page" ); ?>'
			},
			function( response ) {
				$('#ip_support_page_id')
					.append($('<option>', {
						value: response.page_ID,
						text: response.page_title
					}))
					.val(response.page_ID);

				var edit_url = "<?php echo admin_url('post.php?action=edit&post='); ?>" + response.page_ID;

				console.log("Logging!");
				console.log(edit_url);

				$('<span>')
					.insertAfter( $('#ip_support_page_id') )
					.addClass('ip-alert')
					.html('<a href="' + edit_url + '" target="_blank"><?php _e('Support Page Created', $this->name); ?></a>. <?php _e('Please save changes.', $this->name); ?>');

				$('#create-new-support-page')
					.remove();
			});
	});
});
</script>
