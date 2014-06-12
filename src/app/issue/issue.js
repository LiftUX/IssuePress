
angular.module('issue', ['AppState', 'user'])

.controller('IssueCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', 'IPData', 'IPUser',
function($scope, $location, $routeParams, $http, IPAppState, IPData, IPUser) {

  var repo = $routeParams.repo;

  $scope.fetchData = function() {
    // Set Data for this Scope from IPData service - fetch from cache, or from API otherwise
    IPData.getIssueData(repo, $routeParams.issue).then(function(data){

      if(typeof data !== 'undefined') {
        $scope.issue = data.issue;
        $scope.comments = data.comments;
      } else {
        $scope.apiError = true;
      }

    });
  };

  if(!IPAppState.isIPRepo(repo)) {
    $location.path('/404');
  } else {
    $scope.fetchData();
  }

  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link;
  $scope.logout_link = IPUser.logout_link;

  $scope.isStaff = function(login) {
    if(login !== IPAppState.Auth_user)
      return true;
    else
      return false;
  };

  $scope.issue = {};
  $scope.comments = [];


  $scope.$on('issueCommentSuccess', function(){
    $scope.fetchData();
  });

}]);
