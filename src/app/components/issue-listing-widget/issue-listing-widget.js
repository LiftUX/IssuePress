
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
    controller: ['$scope', '$element', '$attrs', '$routeParams',  '$filter', 'IPAppState', function($scope, $element, $attrs, $routeParams, $filter, IPAppState) {
      
      $scope.repo = $routeParams.repo;
      $scope.filteredItems = [];

      $scope.filterBy = 'all';

      $scope.setFilter = function(state) {
        $scope.filterBy = state;
      };

      $scope.$watch("filterBy", function(nVal, oVal){
        if(nVal !== oVal) {
          console.log("Updating filter");
          $scope.filteredItems = $filter('filter')($scope.items, $scope.checkFilter);
        }
      });

      $scope.checkFilter = function(i) {

        if($scope.filterBy === 'all' || $scope.filterBy === i.state) {
          return true;
        }

        return false;

      };

      $scope.isLoading = true;

      $scope.$watch("items", function(nVal, oVal) {
        if(nVal) {
          $scope.isLoading = false;
          console.log(nVal);
          $scope.filteredItems = nVal;
        }
      });

    }],
    templateUrl: IP_PATH + '/app/components/issue-listing-widget/issue-listing-widget.tpl.html',
  };
});
