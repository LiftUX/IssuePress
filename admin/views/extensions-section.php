
<?php
/**
 * Represents the view for the extensions settings tab. It includes the fields for this settings section.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */

$extensions = $this->plugin->get_extensions();

echo "<ul id='issuepress-extensions-list'>";

if ( !empty($extensions) ) {

	foreach ($extensions as $ext) {
	?>

		<li class="ip-extension-section <?php echo $ext['id']; ?>">

			<?php do_settings_sections( $ext['id'] ); ?>
			
		</li>

	<?php
	}

} else { ?>

	<li class="ip-extension-section ip-no-extensions">
		<h4><?php _e("There are currently no IssuePress extensions installed.", $this->name); ?></h4>
		<p><?php _e("Visit the <a href='http://issuepress.co/extensions'>extensions</a> listing to find them.", $this->name); ?></p>
	</li>

<?php
}

echo "</ul>";


