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

    var $ext_id;
    var $ext_name;
    var $ext_options;

    function __construct( $ext_id, $ext_name, $ext_options = array(), $ext_dependancies = array() ) {

      $this->ext_id = $ext_id;
      $this->ext_name = $ext_name;
      $this->ext_options = $ext_options;
      

      $this->register_extension();
    }

    /**
     * Register the extension with IssuePress Core
     */
    protected function register_extension(){




    }


  }

}


