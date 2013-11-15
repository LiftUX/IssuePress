
angular.module('components.search', []).directive('ipSearch', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
    },
    templateUrl: IP_PATH + '/app/components/search/search.tpl.html',
//    controller: function($scope, ipData) {
//      $scope.sections = ipData.sections.getAll();
//    }
  }
});
