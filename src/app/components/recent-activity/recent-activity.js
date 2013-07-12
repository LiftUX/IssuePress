
angular.module('components.recentActivity', []).directive('ipRecentActivity', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity.tpl.html'
  }
})

.directive('ipRecentActivityItem', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
      'icon': '@icon',
      'timeago': '@timeago',
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity-item.tpl.html'
  }

});
