
angular.module('issue', ['AppState', 'user', 'ui.gravatar'])

.controller('IssueCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', 'IPUser', 'gravatar',
function($scope, $location, $routeParams, $http, IPAppState, IPUser, gravatar) {

  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link;
  $scope.logout_link = IPUser.logout_link;

  $scope.isStaff = function(login) {
    if(login !== IPAppState.IP_Auth_user)
      return true;
    else
      return false;
  };


  var ipUrl = IPAppState.IP_API_PATH;
  $scope.params = $routeParams;

  $http({
    method: 'GET',
    url: ipUrl + $scope.params.repo + '/' + $scope.params.issue
  }).success(function(data, status, headers, config){
    if(status === 200){
      var issueData = data.data; 
      $scope.issue = issueData.issue;
      $scope.comments = issueData.comments;
    }
    console.log("Success");
    console.log(data);
//    console.log($scope.issue.body);
  }).error(function(data, status, headers, config){
    console.log("Fail");
    console.log(data);
    console.log(status);
  });
  
}]);
