
angular.module('create-issue', [])

.controller('CreateIssueCtrl', ['$scope', '$location', '$routeParams', function($scope, $location, $routeParams) {
  
  console.log($location.path());

  console.log($routeParams);
}]);
