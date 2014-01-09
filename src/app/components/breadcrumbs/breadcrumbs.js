
angular.module('components.breadcrumbs', ['ui.breadcrumbs']).directive('ipBreadcrumbs', function(breadcrumbs) {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/components/breadcrumbs/breadcrumbs.tpl.html',
    controller: function($scope, breadcrumbs) {
      $scope.breadcrumbs = breadcrumbs.getAll();
    }
  };
});

