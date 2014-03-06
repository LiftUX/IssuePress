<?php
if(!class_exists('IP_Widget')){
  class IP_Widget extends WP_Widget{

    public function update($new_instance, $old_instance){
      return $new_instance;
    }

  }
}
