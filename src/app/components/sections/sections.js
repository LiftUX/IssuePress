angular.module('components.sections', []).directive('ipSections', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/sections/sections.tpl.html',
//    controller: function($scope, ipData) {
//      $scope.sections = ipData.sections.getAll();
//    }
  }
});
