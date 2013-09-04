<?php
if(!class_exists('ip_message')){
  class ip_message extends IP_Widget{
    protected $fields = array(
      'title' => 'Title',
      'msg'  => 'Message'
    );

    public function __construct(){
      parent::__construct(
        'ip_message',
        'IP Message Box',
        array('description' => __('Displays an IP Notification Message Box. For use on IssuePress Sidebars only.', 'IssuePress'))
      );
    }

    public function widget($args, $instance){
      extract($args);
      extract($instance);

      echo $before_widget;

      $title = apply_filters('widget_title', $title);
      if($title)
        echo $before_title . $title . $after_title;

      echo $after_widget;
    }

    public function update($new_instance, $old_instance){
      return $new_instance;
    }

    public function form($instance){
      $form = '';

      foreach($this->fields as $id => $label){
        $field_id = $this->get_field_id($id);
        $field_name = $this->get_field_name($id);

        if(isset($instance[$id]))
          $instance_var = $instance[$id];
        else
          $instance_var = '';

        $form .= "<p><label for='$field_id'>$label</label>";

        if($id == 'title')
          $form .= "<input class='widefat' id='$field_id' name='$field_name' type='text' value=\"$instance_var\">";
        else
          $form .= "<textarea class='widefat' id='$field_id' name='$field_name'>$instance_var</textarea>";

        $form .= "</p>";

      }
      echo $form;
    }
  }
  add_action('widgets_init', create_function('', 'register_widget( "ip_message" );'));
}
