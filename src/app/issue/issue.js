
angular.module('issue', ['AppState', 'user', 'ui.gravatar'])

.controller('IssueCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', 'IPAPI', 'IPUser', 'gravatar',
function($scope, $location, $routeParams, $http, IPAppState, IPAPI, IPUser, gravatar) {

  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link;
  $scope.logout_link = IPUser.logout_link;

  $scope.isStaff = function(login) {
    if(login !== IPAppState.IP_Auth_user)
      return true;
    else
      return false;
  };


  $scope.issue = {};
  $scope.comments = [];

  var handleData = function(data, status, headers, config){
    if(status == 200) {
      console.log(data);
      $scope.issue = data.data.issue;
      $scope.comments = data.data.comments;
    }

    console.log($scope.issue);
  };

  IPAPI.issue($routeParams.repo, $routeParams.issue).success(handleData);
//
//  $http({
//    method: 'GET',
//    url: ipUrl + $scope.params.repo + '/' + $scope.params.issue
//  }).success(function(data, status, headers, config){
//    if(status === 200){
//      var issueData = data.data; 
//      $scope.issue = issueData.issue;
//      $scope.comments = issueData.comments;
//    }
//    console.log("Success");
//    console.log(data);
////    console.log($scope.issue.body);
//  }).error(function(data, status, headers, config){
//    console.log("Fail");
//    console.log(data);
//    console.log(status);
//  });
  
}]);
