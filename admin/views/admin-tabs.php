<?php
/**
 * Represents the view for the administration tabs that controls which settings section to show.
 *
 * @since				1.0.0
 * @package			IssuePress
 * @subpackage	IssuePress/admin/views
 * @author			Matthew Simo <matthew.simo@liftux.com>
 * @license			GPL-2.0+
 * @link				http://issuepress.co
 */

	$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_key;
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $this->settings_tabs as $tab_key => $tab_caption ) {
		$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
		echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->plugin->get_options_key() . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
	}
	echo '</h2>';
