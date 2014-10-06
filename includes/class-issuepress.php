<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the dashboard.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    IssuePress
 * @subpackage IssuePress/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, dashboard-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    IssuePress
 * @subpackage IssuePress/includes
 * @author     Your Name <email@example.com>
 */
class IssuePress {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      IssuePress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The template loader that's responsible for loading template parts for the plugin.
	 *
	 * @since			1.0.0
	 * @access		protected
	 * @var				IssuePress_Template_loader	$template_loader		Loads template parts for the plugin
	 */
	protected $template_loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * The Options Key
	 *
	 * @since		1.0.0
	 * @access	protected
	 * @var 		string		$options_key	$plugin_name . '_options'.
	 */
	protected $options_key;

	/**
	 * The Plugin Basename
	 *
	 * @since		1.0.0
	 * @access	protected
	 * @var 		string		
	 */
	protected $plugin_basename;

  /**
   * Extensions in use.
   *
   * @since 1.0.0
   * @access protected
   * @var array
   */
  protected $extensions = array();

	/**
   * Settings for the class
   *
   * @since 1.0.0
   * @access protected
   * @var array
   */
  protected $settings = array('ip_license_key' => '', 'ip_ext_github_sync_name'=>'Some Value');

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the Dashboard and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'issuepress';
		$this->options_key = $this->plugin_name . '_options';
		$this->plugin_basename = plugin_basename( ISSUEPRESS_PLUGIN_DIR . $this->plugin_name . '.php' );
		$this->version = '1.0.0';


		$this->load_settings();
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->loader->add_action( 'init', $this, 'load_settings', 10 );
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - IssuePress_Loader. Orchestrates the hooks of the plugin.
	 * - IssuePress_i18n. Defines internationalization functionality.
	 * - IssuePress_Admin. Defines all hooks for the dashboard.
	 * - IssuePress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress-i18n.php';

		/**
		 * The class responsible for finding & loading templates
		 */
		require_once ISSUEPRESS_PLUGIN_DIR . 'includes/class-issuepress-template-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once ISSUEPRESS_PLUGIN_DIR . 'admin/class-issuepress-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once ISSUEPRESS_PLUGIN_DIR . 'public/class-issuepress-public.php';

		/**
		 * Expose IP Functions
		 */
		require_once ISSUEPRESS_PLUGIN_DIR . 'public/functions.php';


		// Instantiate some of our newly required classes
		$this->loader = new IssuePress_Loader();
		$this->template_loader = new IssuePress_Template_Loader();


	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the IssuePress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new IssuePress_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the dashboard functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$ip_admin = new IssuePress_Admin( $this->get_plugin_name(), $this->get_version(), $this );

		$this->loader->add_action( 'admin_notices', $ip_admin, 'deferred_admin_notices' );

		$this->loader->add_action( 'admin_enqueue_scripts', $ip_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $ip_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_init', $ip_admin, 'register_ip_settings' );
		$this->loader->add_action( 'admin_init', $ip_admin, 'register_general_section' );
		$this->loader->add_action( 'admin_init', $ip_admin, 'register_extensions_section' );

		$this->loader->add_filter( 'manage_ip_support_request_posts_columns', $ip_admin, 'add_status_column' );
		$this->loader->add_action( 'manage_ip_support_request_posts_custom_column', $ip_admin, 'add_status_column_data', 10, 2 );
		$this->loader->add_filter( 'manage_edit-ip_support_request_sortable_columns', $ip_admin, 'sortable_status_column' );
		$this->loader->add_filter( 'request', $ip_admin, 'status_column_orderby' );

		$this->loader->add_action( 'add_meta_boxes', $ip_admin, 'add_meta_boxes' );
		$this->loader->add_action( 'save_post_ip_support_request', $ip_admin, 'save_support_request_meta' );

		$this->loader->add_action( 'admin_menu', $ip_admin, 'register_admin_menu' );

		$this->loader->add_filter( 'plugin_action_links_' . $this->plugin_basename, $ip_admin, 'add_action_links' );
    $this->loader->add_filter( 'plugin_row_meta', $ip_admin, 'add_meta_links', 10, 2);

		$this->loader->add_action( 'wp_ajax_ip-create-page', $ip_admin, 'ajax_create_page' );

		$this->loader->add_action( 'template_redirect',			$ip_admin, 'ip_template_redirect', 8 );
		$this->loader->add_action( 'ip_template_redirect',	$ip_admin, 'ip_post_request' );
		$this->loader->add_action( 'ip_post_request', 			$ip_admin, 'ip_new_support_request_handler' );


	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$ip_public = new IssuePress_Public( $this->get_plugin_name(), $this->get_version(), $this );

		$this->loader->add_action( 'wp_enqueue_scripts', $ip_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $ip_public, 'enqueue_scripts' );

		$this->loader->add_action( 'init', $ip_public, 'register_post_types' );
		$this->loader->add_action( 'init', $ip_public, 'register_taxonomies' );
		$this->loader->add_action( 'init', $ip_public, 'register_shortcodes' );

		$this->loader->add_action( 'parse_query', 			$ip_public, 'parse_query' );
		$this->loader->add_filter( 'template_include', 	$ip_public, 'template_include' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    IssuePress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Loads settings from DB
	 *
	 * @since		1.0.0
	 */
	public function load_settings(){

		$settings = (array) get_option( $this->get_options_key() );

		$default_settings = apply_filters( 'ip_default_settings', array(
			'ip_test_setting' => 'IP TEST SETTING DEFAULT',
			'ip_license_key'  => 'IP LICENSE DEFAULT'
		));

		// Set up default initial settings.
		$this->set_plugin_settings( array_merge( $default_settings, $settings ) );

	}



	/**
	 * Return the plugin settings keys.
	 *
   * @since   1.0.0
   *
   * @return  Plugin Settings variable.
   */
  public function get_plugin_settings(){
    return $this->settings;
  }


  /**
   * Set the plugin settings keys.
   *
   * @since   1.0.0
   *
   * @return  Plugin Settings variable.
   */
  public function set_plugin_settings($settings = array()){
    return $this->settings = $settings;
  }

	/**
	 * Get a single plugin setting by key.
	 *
	 * @since		1.0.0
	 * @return	Plugin setting by key
	 */
	public function get_plugin_setting_by_key( $key ) {
		$settings = $this->get_plugin_settings();

		if( array_key_exists( $key, $settings ) ) {
			return $settings[$key];
		} else {
			return false;
		}
	}

	/**
	 * Retrieve the plugin's options key.
	 *
	 * @since			1.0.0
	 * @return		string		$plugin_name . '_key';
	 */
	public function get_options_key() {
		return $this->options_key;
	}

	/**
	 * Retrieve the plugin's basename.
	 *
	 * @since			1.0.0
	 * @return		string		$plugin_basename;
	 */
	public function get_plugin_basename() {
		return $this->plugin_basename;
	}
	/**
	 * Retrieve the current extensions.
	 *
	 * @since			1.0.0
	 * @return		array 		The current arrays in use.
	 */
	public function get_extensions() {
		return $this->extensions;
	}

	/**
	 * Add an extension to the list of active extensions.
	 *
	 * @since			1.0.0
	 */
	public function add_extension($extension) {
		array_push($this->extensions, $extension);
	}


	/**
	 * Get the template loader.
	 *
	 * @since			1.0.0
	 * @return		Class		The Template Loader Class Instance
	 */
	public function get_template_loader() {
		return $this->template_loader;
	}

}
