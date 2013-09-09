<?php

/**
 * Get Plugin Version
 * @return plugin version
 */

function plugin_name_get_version() {
  $plugin_data = get_plugin_data( __FILE__ );
  $plugin_version = $plugin_data['Version'];
  return $plugin_version;
}

function get_dynamic_sidebar($index = 1) {
  $sidebar_contents = "";
  ob_start();
  dynamic_sidebar($index);
  $sidebar_contents = ob_get_contents();
  ob_end_clean();
  return $sidebar_contents;
}
