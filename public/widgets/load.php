<?php
/**
 * The File that loads the Widget Classes
 *
 * @package			IssuePress
 * @subpackage	IssuePress/public/widgets	
 */

/**
 * If you use this filter, be sure and pass the proper path. 
 * There is the rick of including relative paths if you're not careful.
 */
$ip_load_widget_paths = apply_filters( 'ip_load_widget_paths', array( 
	'search-form.php',
	'new-request-form.php',
) );

foreach ( $ip_load_widget_paths as $path ) {

	include_once($path);

}
