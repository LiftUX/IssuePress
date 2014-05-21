
angular.module('header', [
  'user',
  'AppState',
])

.directive('ipHeader', function() {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/header/header.tpl.html',
  };
})

.controller('HeaderCtrl', ['$rootScope', '$scope', '$location', '$route', 'IPUser', 'IPAppState', function ($rootScope, $scope, $location, $route, IPUser, IPAppState) {

  $rootScope.$on('$routeChangeSuccess', function(scope, current) {
    $scope.loc = $location.$$url;
    $scope.login_link = IPUser.login_link + encodeURIComponent("#" + $scope.loc);

    if($route.current.params.repo) {
      $scope.repo = $route.current.params.repo;
    } else {
      $scope.repo = '';
    }
  });

  $scope.loc = $location.$$url;
  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link + encodeURIComponent("#" + $scope.loc);
  $scope.logout_link = IPUser.logout_link;
  $scope.page_title = IPAppState.root.post_title;
  $scope.home = IPAppState.site;

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.loc;
  };
}]);
