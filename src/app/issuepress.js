angular.module('issuepress', []);

angular.module('issuepress').controller('IPHeaderCtrl', ['$scope', '$location', '$route',
  function ($scope, $location, $route) {
  $scope.location = $location;

  $scope.home = function () {
    $location.path('/dashboard');
  };

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.location.$$url;
  };

}]);


