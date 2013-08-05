
angular.module('sections', [])

.controller('SectionsCtrl', ['$scope', '$location', function($scope, $location) {
  
  $scope.sections = IP_repos;

}]);
