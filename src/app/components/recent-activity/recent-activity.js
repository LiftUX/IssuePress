
angular.module('components.recentActivity', [
  'AppState',
])


.directive('ipRecentActivity', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
      'items': '='
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity.tpl.html',
    controller: ['$scope', '$element', '$attrs', 'IPAppState', function($scope, $element, $attrs, IPAppState) {
      $scope.isLoading = true;

      $scope.isEventType = function(item, eventType){

        if(item.type === eventType) {
          return true;
        }

        return false;

      };

      $scope.isNonRepoEvent = function(item){

        if( $scope.isEventType(item, "IssueCommentEvent") || $scope.isEventType(item, "IssuesEvent") ) {
          return true;
        }

        return false;

      };

      $scope.itemAction = function(item) {

        if($scope.isEventType(item, "IssuesEvent")) {
          return item.payload.action + ' an issue';
        } else if ($scope.isEventType(item, "IssueCommentEvent")) {
          return 'made a comment';
        } else {
          return 'updated an issue';
        }

      };

      $scope.isIssueCommentEvent = 

      $scope.getIPRepo = function(item){

        var repo = IPAppState.getRepoName(item.repo.name);
        return repo;

      };

      $scope.getIPIssueLink = function(item){

        if(item.payload.issue) {
          var repo = IPAppState.getRepoName(item.repo.name);
          return '#/' + repo + '/' + item.payload.issue.number;
        }

        return false;

      };

      $scope.$watch("items", function(nVal, oVal) {
        if(nVal) {
          $scope.isLoading = false;
        }
      });
        
    }]
  };
})

.directive('ipRecentActivityItem', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'icon': '@icon',
      'timeago': '@timeago',
      'href': '@href',
      'item': '='
    },
    templateUrl: IP_PATH + '/app/components/recent-activity/recent-activity-item.tpl.html',
    controller: ['$scope', '$element', '$attrs', function($scope, $element, $attrs) {

    }]
  };
})


.directive('ipRecentActivityItemMeta', function(){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
    },
    template: '<div class="recent-activity-meta"><div data-ng-transclude></div></div>'
  };
});
