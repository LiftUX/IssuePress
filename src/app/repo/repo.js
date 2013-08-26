
angular.module('repo', [])

.controller('RepoCtrl', ['$scope', '$location', '$routeParams', function($scope, $location, $routeParams) {
  
  console.log($location.path());

  console.log($routeParams);
}]);
