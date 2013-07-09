
angular.module('header', [])

.controller('HeaderCtrl', ['$scope', '$location', '$route',
  function ($scope, $location, $route) {
  $scope.location = $location;

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.location.$$url;
  };
}]);
