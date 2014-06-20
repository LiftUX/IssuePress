
angular.module('components.issueListingWidget', [
  'AppState',
])


.directive('ipIssueListingWidget', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@widgetTitle',
      'items': '='
    },
    controller: ['$scope', '$element', '$attrs', '$routeParams', 'IPAppState', function($scope, $element, $attrs, $routeParams, IPAppState) {
      
      $scope.repo = $routeParams.repo;

      $scope.isLoading = true;

      $scope.$watch("items", function(nVal, oVal) {
        if(nVal) {
          $scope.isLoading = false;
          console.log(nVal);
        }
      });

    }],
    templateUrl: IP_PATH + '/app/components/issue-listing-widget/issue-listing-widget.tpl.html',
  };
});
