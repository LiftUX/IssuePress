var IP = angular.module('issuepress', [
  'components.message'
]);

IP.controller('IPHeaderCtrl', ['$scope', '$location', '$route',
  function ($scope, $location, $route) {
  $scope.location = $location;

  $scope.home = function () {
    $location.path('/dashboard');
  };

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.location.$$url;
  };

}]);


