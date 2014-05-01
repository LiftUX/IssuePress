
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
      if($scope.author !== IPAppState.Auth_user)
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
    controller: ['$scope', '$location', '$routeParams', '$timeout', 'IPAPI', 'IPUser', function($scope, $location, $routeParams, $timeout, IPAPI, IPUser) {

      
      var repo = $routeParams.repo;
      var issue = $routeParams.issue;

      $scope.comment = {};
      $scope.comment.meta = IPUser.user;
      $scope.user = IPUser.user;
      $scope.loginLink = IPUser.login_link + encodeURIComponent("#" + $location.$$url );

      $scope.submitForm = function(){

        if( $scope.commentForm.$valid ) {

          $scope.formSubmitted = true;
          IPAPI.issueComment(repo, issue, $scope.comment).then(function(result){

            if(result) {
              $scope.$emit('issueCommentSuccess');
              $scope.resetForm();
            } 

          });

        }

      };

      $scope.resetForm = function() {

        $scope.comment = {};
        $scope.commentForm.$setPristine();

        $timeout(function() {
          $scope.formSubmitted = false;
        }, 1500);
      };

      $scope.hasUser = function(){
        if(IPUser.user)
          return true;
        else
          return false;
      };

    }],
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-form.tpl.html'
  };
});
