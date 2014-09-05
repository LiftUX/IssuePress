<!--

<code><pre>
<?php var_dump($args); ?>
</pre></code>

-->

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
foreach ($extensions as $ext) {
?>

	<li class="ip-extension <?php echo $ext['id']; ?>">

    <?php do_settings_sections( 'ip-gh-sync' ); ?>
    
  </li>

<?php
}

echo "</ul>";
