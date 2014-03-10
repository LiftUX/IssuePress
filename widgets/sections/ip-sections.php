<?php
if(!class_exists('ip_sections')){
  class ip_sections extends IP_Widget{
    protected $fields = array(
      'title' => 'Title',
    );

    public function __construct(){
      parent::__construct(
        'ip_sections',
        'IP Sections',
        array('description' => __('Displays the list of IP Sections. For use on IssuePress Sidebars only.', 'IssuePress'))
      );
    }

    public function widget($args, $instance){
      extract($args);
      extract($instance);

      $title = apply_filters('widget_title', $title);
      if(!$title)
        $title = '';

      $ng_html =  '<div data-ip-sections title="'. $title .'"></div>';

      echo $ng_html;
    }

//    public function widget($args, $instance){
//      extract($args);
//      extract($instance);
//
//      echo $before_widget;
//
//      $title = apply_filters('widget_title', $title);
//      if($title)
//        echo $before_title . $title . $after_title;
//
//      echo $after_widget;
//    }

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

        $form .= "<input class='widefat' id='$field_id' name='$field_name' type='text' value=\"$instance_var\">";

        $form .= "</p>";

      }
      echo $form;
    }
  }
  add_action('widgets_init', create_function('', 'register_widget( "ip_sections" );'));
}
