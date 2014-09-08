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
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

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
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issuepress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-issuepress-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the Dashboard.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-issuepress-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-issuepress-public.php';

		$this->loader = new IssuePress_Loader();

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

		$this->loader->add_action( 'admin_enqueue_scripts', $ip_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $ip_admin, 'enqueue_scripts' );

		$this->loader->add_action( 'admin_init', $ip_admin, 'register_ip_settings' );
		$this->loader->add_action( 'admin_init', $ip_admin, 'register_general_section' );
		$this->loader->add_action( 'admin_init', $ip_admin, 'register_extensions_section' );


		$this->loader->add_action( 'admin_menu', $ip_admin, 'register_admin_menu' );

		$this->loader->add_filter( 'plugin_action_links_' . $this->get_plugin_name(), $ip_admin, 'add_action_links' );

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
  public function set_plugin_settings($keys = array()){
    return $this->settings = $keys;
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

}
