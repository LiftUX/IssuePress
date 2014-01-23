<?php
if(!class_exists('ip_recent_activity')){
  class ip_recent_activity extends IP_Widget{
    protected $fields = array(
      'title' => 'Title',
    );

    public function __construct(){
      parent::__construct(
        'ip_recent_activity',
        'IP Recent Activity',
        array('description' => __('Displays a "context aware" list of recent IP Section Activity. For use on IssuePress Sidebars only.', 'IssuePress'))
      );
    }

    public function widget($args, $instance){
      extract($args);
      extract($instance);

      $title = apply_filters('widget_title', $title);
      if(!$title)
        $title = '';

//      $ng_html =  '<div data-ip-recent-activity title="'. $title .'">' .
//                    '<div data-ng-repeat="item in activity">' .
//                      '<div data-ip-recent-activity-item ng-switch="item.type">' .
//
//                        '<div data-ng-switch-when="issue_comment">'.
//                          '<div data-ip-recent-activity-item-title href="{{item.href}}">{{item.title}}</div>'.
//                          '<div data-ip-recent-activity-item-meta>{{item.meta}}</div>'.
//                          '<div>{{item.comment.body}}</div>'.
//                        '</div>'.
//
//                        '<div data-ng-switch-when="issue">'.
//                          '<div data-ip-recent-activity-item-meta>{{item.meta}}</div>'.
//                          '<div data-ip-recent-activity-item-title href="{{item.href}}">{{item.title}}</div>'.
//                        '</div>'.
//
//                      '</div>'.
//                    '</div>' .
//                  '</div>';
//
      $ng_html =  '<div data-ip-recent-activity title="'. $title . '">' .
                    '<div data-ng-show="activity" ng-if="activity" data-ng-repeat="item in activity" data-ip-recent-activity-item ' . 
                          'href="#/{{repo}}/{{item.issue.number}}" ' .
                          'timeago="{{item.created_at}}"> ' . 

                      '<div data-ip-recent-activity-item-meta><a href="#/{{repo}}/{{item.issue.number}}">{{item.actor.login}}</a> {{item.event}} an issue in <a href="#/{{repo}}/">{{repo}}</a></div> ' . 
                      '<div data-ip-recent-activity-item-title href="#/{{repo}}/{{item.issue.number}}">{{item.issue.title}}</div> ' .

                    '</div> ' .

                    '<div data-ng-show="!activity.">' .
                      '<p>No Recent Activity</p>' .
                    '</div>' .
                  '</div> '; 



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

        $form .= "<input class='widefat' id='$field_id' name='$field_name' type='text' value=\"$instance_var\">";

        $form .= "</p>";

      }
      echo $form;
    }
  }
  add_action('widgets_init', create_function('', 'register_widget( "ip_recent_activity" );'));
}
