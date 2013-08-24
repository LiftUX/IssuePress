
angular.module('header', [])

.directive('ipHeader', function() {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/header/header.tpl.html',
  }
})

.controller('HeaderCtrl', ['$scope', '$location',
function ($scope, $location) {
  $scope.loc = $location.$$url;

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.loc;
  };
}]);
