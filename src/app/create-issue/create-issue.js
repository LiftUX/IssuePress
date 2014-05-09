
angular.module('create-issue', ['AppState'])

.controller('CreateIssueCtrl', ['$scope', '$location', '$routeParams', 'IPAPI', 'IPUser', 'IPAppState', function($scope, $location, $routeParams, IPAPI, IPUser, IPAppState) {
  
  var repo = $routeParams.repo;
  
  // Test if repo is a valid name, otherwise goto 404
  if(!IPAppState.isIPRepo(repo)) {
    $location.path('/404');
  }

  $scope.issue = {};
  $scope.issue.meta = IPUser.user;
  $scope.loginLink = IPUser.login_link + encodeURIComponent("#" + $location.$$url );

  $scope.submitForm = function(){

    if( $scope.issueForm.$valid ) {

      IPAPI.issueNew(repo, $scope.issue).then(function(result){
        if(result) {
          $scope.issueLink = repo + "/" + result.data.response.number;
          $scope.formSubmitted = true;
        }
      });

    }

  };

  $scope.hasUser = function(){
    if(IPUser.user)
      return true;
    else
      return false;
  };

}]);
