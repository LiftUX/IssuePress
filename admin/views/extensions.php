<?php
/**
 * Represents the view for the extensions settings tab. It includes the fields for this settings section.
 *
 * @package    IssuePress
 * @author     Matthew Simo <matthew.simo@liftux.com>
 * @license    GPL-2.0+
 * @link       http://issuepress.co
 * @copyright  2014 Matthew Simo
 */


$extensions = $this->plugin->extensions;

echo "<ul id='issuepress-extensions'>";
foreach ($extensions as $ext) {
?>

	<li class="ip-extension <?php echo $ext['id']; ?>">
		<h4><?php echo $ext['name']; ?></h4>
		<p><?php echo $ext['opts']['description']; ?></p>
	</li>

<?php
}
