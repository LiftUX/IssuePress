angular.module('components.search', ['AppState'])

.directive('ipSearch', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      "repo": '@repo',
    },
    templateUrl: IP_PATH + '/app/components/search/search.tpl.html',
    controller: ['$scope', '$element', '$attrs', 'IPAPI', function($scope, $element, $attrs, IPAPI) {
      

      var target = 'all';
      if($scope.repo) {
        target = $scope.repo;
      }

      IPAPI.search({search: "My API Test Terms!"}, target).then(function(data){
        if(data){

          console.log("In Search directrive controller");
          console.log("data");
          console.log(data);
        }
      });


    }],
  };
});
