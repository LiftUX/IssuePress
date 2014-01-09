
angular.module('components.issueThread', ['AppState', 'user', 'ui.markdown'])


.directive('ipIssueComment', ['marked', function(marked) {
  return {
    restrict: 'A',
    scope: {
      'title': '@ctitle',
      'body': '@body',
      'author': '@author',
      'action': '@action',
      'timeago': '@timeago',
      'meta': '@meta',
      'follow': '@follow',
      'labels': '@labels',
      'gravatarHash': '@gravatarHash',
    },
    controller: ['$scope', '$element', '$attrs', 'IPAppState', function($scope, $element, $attrs, IPAppState) {
      if($scope.author !== IPAppState.IP_Auth_user)
         $scope.staff = true;
    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-comment.tpl.html'
  };
}])

.directive('ipIssueForm', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {},
    controller: ['$scope', function($scope) {
      $scope.user = $scope.$parent.user;
      $scope.login_link = $scope.$parent.login_link;
    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-form.tpl.html'
  };
});
