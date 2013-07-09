
angular.module('issue', [])

.controller('IssueCtrl', ['$scope', '$location', '$routeParams', function($scope, $location, $routeParams) {
  $scope.params = $routeParams;
  
}]);
