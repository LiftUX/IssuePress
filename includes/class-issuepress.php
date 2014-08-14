<?php
/**
 * IssuePress
 *
 * @package   IssuePress
 * @author    Matthew Simo <matthew.simo@liftux.com>
 * @license   GPL-2.0+
 * @link      http://issuepress.co
 * @copyright 2014 Matthew Simo
 */

/**
 * IssuePress class. This class should ideally be used to work with
 * general functionality that could/should effect both public-facing
 * & admin sides of the WordPress site.
 *
 * If you're interested in introducing exclusively administrative or dashboard
 * functionality, then refer to `admin/class-issuepress-admin.php`
 *
 * If you're interested in introducing exclusively public-facing
 * functionality, then refer to `public/class-issuepress-public.php`
 *
 *
 * @package IssuePress
 * @author	Matthew Simo <matthew.simo@liftux.com>
 */
class IssuePress {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since	 1.0.0
	 *
	 * @var		 string
	 */
	const VERSION = '1.0.0';

	/**
	 * Unique identifier for your plugin.
	 *
	 *
	 * The variable name is used as the text domain when internationalizing strings
	 * of text. Its value should match the Text Domain file header in the main
	 * plugin file.
	 *
	 * @since    1.0.0
	 *
	 * @var      string
	 */
	protected $plugin_slug = 'issuepress';

	/**
	 * Instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @var      object
	 */
	protected static $instance = null;

	/**
	 * Extensions in use.
	 *
	 * @since    1.0.0
	 *
	 * @var      array
	 */
	public $extensions = array(
							array(
								'id' 	=> 'test',
								'name'	=> 'Test Name',
								'opts'  => array(
										'description' => 'No description'
										)
									)
								);

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since     1.0.0
	 */
	private function __construct() {

		// Load plugin text domain
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Run anything that the plugin might require in 'init' action
		add_action( 'init', array( $this, 'on_init' ) );

		// Activate plugin when new blog is added
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Load public-facing style sheet and JavaScript.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );



		/* Define custom functionality.
		 * Refer To http://codex.wordpress.org/Plugin_API#Hooks.2C_Actions_and_Filters
		 */
		add_action( '@TODO', array( $this, 'action_method_name' ) );
		add_filter( '@TODO', array( $this, 'filter_method_name' ) );

	}

	/**
	 * Return the plugin slug.
	 *
	 * @since    1.0.0
	 *
	 * @return   Plugin slug variable.
	 */
	public function get_plugin_slug() {
		return $this->plugin_slug;
	}

	/**
	 * Called when wordpress action 'init' is fired.
	 *
	 * @since    1.0.0
	 *
	 * @return   Void.
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
	 * Return an instance of this class.
	 *
	 * @since    1.0.0
	 *
	 * @return    object    A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null == self::$instance ) {
			self::$instance = new self;
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Activate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide	) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}

		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 *
	 * @param    boolean    $network_wide    True if WPMU superadmin uses
	 *                                       "Network Deactivate" action, false if
	 *                                       WPMU is disabled or plugin is
	 *                                       deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}

		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since    1.0.0
	 *
	 * @param    int    $blog_id    ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since    1.0.0
	 *
	 * @return   array|false    The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids
		$sql = "SELECT blog_id FROM $wpdb->blogs
			WHERE archived = '0' AND spam = '0'
			AND deleted = '0'";

		return $wpdb->get_col( $sql );

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since    1.0.0
	 */
	private static function single_activate() {
		// @TODO: Define activation functionality here
		//

	}

	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since    1.0.0
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		$domain = $this->plugin_slug;
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, trailingslashit( WP_LANG_DIR ) . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, FALSE, basename( plugin_dir_path( dirname( __FILE__ ) ) ) . '/languages/' );

	}

	/**
	 * Register and enqueue public-facing style sheet.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		wp_enqueue_style( $this->plugin_slug . '-plugin-styles', plugins_url( 'assets/css/public.css', __FILE__ ), array(), self::VERSION );
	}

	/**
	 * Register and enqueues public-facing JavaScript files.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( $this->plugin_slug . '-plugin-script', plugins_url( 'assets/js/public.js', __FILE__ ), array( 'jquery' ), self::VERSION );
	}

}
