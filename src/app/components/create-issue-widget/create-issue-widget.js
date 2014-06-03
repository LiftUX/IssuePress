
angular.module('components.createIssueWidget', ['AppState'])

.directive('ipCreateIssueWidget', [function() {
  return {
    restrict: 'A',
    scope: {
      'title': '@title',
    },
    controller: ['$scope', '$element', '$attrs', '$routeParams', 'IPAppState', function($scope, $element, $attrs, $routeParams, IPAppState) {

      $scope.repo = $routeParams.repo;

      if(IPAppState.repos !== 'undefined') {
        $scope.sections = IPAppState.repos;
      }

    }],
    templateUrl: IP_PATH + '/app/components/create-issue-widget/create-issue-widget.tpl.html'
  };
}]);
