<?php

/**
 * This is the recommended Parent Class for IP Extensions.
 * We expose the class if IssuePress is activated,
 * you can subclass your extension from this class.
 * There are some useful methods for interacting with IssuePress Core.
 *
 * @package IssuePress
 * @author  Matthew Simo <matthew.simo@liftux.com>
 */

// Check to see if the IP_Extension class already exists for namespace trampling.
if(!class_exists('IP_Extension')){

	class IP_Extension {

    /**
     * Hold an instance of the IP Core
     *
     * @since 1.0.0
     *
     * @var object
     */
    protected $plugin;

		/**
		 * This Extension's ID
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $ext_id;

		/**
		 * This Extension's Name
		 *
		 * @since 1.0.0
		 *
		 * @var string
		 */
		protected $ext_name;

    /**
     * This Extension's Options 
     *
     * @since 1.0.0
     *
     * @var array
     */
    protected $ext_meta;

    function __construct( $ext_id, $ext_name, $ext_meta = array(), $ext_dependancies = array() ) {

			global $IssuePress;
			$this->plugin = $IssuePress;
      $this->options_key = $this->plugin->get_options_key();

      $this->ext_id = $ext_id;
      $this->ext_name = $ext_name;
      $this->ext_options = $ext_meta;
      
      $this->register_extension($ext_id, $ext_name, $ext_meta);

		}

		/**
		 * Register the extension with IssuePress Core
		 */
		public function register_extension($id, $name, $meta){
			$this->plugin->add_extension(array(
				'id' => $id, 
				'name' => $name, 
				'meta' => $meta
			));
    }

    /**
     * Default Render Extention Section View
     */
    public function render_extension_section() { ?>

        <p><?php echo $this->meta['description']; ?></p>

<?php
    }


		/**
		 *  Adds a default setting for given key
		 *
		 *  @since		1.0.0
		 */
		public function add_setting_defaults( $settings, $defaults = array()) {

			foreach ( $defaults as $field_key => $field_value ) {
				if( !array_key_exists( $field_key, $settings ) || empty($settings[$field_key]) ) {
					$settings[$field_key] = $field_value;
				}
			}
			
			return $settings;

		}


	}

}

