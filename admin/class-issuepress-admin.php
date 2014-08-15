<?php
/**
 * IssuePress
 *
 * @package   IssuePress_Admin
 * @author    Matthew Simo <matthew.simo@liftux.com>
 * @license   GPL-2.0+
 * @link      http://issuepress.co
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
 * @author  Matthew Simo <matthew.simo@liftux.com>
 */
class IssuePress_Admin {

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

  /**
   * Slug of the plugin
   *
   * @since 1.0.0
   *
   * @var string
   */
  protected $plugin;

	/**
	 * Slug of the plugin screen.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_screen_hook_suffix = null;

  protected $settings = array();
  private $options_key;
  private $general_settings_key = 'general';
  private $extensions_key = 'extensions';

	/**
	 * Initialize the plugin by loading admin scripts & styles and adding a
	 * settings page and menu.
	 *
	 * @since     1.0.0
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
    add_action('admin_init', array($this, 'register_ip_settings'));
    add_action('admin_init', array($this, 'register_general_section'));
    add_action('admin_init', array($this, 'register_extensions_section'));

    // Run anything that the plugin might require in 'init' action
    add_action( 'init', array( $this, 'on_init' ) );
    add_action( 'init', array( $this, 'load_settings' ) );

		// Load admin style sheet and JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
    add_action( 'admin_print_styles', array($this, 'resize_icon' ) );

		// Add the options page and menu item.
		add_action( 'admin_menu', array( $this, 'add_plugin_admin_menu' ) );

		// Add an action link pointing to the options page.
		$plugin_basename = plugin_basename( plugin_dir_path( realpath( dirname( __FILE__ ) ) ) . $this->plugin_slug . '.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		/*
		 * Define custom functionality.
		 *
		 * Read more about actions and filters:
		 * http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return an instance of this class.
	 *
	 * @since     1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		/*
		 * @TODO :
		 *
		 * - Uncomment following lines if the admin class should only be available for super admins
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
      'name' => __( 'Support Requests' ),
      'singular_name' => __( 'Support Request' ),
    ));

    $support_request_args = array(
      'labels' => $support_request_labels,
      'public' => true,
      'has_archive' => false,
    );

    register_post_type( 'ip_support_request', apply_filters( 'ip_support_request_post_type_args', $support_request_args)); 


    // Create Support Request Sections Custom Taxonomy
    $support_section_labels = apply_filters( 'ip_support_section_labels', array(
        'name' => __( 'Support Sections' ),
        'singular_name' => __( 'Support Section' ),
        'add_new_item' => __( 'Add New Support Section' ),
        'new_item_name' => __( 'New Support Section' )
    ));

    $support_section_args = apply_filters( 'ip_support_section_args', array(
      'labels' => $support_section_labels 
    ));

    register_taxonomy( 'ip_support_section', 'ip_support_request', $support_section_args);


    // Create Support Request Labels Custom Taxonomy
    $support_label_labels = apply_filters( 'ip_support_label_labels', array(
      'name' => __( 'Support Labels' ),
      'singular_name' => __( 'Support Label' ),
      'add_new_item' => __( 'Add New Support Label' ),
      'new_item_name' => __( 'New Support Label' )
    ));

    $support_label_args = apply_filters( 'ip_support_label_args', array(
      'labels' => $support_label_labels 
    ));

    register_taxonomy( 'ip_support_label', 'ip_support_request', $support_label_args);



  }

  /**
   * Loads tabs settings from DB into their own arrays.
   */
  public function load_settings(){

    $this->settings = (array) get_option( $this->options_key );

    $this->settings = array_merge( array(
      'ip_test_setting' => '',
      'ip_license_key' => '',
      'ip_ext_github_sync_name' => ''
    ), $this->settings );

    $this->plugin->set_plugin_settings( $this->settings );

  }

  /**
   * Registers the IP Settings 
   */
  public function register_ip_settings(){
    register_setting($this->options_key, $this->options_key, array($this,'settings_validate'));
  }


  /**
   * Registers the general settings section & fields
   */
  public function register_general_section(){

    $this->settings_tabs[$this->general_settings_key] = 'General';

    $section_key = 'section-general';

    add_settings_section(
      $section_key,
      'General Settings',
      array($this,'render_general_section'),
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
    $this->settings_tabs[$this->extensions_key] = 'Extensions';

    $section_key = 'section-extensions';

    add_settings_section(
      $section_key,
      'IssuePress Extensions',
      array($this,'render_extensions_section'),
      $this->extensions_key
    );

    do_action( $this->plugin_slug . '_extensions_settings_fields');

  }

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
	 * Register and enqueue admin-specific style sheet.
	 *
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
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
	 * @since     1.0.0
	 *
	 * @return    null    Return early if no settings page is registered.
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
	 * @since    1.0.0
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
	 * @since    1.0.0
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
  public function render_extensions_section($args) {

    include_once( 'views/extensions.php' );

  }



	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since    1.0.0
	 */
	public function add_action_links( $links ) {

		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_slug ) . '">' . __( 'Settings', $this->plugin_slug ) . '</a>'
			),
			$links
		);

	}

	/**
	 * NOTE:     Actions are points in the execution of a page or process
	 *           lifecycle that WordPress fires.
	 *
	 *           Actions:    http://codex.wordpress.org/Plugin_API#Actions
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Action_Reference
	 *
	 * @since    1.0.0
	 */
	public function action_method_name() {
		// @TODO: Define your action hook callback here
	}

	/**
	 * NOTE:     Filters are points of execution in which WordPress modifies data
	 *           before saving it or sending it to the browser.
	 *
	 *           Filters: http://codex.wordpress.org/Plugin_API#Filters
	 *           Reference:  http://codex.wordpress.org/Plugin_API/Filter_Reference
	 *
	 * @since    1.0.0
	 */
	public function filter_method_name() {
		// @TODO: Define your filter hook callback here
	}

}
