
angular.module('components.issueThread', ['AppState', 'user'])


.directive('ipIssueComment', function() {
  return {
    restrict: 'A',
    replace: false,
    transclude: true,
    scope: {
      'title': '@title',
      'author': '@author',
      'gravatarHash': '@gravatarHash',
      'action': '@action',
      'timeago': '@timeago',
      'meta': '@meta',
      'follow': '@follow',
      'tags': '@tags',
    },
    controller: ['$scope', 'IPAppState', function($scope, IPAppState) {
      if($scope.author == IPAppState.IP_Auth_user)
         $scope.staff = true;
    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-comment.tpl.html'
  }
})

.directive('ipIssueForm', function(IPUser) {
  return {
    restrict: 'A',
    replace: true,
    scope: {},
    controller: ['$scope', 'IPUser', function($scope, IPUser) {
      $scope.user = IPUser.user;
    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-form.tpl.html'
  }
})
