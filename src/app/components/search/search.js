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
      $scope.searchComplete = $scope.hasResults = false;
      $scope.results = [];

      var target = 'all';
      if($scope.repo) {
        target = $scope.repo;
      }

      var timeout;

      $scope.$watch('q', function(nVal, oVal){

        $scope.searchComplete = $scope.hasResults = false;

        if(nVal.length < 3) {
          return;
        }

        $scope.isSearching = true;

        if(nVal !== oVal) {
          if(timeout) $timeout.cancel(timeout);
          
          timeout = $timeout(function(){
            $scope.executeSearch();
          }, 600);

        }

      });

      $scope.executeSearch = function(){

        IPAPI.search({q: $scope.q, repo: target}).then(function(data){
          if(data){

            $scope.searchComplete = true;
            $scope.isSearching = false;

            if(typeof data.data.response.items !== 'undefined') { 
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
