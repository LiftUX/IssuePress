<?php
/**
 * Represents the view for the administration tabs that controls which settings section to show.
 *
 * @package   IssuePress
 * @author    Matthew Simo <matthew.simo@liftux.com>
 * @license	  GPL-2.0+
 * @link      http://issuepress.co
 * @copyright 2014 Matthew Simo
 */

	$current_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : $this->general_settings_key;
	echo '<h2 class="nav-tab-wrapper">';
	foreach ( $this->settings_tabs as $tab_key => $tab_caption ) {
		$active = $current_tab == $tab_key ? 'nav-tab-active' : '';
		echo '<a class="nav-tab ' . $active . '" href="?page=' . $this->options_key . '&tab=' . $tab_key . '">' . $tab_caption . '</a>';
	}
	echo '</h2>';
