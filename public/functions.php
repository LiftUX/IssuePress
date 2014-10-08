<?php
/**
 * The functions & template tags exposed by the plugin.
 *
 * @link       http://issuepress.co
 * @since      1.0.0
 *
 * @package    IssuePress
 * @subpackage IssuePress/public
 */


/**
 * Get the IP Custom Post Type for Support Requests
 *
 * @since			1.0.0
 */
function get_ip_support_request_post_type() {
	return 'ip_support_request';

}

/**
 * Get the IP Custom Taxonomy for Support Request Sections
 *
 * @since			1.0.0
 */
function get_ip_support_section_taxonomy() {
	return 'ip_support_section';
}

/**
 * Get the IP Custom Taxonomy for Support Request Labels
 *
 * @since			1.0.0
 */
function get_ip_support_label_taxonomy() {
	return 'ip_support_label';
}


/**
 * Get the IP Landing Page ID From Settings
 *
 * @since			1.0.0
 */
function get_ip_support_page_id () {

	global $IssuePress;
	return $IssuePress->get_plugin_setting_by_key('ip_support_page_id');

}

/**
 * Get the IP Default Section From Settings
 *
 * @since			1.0.0
 */
function get_ip_default_section () {

	global $IssuePress;
	return $IssuePress->get_plugin_setting_by_key('ip_default_support_section');

}

/**
 * Get IP Search Form
 *
 * @uses			$IssuePress->get_template_loader()
 * @since			1.0.0
 */
function ip_get_search_form( $echo = true ) {

	if( $echo ) {
		ip_get_template_part( 'support', 'search-form' );
	} else {
		return ip_get_clean_template_part ( 'support', 'search-form' );
	}

}


/**
 * Get IP Template Part
 *
 * Uses the IP Template Loader to find a proper template
 *
 * @since			1.0.0
 */
function ip_get_template_part ( $slug, $name = null, $load = true ) {

	global $IssuePress;
	$template_loader = $IssuePress->get_template_loader();
	$template_loader->get_template_part($slug, $name, $load);

}

/**
 * Get IP Clean Template Part
 *
 * @uses			$IssuePress->get_template_loader()
 * @since			1.0.0
 */
function ip_get_clean_template_part ( $slug, $name = null, $load = true ) {

	global $IssuePress;
	$template_loader = $IssuePress->get_template_loader();
	return $template_loader->get_clean_template_part($slug, $name, $load);

}


/**
 * Return the status meta data for a given support request
 *
 * @since			1.0.0
 * @return		String		The status (closed|open)
 */
function get_support_request_status ( $support_request_ID = 0 ) {

	if( empty( $support_request_ID ) ) {
		return false;
	}

	$status = get_post_meta( $support_request_ID, 'status', true);

	if( empty( $status ) ) {
		$status = 'open';
	}

	return $status;

}


/**
 * Returns true if there are Support Sections
 *
 * @since			1.0.0
 * @return		BOOL		True if there are support sections, false otherwise
 */
function ip_has_sections () {

	$sections = get_terms( 'ip_support_section' );
	if( !empty( $sections ) ) {
		return true;
	} else {
		return false;
	}
}


/**
 * Returns array of Support Sections 
 *
 * @since			1.0.0
 * @return		Array		The list of support section terms
 */
function get_ip_sections ( $args = array( 'hide_empty' => false ) ) {

	$sections = get_terms( 'ip_support_section', $args );

	if( !empty( $sections ) ) {
		return $sections;
	} else {
		return false;
	}

}

/**
 * Return array of Support Sections by Request ID
 *
 * @since			1.0.0
 * @uses			wp_get_post_terms()
 * @return		Array		The list of sections a post has set.
 */
function get_ip_sections_by_id ( $support_request_id, $args = array() ) {

	return wp_get_post_terms( $support_request_id, get_ip_support_section_taxonomy(), $args );

}


/**
 * Returns the Support Section Permalink
 *
 * @since			1.0.0
 * @return		String		The permalink for the support section
 */
function ip_section_permalink ( $section ) {

	return esc_url(get_term_link($section));

}


/**
 * Returns the IP Form placeholder text
 *
 * @since			1.0.0
 * @return		String		The placeholder text
 */
function get_ip_form_placeholder () {
	return apply_filters( 'ip_form_placeholder', 'What seems to the be problem?' );
}

/**
 * Returns the IP Section Select Form Element
 *
 * @since			1.0.0
 * @return		String		The html markup to display the section select element
 */
function get_ip_form_section_select () {

	if( !ip_has_sections() ) { 
		return;
	}

	$sections = get_ip_sections();
	$default_section = get_ip_default_section();
	$section_select_output = '<div id="ip-support-section-select-wrapper"><label for="ip-support-section">Section: </label>
		<select id="ip-support-section-select" name="ip-support-section" class="ip-select">';

	foreach ($sections as $section) {

		$selected = '';
		if( $default_section == $section->term_id ) {
			$selected = 'selected';
		}

		$section_select_output .= '<option value="' . $section->term_id . '" ' . $selected . '>' . $section->name . '</option>';

	}

	$section_select_output .= '</select></div>';

	return $section_select_output;

}


/**
 * Create a nonce field specifically for the ip form
 *
 * @since			1.0.0
 */
function ip_new_request_process_fields() {

	echo '<input type="hidden" name="action" id="ip_post_action" value="ip-create-support-request" />';
	wp_nonce_field( 'ip-create-support-request' );

}


/**
 * Create a nonce field specifically for the ip search form
 *
 * @since			1.0.0
 */
function ip_search_process_fields() {

	echo '<input type="hidden" name="action" id="ip_post_action" value="ip-search-support-requests" />';
	wp_nonce_field( 'ip-search-support-requests' );

}



