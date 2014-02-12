
angular.module('header', ['user'])

.directive('ipHeader', function() {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/header/header.tpl.html',
  };
})

.controller('HeaderCtrl', ['$rootScope', '$scope', '$location', 'IPUser',
function ($rootScope, $scope, $location, IPUser) {

  $rootScope.$on('$routeChangeSuccess', function(scope, current) {
    $scope.loc = $location.$$url;
    $scope.login_link = IPUser.login_link + encodeURIComponent("#" + $scope.loc);
  });

  $scope.loc = $location.$$url;
  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link + encodeURIComponent("#" + $scope.loc);
  $scope.logout_link = IPUser.logout_link;

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.loc;
  };
}]);
