<?php
/**
 * Represents the view for the administration dashboard.
 *
 * This includes the header, tabs, setting sections, and other information that should provide
 * The User Interface to the end user.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */
?>

<div class="wrap">

	<h2><img src="<?php echo plugins_url("../img/mark.svg", __FILE__ ); ?>" style="vertical-align:middle; top: -2px; position: relative;" width="32" height="32" alt=""> <?php echo esc_html( get_admin_page_title() ); ?></h2>
	<?php $this->render_admin_tabs(); ?>
	<form action="options.php" method="post">
	<?php
//
//// TESTING CODE
//
//echo '<h2>get_option</h2><code><pre>';
//var_dump( get_option($this->plugin->get_options_key()) );
//echo '</pre></code>';
//
//
//echo '<h2>get_plugin_settings</h2><code><pre>';
//var_dump( $this->plugin->get_plugin_settings() );
//echo '</pre></code>';
//
// TESTING CODE

		$tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_key;
		settings_fields( $this->plugin->get_options_key() );
		do_settings_sections( $tab );
		submit_button();

	?>
	</form>

</div>
