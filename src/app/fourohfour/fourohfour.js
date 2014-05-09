angular.module('fourohfour', [])

.controller('FourOhFourCtrl', ['$scope', '$location', '$timeout', function($scope, $location, $timeout) {
  
  $timeout(function(){
    $location.path('/dashboard');
  }, 6000);

}]);
