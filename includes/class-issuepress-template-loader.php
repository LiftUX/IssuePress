<?php

/**
 * Template loader for IssuePress.
 *
 * @since			1.0.0
 * @type			class
 *
 */

if( ! class_exists( 'Gamajo_Template_Loader' ) ) {
	require_once ISSUEPRESS_PLUGIN_DIR . 'includes/class-gamajo-template-loader.php';
}

class IssuePress_Template_Loader extends Gamajo_Template_Loader {

	/**
	 * Prefix for filter names.
	 *
	 * @since		1.0.0
	 * @type		string
	 */
	protected $filter_prefix = 'ip';

	/**
	 * Directory name where custom templates for this plugin should be found in the theme.
	 *
	 * @since		1.0.0
	 * @type		string
	 */
	protected $theme_template_directory = 'ip-templates';

	/**
	 * Reference to the root directory path of this plugin.
	 *
	 * @since		1.0.0
	 * @type		string
	 */
	protected $plugin_directory = ISSUEPRESS_PLUGIN_DIR;

	public function get_clean_template_part( $slug, $name = null, $load = true ) {

		ob_start();
		$this->get_template_part( $slug, $name, $load );
		return ob_get_clean();

	}

}
