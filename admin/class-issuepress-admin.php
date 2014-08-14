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

		// Create Support Request Custom Post Type
		$support_request_labels = apply_filters( 'ip_support_request_labels', array(
			'name'                => _x( 'Support Requests', 'Post Type General Name', 'issuepress' ),
			'singular_name'       => _x( 'Support Request', 'Post Type Singular Name', 'issuepress' ),
			'menu_name'           => __( 'Support', 'issuepress' ),
			'parent_item_colon'   => __( 'Parent Request:', 'issuepress' ),
			'all_items'           => __( 'All Support Requests', 'issuepress' ),
			'view_item'           => __( 'View Request', 'issuepress' ),
			'add_new_item'        => __( 'Add New Request', 'issuepress' ),
			'add_new'             => __( 'Add New', 'issuepress' ),
			'edit_item'           => __( 'Edit Request', 'issuepress' ),
			'update_item'         => __( 'Update Request', 'issuepress' ),
			'search_items'        => __( 'Search Request', 'issuepress' ),
			'not_found'           => __( 'Not found', 'issuepress' ),
			'not_found_in_trash'  => __( 'Not found in Trash', 'issuepress' ),
		) );

		$support_request_args = array(
			'label'               => __( 'ip_support_request', 'issuepress' ),
			'description'         => __( 'Customer support requests.', 'issuepress' ),
			'labels'              => $support_request_labels,
			'public'              => true,
			'has_archive'         => false,
			'menu_icon'           => 'dashicons-sos',
			'supports'            => array( 'title', 'editor', 'comments', ),
			'hierarchical'        => false,
			'public'              => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_nav_menus'   => true,
			'show_in_admin_bar'   => true,
			'menu_position'       => 24,
			'can_export'          => true,
			'has_archive'         => true,
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'capability_type'     => 'page',
		);

		register_post_type( 'ip_support_request', apply_filters( 'ip_support_request_post_type_args', $support_request_args ) );


		// Create Support Request Sections Custom Taxonomy
		$support_section_labels = apply_filters( 'ip_support_section_labels', array(
			'name'                       => _x( 'Sections', 'Taxonomy General Name', 'issuepress' ),
			'singular_name'              => _x( 'Section', 'Taxonomy Singular Name', 'issuepress' ),
			'menu_name'                  => __( 'Sections', 'issuepress' ),
			'all_items'                  => __( 'All Sections', 'issuepress' ),
			'parent_item'                => __( 'Parent Section', 'issuepress' ),
			'parent_item_colon'          => __( 'Parent Section:', 'issuepress' ),
			'new_item_name'              => __( 'New Section Name', 'issuepress' ),
			'add_new_item'               => __( 'Add New Section', 'issuepress' ),
			'edit_item'                  => __( 'Edit Section', 'issuepress' ),
			'update_item'                => __( 'Update Section', 'issuepress' ),
			'separate_items_with_commas' => __( 'Separate sections with commas', 'issuepress' ),
			'search_items'               => __( 'Search Sections', 'issuepress' ),
			'add_or_remove_items'        => __( 'Add or remove sections', 'issuepress' ),
			'choose_from_most_used'      => __( 'Choose from the most used sections', 'issuepress' ),
			'not_found'                  => __( 'Not Found', 'issuepress' ),
		) );

		$support_section_args = apply_filters( 'ip_support_section_args', array(
			'labels' 				=> $support_section_labels,
			'show_admin_column'     => true,
		));

		register_taxonomy( 'ip_support_section', 'ip_support_request', $support_section_args );

		// Create Support Request Labels Custom Taxonomy
		$support_label_labels = apply_filters( 'ip_support_label_labels', array(
			'name'                       => _x( 'Labels', 'Taxonomy General Name', 'issuepress' ),
			'singular_name'              => _x( 'Label', 'Taxonomy Singular Name', 'issuepress' ),
			'menu_name'                  => __( 'Labels', 'issuepress' ),
			'all_items'                  => __( 'All Labels', 'issuepress' ),
			'parent_item'                => __( 'Parent Label', 'issuepress' ),
			'parent_item_colon'          => __( 'Parent Label:', 'issuepress' ),
			'new_item_name'              => __( 'New Label Name', 'issuepress' ),
			'add_new_item'               => __( 'Add New Label', 'issuepress' ),
			'edit_item'                  => __( 'Edit Label', 'issuepress' ),
			'update_item'                => __( 'Update Label', 'issuepress' ),
			'separate_items_with_commas' => __( 'Separate labels with commas', 'issuepress' ),
			'search_items'               => __( 'Search Labels', 'issuepress' ),
			'add_or_remove_items'        => __( 'Add or remove labels', 'issuepress' ),
			'choose_from_most_used'      => __( 'Choose from the most used labels', 'issuepress' ),
			'not_found'                  => __( 'Not Found', 'issuepress' ),
		) );

		$support_label_args = apply_filters( 'ip_support_label_args', array(
			'labels' 				=> $support_label_labels,
			'show_admin_column'     => true,
		));

		register_taxonomy( 'ip_support_label', 'ip_support_request', $support_label_args );



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
