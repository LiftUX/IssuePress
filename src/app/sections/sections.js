
angular.module('sections', [
  'AppState'
])

.controller('SectionsCtrl', ['$scope', '$location', 'IPAppState', function($scope, $location, IPAppState) {
  
  if(IPAppState.repos !== 'undefined') {
    $scope.sections = IPAppState.repos;
  }

}]);
