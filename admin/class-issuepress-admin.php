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
	 * Add Metabox for the Support Requests CPT
	 *
	 * @since		1.0.0
	 */
	public function add_meta_boxes() {

		$cpt = 'ip_support_request';

		add_meta_box(
			'ip_support_meta_box',
			__( 'Support Request Meta', $this->name ),
			array( $this, 'render_support_meta_box' ),
			$cpt,
			'side',
			'high'
		);

	}


	/**
	 * Callback to render the Support Request Meta Box
	 *
	 * @since		1.0.0
	 */
	public function render_support_meta_box( $support_request ) {

		$status = get_support_request_status( $support_request->ID );

		// Support Request Status - defaults to 'open'
?>
		<p>
			<label for="ip_support_status"><?php _e( 'Status:', $this->name ); ?></label>
			<select name="ip_support_status">
				<option value="open" ><?php _e( 'Open', $this->name ); ?></option>
				<option value="closed" ><?php _e( 'Closed', $this->name ); ?></option>
			</select>
		</p>

<?php
		// Support Request Section - No default
?>
		<p>
			<label for="ip_support_section"><?php _e( 'Section:', $this->name ); ?></label>
			<select name="ip_support_section">
				<option value="">-- Select a Section --</option>
			</select>
		</p>

<?php

		wp_nonce_field( 'ip_support_meta_box', 'ip_support_meta_box_nonce' );

	}

	/**
	 * Saves the Support Request Meta on save post action
	 *
	 * @since		1.0.0
	 */
	public function save_support_request_meta( $support_request_ID ) {

		if( isset( $_REQUEST['ip_support_status'] ) ) {
			update_post_meta( $support_request_ID, 'status', $_REQUEST['ip_support_status']);
		}

		// Add section updating based on Default section setting
		
//		if( isset( $_REQUEST['ip_support_section'] ) ) {
//			// Set Section
//			echo "<!-- SET SECTION \n\n";
//			var_dump( $_REQUEST['ip_support_section'] );
//			echo "\n\n -->\n";
//		}

	}




	/**
	 * Adds the Custom Column Header "Status" to the Manage Support Requests Admin Panel
	 *
	 * @since		1.0.0
	 */
	public function add_status_column( $defaults ) {
		$defaults['status'] = __( 'Status', $this->name );
		return $defaults;
	}

	/**
	 * Adds the Status Custom Column Data to the Manage Support Requests Admin Panel for each item
	 *
	 * @since		1.0.0
	 */
	public function add_status_column_data( $column, $support_request_ID ) {
		if( $column != 'status' ) {
			return;
		} else {

			$status = get_support_request_status( $support_request_ID );

			if ( $status == 'closed' ) {
				_e( 'Closed', $this->name );
			} else {
				_e( 'Open', $this->name );
			}

		}
	}

	/**
	 * Register the Status Custom Colum as sortable on the Manage Support Requests Admin Panel
	 *
	 * @since		1.0.0
	 */
	public function sortable_status_column( $columns ) {

		$columns['status'] = 'status';
		return $columns;

	}

	/**
	 * Teach Wordpress about ordering by status
	 *
	 * @since		1.0.0
	 */
	public function status_column_orderby( $vars = array() ) {

		if ( isset( $vars['orderby'] )  && $vars['orderby'] == 'status' ) {
			$vars = array_merge( $vars, array(
				'meta_key'	=> 'status',
				'orderby'		=>	'meta_value'
			));

		}

		return $vars;
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
			'ip_support_page_id',
			'Support Landing Page',
			array($this,'render_landing_page_field'),
			$this->general_key,
			$section_key
		);

		add_settings_field(
			'ip_default_support_section',
			'Default Support Section',
			array($this, 'render_default_support_section_field'),
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
      'Extension Settings',
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

		$settings = (array) get_option( $this->plugin->get_options_key() );
    $settings =  array_merge( $settings , $input );


		// Remove key/value if value is empty
		foreach( $settings as $key => $value ) {

			if( empty($value) ) {
				unset($settings[$key]);
			}

		}

		return $settings;

  }


	/**
	 * The main action used for redirecting IP theme actions not permitted by current user
	 *
	 * @since			1.0.0
	 * @uses			do_action()
	 */
	public function ip_template_redirect() {
		do_action( 'ip_template_redirect' );
	}

	/**
	 *
	 * @since			1.0.0
	 */
	public function ip_post_request() {

		// Bail if no action
		if ( empty( $_POST['action'] ) )
			return;

		// This dynamic action is probably the one you want to use. It narrows down
		// the scope of the 'action' without needing to check it in your function.
		do_action( 'ip_post_request_' . $_POST['action'] );

		// Use this static action if you don't mind checking the 'action' yourself.
		do_action( 'ip_post_request',   $_POST['action'] );

	}

	/**
	 * Handles support request form submissions
	 *
	 * @since			1.0.0
	 */
	public function ip_new_support_request_handler($action = '') {

		// Bail if action is not ip-create-support-request
		if ( $action !== 'ip-create-support-request' )
			return;

		// Nonce check
		if ( !wp_verify_nonce( $_REQUEST['_wpnonce'], $action ) ) {
			return;
		}

		$request_author = get_current_user_id();
		$request_content = $request_title = '';

		if ( !empty( $_POST['ip-support-request-description'] ) ) {
			$request_content = esc_attr( strip_tags( $_POST['ip-support-request-description'] ) );
		}

		if ( !empty( $_POST['ip-support-request-title'] ) ) {
			$request_title = esc_attr( strip_tags( $_POST['ip-support-request-title'] ) );
		} else {
			$request_title = substr($request_content, 0, 18);
		}

		if ( !empty( $_POST['ip-support-request-section'] ) ) {
			$request_section = $_POST['ip-support-request-section'];
		} else {
			$request_section = get_ip_default_section();
		}



		$support_request_data = apply_filters( 'ip_new_support_request_pre_insert', array(
			'post_author'			=> $request_author,
			'post_title'			=> $request_title,
			'post_content'		=> $request_content,
			'comment_status'	=> 'closed',
			'ping_status'			=> 'closed'
		) );

		$support_request_id = wp_insert_post( $support_request_data );

		if( !empty( $support_request_id ) ) {

			update_post_meta( $support_request_id, 'status', 'open');
			update_post_meta( $support_request_id, 'status', 'open');

			do_action( 'ip_new_support_request_post_insert', $support_request_id );

		}



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
	 * Build the Landing Page ID field
	 *
	 * @since 1.0.0
	 */
	public function render_landing_page_field() {

		include_once( 'views/general/landing-page.php' );

	}

	/**
	 * Build the Default Support Section Field
	 *
	 * @since		1.0.0
	 */
	public function render_default_support_section_field() {

		include_once( 'views/general/default-section.php');

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
   * Method to create a WP page from the IssuePress admin settings page
   */
  public function ajax_create_page() {
    header( "Content-Type: application/json" );

    $nonce = $_POST['support_page_nonce'];

    // check to see if the submitted nonce matches with the
    // generated nonce we created earlier
    if ( ! wp_verify_nonce( $nonce, 'create-support-page' ) ){
      $response = json_encode( array( 'message' => 'Invalid nonce', 'nonce' => $nonce ) );
      echo $response;
      exit;
    }

    // ignore the request if the current user doesn't have
    // sufficient permissions
    if ( current_user_can( 'edit_posts' ) ) {

      $page_title = 'Support';

      // create support page
      $support_page_args = array(
        'post_name'				=> 'support',
        'post_status'			=> 'publish',
        'post_title'			=> $page_title,
				'post_type'				=> 'page',
				'post_content'		=> "[ip_support_sections]",
				'comment_status'	=> 'closed',
				'ping_status'			=> 'closed'
      );

      $page_ID = wp_insert_post($support_page_args);

      // generate the response
      $response = json_encode( array( 'success' => true, 'page_ID' => $page_ID, 'page_title' => $page_title ) );

      // response output
      echo $response;
    }
    exit;
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
