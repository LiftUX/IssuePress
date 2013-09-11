<?php

/**
 * Get Plugin Version
 * @return plugin version
 */

function issuepress_get_version() {
  $plugin_data = get_plugin_data( __FILE__ );
  $plugin_version = $plugin_data['Version'];
  return $plugin_version;
}