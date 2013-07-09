var IP = angular.module('issuepress', [
  'components.message',
  'components.breadcrumbs'
]);

IP.config(function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/:repo/issue/new', {
    })
    .when('/:repo/:issue/', {
    })
    .when('/:repo/', {
    })
    .when('/sections', {
    })
    .when('/dashboard', {
    })
    .otherwise({
      redirectTo: "/dashboard"
    });

//    $locationProvider.html5Mode(true);
});

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


