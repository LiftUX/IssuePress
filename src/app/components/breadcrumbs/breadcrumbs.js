
angular.module('components.breadcrumbs', ['AppState']).directive('ipBreadcrumbs', function(IPAppState) {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/components/breadcrumbs/breadcrumbs.tpl.html',
    controller: function($scope, IPAppState) {
      $scope.breadcrumbs = IPAppState.breadcrumbs;
    }
  }
});

