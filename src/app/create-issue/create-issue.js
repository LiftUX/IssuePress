
angular.module('create-issue', ['AppState'])

.controller('CreateIssueCtrl', ['$scope', '$location', '$routeParams', 'IPAPI', 'IPUser', function($scope, $location, $routeParams, IPAPI, IPUser) {
  
  var repo = $routeParams.repo;
  
  $scope.issue = {};
  $scope.issue.meta = IPUser.user;
  $scope.loginLink = IPUser.login_link + encodeURIComponent("#" + $location.$$url );

  $scope.submitForm = function(){

    if( $scope.issueForm.$valid ) {

      IPAPI.issueNew(repo, $scope.issue).then(function(result){
        if(result) {

          $scope.issueLink = repo + "/" + result.data.data.response.number;
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
