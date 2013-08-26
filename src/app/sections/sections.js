
angular.module('sections', [])

.controller('SectionsCtrl', ['$scope', '$location', function($scope, $location) {
  
  if(IP_repos !== 'undefined')
    $scope.sections = IP_repos;

}]);
