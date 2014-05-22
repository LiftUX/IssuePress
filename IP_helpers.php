<?php

/**
 * Get Plugin Version
 * @return plugin version
 */

function lighten_color($hex, $steps) {
    // Steps should be between -255 and 255. Negative = darker, positive = lighter
    $steps = max(-255, min(255, $steps));

    // Format the hex color string
    $hex = str_replace('#', '', $hex);
    if (strlen($hex) == 3) {
        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
    }

    // Get decimal values
    $r = hexdec(substr($hex,0,2));
    $g = hexdec(substr($hex,2,2));
    $b = hexdec(substr($hex,4,2));

    // Adjust number of steps and keep it inside 0 to 255
    $r = max(0,min(255,$r + $steps));
    $g = max(0,min(255,$g + $steps));  
    $b = max(0,min(255,$b + $steps));

    $r_hex = str_pad(dechex($r), 2, '0', STR_PAD_LEFT);
    $g_hex = str_pad(dechex($g), 2, '0', STR_PAD_LEFT);
    $b_hex = str_pad(dechex($b), 2, '0', STR_PAD_LEFT);

    return '#'.$r_hex.$g_hex.$b_hex;
}

function issuepress_get_version() {
  $plugin_data = get_plugin_data( __FILE__ );
  $plugin_version = $plugin_data['Version'];
  return $plugin_version;
}

function get_dynamic_sidebar($index = 1) {
  $sidebar_contents = "";
  ob_start();
  dynamic_sidebar($index); // Change this when adding ng_sidebar support
  $sidebar_contents = ob_get_contents();
  ob_end_clean();
  return $sidebar_contents;
}


//
// Add support for fetching the sidebars and using ng_widget to render widge to render widget
//
// function dynamic_ng_sidebar($index = 1) {
//   global $wp_registered_sidebars, $wp_registered_widgets;
// 
//   if ( is_int($index) ) {
//     $index = "sidebar-$index";
//   } else {
//     $index = sanitize_title($index);
//     foreach ( (array) $wp_registered_sidebars as $key => $value ) {
//       if ( sanitize_title($value['name']) == $index ) {
//         $index = $key;
//         break;
//       }
//     }
//   }
// 
//   $sidebars_widgets = wp_get_sidebars_widgets();
//   if ( empty( $sidebars_widgets ) )
//     return false;
// 
//   if ( empty($wp_registered_sidebars[$index]) || !array_key_exists($index, $sidebars_widgets) || !is_array($sidebars_widgets[$index]) || empty($sidebars_widgets[$index]) )
//     return false;
// 
//   $sidebar = $wp_registered_sidebars[$index];
// 
//   $did_one = false;
//   foreach ( (array) $sidebars_widgets[$index] as $id ) {
// 
//     if ( !isset($wp_registered_widgets[$id]) ) continue;
// 
//     $params = array_merge(
//       array( array_merge( $sidebar, array('widget_id' => $id, 'widget_name' => $wp_registered_widgets[$id]['name']) ) ),
//       (array) $wp_registered_widgets[$id]['params']
//     );
// 
//     // Substitute HTML id and class attributes into before_widget
//     $classname_ = '';
//     foreach ( (array) $wp_registered_widgets[$id]['classname'] as $cn ) {
//       if ( is_string($cn) )
//         $classname_ .= '_' . $cn;
//       elseif ( is_object($cn) )
//         $classname_ .= '_' . get_class($cn);
//     }
//     $classname_ = ltrim($classname_, '_');
//     $params[0]['before_widget'] = sprintf($params[0]['before_widget'], $id, $classname_);
// 
//     $params = apply_filters( 'dynamic_sidebar_params', $params );
// 
//     $callback = $wp_registered_widgets[$id]['callback'];
// 
//     var_dump($params);
//     var_dump($callback);
// 
//     do_action( 'dynamic_sidebar', $wp_registered_widgets[$id] );
// 
//     if ( is_callable($callback) ) {
//       call_user_func_array($callback, $params);
//       $did_one = true;
//     }
//   }
// 
//   return $did_one;
// }
