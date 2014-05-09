angular.module('fourohfour', [])

.controller('FourOhFourCtrl', ['$scope', '$location', '$timeout', function($scope, $location, $timeout) {
  
  $timeout(function(){
    console.log("set location to dashboard!");
    $location.path('/dashboard');
  }, 8000);

}]);
