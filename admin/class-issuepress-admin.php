<?php
/**
 * IssuePress
 *
 * @package	 IssuePress_Admin
 * @author		Matthew Simo <matthew.simo@liftux.com>
 * @license	 GPL-2.0+
 * @link			http://issuepress.co
 * @copyright 2014 Matthew Simo
 */

/**
 * IssuePress_Admin class. This class should ideally be used to work with the
 * administrative side of the WordPress site.
 *
 * If you're interested in introducing public-facing
 * functionality, then refer to `class-plugin-name.php`
 *
 * @package IssuePress_Admin
 * @author	Matthew Simo <matthew.simo@liftux.com>
 */
class IssuePress_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since		1.0.0
	 *
	 * @var			object
	 */
	protected static $instance = null;

	/**
	 * IssuePress plugin instance
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $plugin;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since		1.0.0
	 *
	 * @var			string
	 */
	protected $plugin_screen_hook_suffix = null;

	/**
	 * Holds settings array
	 *
	 * @since		1.0.0
	 *
	 * @var			array
	 */
	private $settings = array();

	/**
	 * Options license key.
	 *
	 * @since		1.0.0
	 *
	 * @var			string
	 */
	private $options_key;

	/**
	 * Settings section name.
	 *
	 * @since		1.0.0
	 *
	 * @var			string
	 */
	private $general_settings_key = 'general';

	/**
	 * Extensions key used by admin functions.
	 *
	 * @since		1.0.0
	 *
	 * @var			string
	 */
	private $extensions_key = 'extensions';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since		 1.0.0
	 */
	private function __construct() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		$this->admin_includes();

		/*
		 * Call $plugin_slug from public plugin class.
		 */
		$this->plugin = IssuePress::get_instance();
		$this->plugin_slug = $this->plugin->get_plugin_slug();
		$this->options_key = $this->plugin_slug . '_options';

		// Register Settings for each tab
		add_action( 'admin_init', array( $this, 'register_ip_settings') );
		add_action( 'admin_init', array( $this, 'register_general_section') );
		add_action( 'admin_init', array( $this, 'register_extensions_section') );

		// Run anything that the plugin might require in 'init' action
		add_action( 'init', array( $this, 'on_init' ) );
		add_action( 'init', array( $this, 'load_settings' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'admin_print_styles', array( $this, 'resize_icon' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since		 1.0.0
	 *
	 * @return		object		A single instance of this class.
	 */
	public static function get_instance() {

		/**
		 * @todo Uncomment following lines if the admin class should only be available for super admins
		 */
		/* if( ! is_super_admin() ) {
			return;
		} */

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	public static function admin_includes(){

	}

	/**
	 * Called when wordpress action 'init' is fired.
	 *
	 * @since 1.0.0
	 *
	 * @return Void.
	 */
	public static function on_init(){


	}

	/**
	 * Loads tabs settings from DB into their own arrays.
	 */
	public function load_settings(){

		$this->settings = (array) get_option( $this->options_key );

		$this->settings = array_merge( array(
			'ip_test_setting' => '',
			'ip_license_key'  => '',
		), $this->settings );

	}

	/**
	 * Registers the IP Settings
	 */
	public function register_ip_settings(){
		register_setting( $this->options_key, $this->options_key, array( $this, 'settings_validate' ) );
	}


	/**
	 * Registers the general settings section & fields
	 */
	public function register_general_section(){

		$this->settings_tabs[ $this->general_settings_key ] = __( 'General' , 'issuepress' );

		$section_key = 'section-general';

		add_settings_section(
			$section_key,
			'General Settings',
			array( $this, 'render_general_section' ),
			$this->general_settings_key
		);

		add_settings_field(
			'ip_license_key',
			'License Key',
			array($this,'render_license_key_field'),
			$this->general_settings_key,
			$section_key
		);


	}

	/**
	 * Register the Extensions section & fields
	 */
	public function register_extensions_section(){
		$this->settings_tabs[ $this->extensions_key ] = __( 'Extensions', 'issuepress' );

		$section_key = 'section-custom';

		add_settings_section(
			$section_key,
			__( 'IssuePress Extensions', 'issuepress' ),
			array( $this, 'render_extensions_section' ),
			$this->extensions_key
		);

	}





	/**
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since		 1.0.0
	 *
	 * @return		null		Return early if no settings page is registered.
	 */
	public function enqueue_admin_styles() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'assets/css/admin.css', __FILE__ ), array(), IssuePress::VERSION );
		}

	}

	/**
	 * Register and enqueue admin-specific JavaScript.
	 *
	 * @since		 1.0.0
	 *
	 * @return		null		Return early if no settings page is registered.
	 */
	public function enqueue_admin_scripts() {

		if ( ! isset( $this->plugin_screen_hook_suffix ) ) {
			return;
		}

		$screen = get_current_screen();
		if ( $this->plugin_screen_hook_suffix == $screen->id ) {
			wp_enqueue_script( $this->plugin_slug . '-admin-script', plugins_url( 'assets/js/admin.js', __FILE__ ), array( 'jquery' ), IssuePress::VERSION );
		}

	}

	/**
	 * Resizes the IssuePress menu icon (retina icon hack)
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function resize_icon(){
		echo '<style type="text/css">#toplevel_page_issuepress_options img{ width: 16px; height: 16px; margin-top: 0; }</style>';
		return;
	}

	/**
	 * Register the administration menu for this plugin into the WordPress Dashboard menu.
	 *
	 * @since		1.0.0
	 */
	public function add_plugin_admin_menu() {

		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'IssuePress Settings', $this->plugin_slug ),
			__( 'IssuePress', $this->plugin_slug ),
			'manage_options',
			$this->options_key,
			array( $this, 'render_admin_page' ),
			plugins_url("assets/img/issuepress-wordpress-icon-32x32.png", __FILE__ ),
			140
		);

	}

	/**
	 * Render Methods for Admin Settings Pages & Sections
	 *
	 * Each is bound to either a WP page, section, or field register call and
	 * should typically includes the corresponding template view in `admin/views/`
	 */


	/**
	 * Render the settings page for this plugin.
	 *
	 * @since		1.0.0
	 */
	public function render_admin_page() {

		include_once( 'views/admin.php' );

	}

	/**
	 * Render the Tabs for the admin page
	 */
	public function render_admin_tabs() {

		include_once( 'views/admin-tabs.php' );

	}

	/**
	 * General Settings Initial Setup
	 * Binds some stuff so we can use ajax to create a page
	 */
	public function render_general_section() {

		include_once( 'views/general.php' );

	}

	/**
	 * Build the License Key field
	 */
	public function render_license_key_field() {

		include_once( 'views/general/license-key.php' );

	}

	/**
	 * Extensions Tab Initial Setup
	 * Binds stuff before rendering fields
	 */
	public function render_extensions_section() {

		include_once( 'views/extensions.php' );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since		1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', 'issuepress' ) . '</a>'
			),
			$links
		);

	}

}
