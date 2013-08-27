
angular.module('components.recentActivity', [])

.directive('ipRecentActivity', function() {
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
      'icon': '@icon',
      'timeago': '@timeago',
      'href': '@href'
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity-item.tpl.html'
  }
})

.directive('ipRecentActivityItemTitle', function(){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'href': '@href',
    },
    template: '<a href="{{href}}" class="recent-activity-title"><div data-ng-transclude></div></a>'
  }
})


.directive('ipRecentActivityItemMeta', function(){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
    },
    template: '<div class="recent-activity-meta"><div data-ng-transclude></div></div>'
  }
})
