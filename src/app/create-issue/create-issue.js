
angular.module('create-issue', ['AppState'])

.controller('CreateIssueCtrl', ['$scope', '$location', '$routeParams', 'IPAPI', 'IPUser', function($scope, $location, $routeParams, IPAPI, IPUser) {
  
  console.log($location.path());
  console.log($routeParams);

  var repo = $routeParams.repo;
  
  $scope.issue = {};
  $scope.issue.meta = IPUser.user;

  $scope.submitForm = function(){


    if( $scope.createIssue.$valid ) {

      IPAPI.issueNew(repo, $scope.issue).then(function(result){
        if(result) {
          console.log("In CreateIssueCtrl");
          console.log( result.data );
        }
      });

    }


  };


}]);
