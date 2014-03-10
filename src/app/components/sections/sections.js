
angular.module('components.sections', [
  'AppState',
])

.directive('ipSections', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/sections/sections.tpl.html',
    controller: function($scope, IPAppState) {

      if(IPAppState.repos !== 'undefined') {
        $scope.sections = IPAppState.repos;
      }
    }
  };
});
