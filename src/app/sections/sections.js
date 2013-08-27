
angular.module('sections', ['AppState'])

.controller('SectionsCtrl', ['$scope', '$location', 'IPAppState', function($scope, $location, IPAppState) {
  
  if(IPAppState.IP_repos !== 'undefined')
    $scope.sections = IPAppState.IP_repos;

}]);
