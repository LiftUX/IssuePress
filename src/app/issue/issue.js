
angular.module('issue', ['AppState', 'user'])

.controller('IssueCtrl', ['$scope', '$location', '$routeParams', '$http', 'IPAppState', 'IPData', 'IPUser',
function($scope, $location, $routeParams, $http, IPAppState, IPData, IPUser) {

  var repo = $routeParams.repo;
  // Test if repo is a valid name, otherwise goto 404
  if(!IPAppState.isIPRepo(repo)) {
    $location.path('/404');
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

  $scope.fetchData = function() {
    // Set Data for this Scope from IPData service - fetch from cache, or from API otherwise
    IPData.getIssueData(repo, $routeParams.issue).then(function(data){
      $scope.issue = data.issue;
      $scope.comments = data.comments;
    });
  };

  $scope.fetchData();

  $scope.$on('issueCommentSuccess', function(){
    $scope.fetchData();
  });

  
}]);
