angular.module('components.search', ['AppState'])

.directive('ipSearch', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      "repo": '@repo',
    },
    templateUrl: IP_PATH + '/app/components/search/search.tpl.html',
    controller: ['$scope', '$element', '$attrs', '$timeout', 'IPAPI', function($scope, $element, $attrs, $timeout, IPAPI) {
      
      $scope.q = '';
      $scope.isSearching = false;
      $scope.searchComplete = $scope.hasResults = $scope.lastQ = false;
      $scope.results = [];

      var target = 'all';
      if($scope.repo) {
        target = $scope.repo;
      }

      var timeout;

      $scope.submitForm = function(){

        if( $scope.searchForm.$valid && ($scope.q !== $scope.lastQ) ) {

          $scope.isSearching = true;
          $scope.executeSearch();

        }

      };

      $scope.executeSearch = function(){

        IPAPI.search({q: $scope.q, repo: target}).then(function(data){
          if(data){

            $scope.searchComplete = true;
            $scope.isSearching = false;
            $scope.lastQ = $scope.q;

            if(typeof data.data.response.items !== 'undefined') { 
              console.log(data.data.response);
              $scope.results = data.data.response.items;
            }

          }
        });

      };

      $scope.$watch('results', function(nVal, oVal) {

        if(nVal.length === 0) {
          $scope.hasResults = false;
        } else {
          $scope.hasResults = true;
        }

      });


      $scope.getRepoFromResult = function(string) {

        var regEx = /([^\/]*)\/issues\/\d*/i;
        var matches = string.match(regEx);

        return matches[1];

      };


    }],
  };
});
