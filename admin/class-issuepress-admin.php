<?php

/**
 * The dashboard-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    IssuePress
 * @subpackage IssuePress/includes
 */

/**
 * The dashboard-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the dashboard-specific stylesheet and JavaScript.
 *
 * @package    IssuePress
 * @subpackage IssuePress/admin
 * @author     Your Name <email@example.com>
 */
class IssuePress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $name    The ID of this plugin.
	 */
	private $name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Instance of IssuePress class
	 *
	 * @since 1.0.0
	 * @access private
	 * @var		object			$plugin			This is the instance of the IssuePress class.
	 */
	private $plugin;

	/**
	 * Settings section name.
	 *
	 * @since		1.0.0
	 * @access	private
	 * @var			string		$general_key			The Key used for the general settings tab.
	 */
	private $general_key = 'general';

	/**
	 * Extensions key used by admin functions.
	 *
	 * @since		1.0.0
	 * @access	private
	 * @var			string		$extensions_key		The key used for the extensions settings tab.
	 */
	private $extensions_key = 'extensions';

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @var      string    $name       The name of this plugin.
	 * @var      string    $version    The version of this plugin.
	 */
	public function __construct( $name, $version, $instance ) {

		$this->name = $name;
		$this->version = $version;
		$this->plugin = $instance;
		$this->plugin_settings = $this->plugin->get_plugin_settings();

	}

	/**
	 * Register the Admin Menu
	 *
	 * @since		1.0.0
	 */
	public function register_admin_menu() {

		$this->plugin_screen_hook_suffix = add_menu_page(
			__( 'IssuePress Settings', $this->name ),
			__( 'IssuePress', $this->name ),
			'manage_options',
			$this->plugin->get_options_key(),
			array( $this, 'render_admin_page' ),
			plugins_url("img/issuepress-wordpress-icon-32x32.png", __FILE__ ),
			140
		);

	}

	/**
	 * Registers the IP Settings
	 *
	 * @since		1.0.0
	 */
	public function register_ip_settings(){
		register_setting( $this->plugin->get_options_key(), $this->plugin->get_options_key(), array( $this, 'settings_validate' ) );
	}


	/**
	 * Registers the general settings section & fields
	 *
	 * @since		1.0.0
	 */
	public function register_general_section(){

		$this->settings_tabs[ $this->general_key ] = __( 'General' , 'issuepress' );

		$section_key = 'section-general';

		add_settings_section(
			$section_key,
			'General Settings',
			array( $this, 'render_general_section' ),
			$this->general_key
		);

		add_settings_field(
			'ip_license_key',
			'License Key',
			array($this,'render_license_key_field'),
			$this->general_key,
			$section_key
		);


	}


  /**
   * Register the Extensions section & fields
	 *
	 * @since		1.0.0
   */
  public function register_extensions_section(){
    $this->settings_tabs[$this->extensions_key] = 'Extensions';

    $section_key = 'section-extensions';

    add_settings_section(
      $section_key,
      'IssuePress Extensions',
      array($this,'render_extensions_section'),
      $this->extensions_key
    );

    do_action( $this->name . '_extensions_settings_fields');

  }


	/**
	 * Validate Settings
	 *
	 * @since		1.0.0
	 */
  public function settings_validate($input) {

    return $input;

//    if(array_key_exists('upip_gh_token', $input)) {
//      $input = $this->general_settings_validate($input);
//    } else {
//      $input = $this->custom_settings_validate($input);
//    }
//    
//    return array_merge( $this->settings, $input );
  }



	/**
	 * Register the stylesheets for the Dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in IssuePress_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The IssuePress_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->name, plugin_dir_url( __FILE__ ) . 'css/issuepress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the dashboard.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in IssuePress_Admin_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The IssuePress_Admin_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->name, plugin_dir_url( __FILE__ ) . 'js/issuepress-admin.js', array( 'jquery' ), $this->version, FALSE );

	}


	/**
	 * Render Methods for Admin Settings Pages, Sections & Views
	 *
	 * Each is bound to either a WP page, section or field register call and 
	 * should typically include the corresponding template view in `admin/views/`.
	 */


	/**
	 * Render the Settings page for issuepress.
	 *
	 * @since 1.0.0
	 */
	public function render_admin_page() {
		include_once( 'views/admin-page.php' );
	}

	/**
	 * Render the Tabs for the admin page
	 *
	 * @since 1.0.0
	 */
	public function render_admin_tabs() {

		include_once( 'views/admin-tabs.php' );

	}

	/**
	 * General Settings Initial Setup
	 * Binds some stuff so we can use ajax to create a page
	 *
	 * @since 1.0.0
	 */
	public function render_general_section() {

		include_once( 'views/general-section.php' );

	}

	/**
	 * Build the License Key field
	 *
	 * @since 1.0.0
	 */
	public function render_license_key_field() {

		include_once( 'views/general/license-key.php' );

	}

	/**
	 * Extensions Tab Initial Setup
	 * Binds stuff before rendering fields
	 *
	 * @since		1.0.0
	 */
	public function render_extensions_section() {

		include_once( 'views/extensions-section.php' );

	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since		1.0.0
	 */
	public function add_action_links( $links ) {

		return $links;

	}

	/**
	 * Add meta links to the plugins page.
	 *
	 * @since		1.0.0
	 */
	public function add_meta_links( $links, $file ) {

    if($file == $this->plugin->get_plugin_basename()) {

      array_push($links, '<a target="_blank" href="http://issuepress.co/docs/">Documentation</a>');
      array_push($links, '<a href="' . admin_url( 'admin.php?page=' . $this->plugin->get_options_key() ) . '">' . __( 'Settings', $this->name ) . '</a>');

    }

    return $links;

	}

}
