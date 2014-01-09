
angular.module('header', ['user'])

.directive('ipHeader', function() {
  return {
    restrict: 'A',
    replace: true,
    templateUrl: IP_PATH + '/app/header/header.tpl.html',
  };
})

.controller('HeaderCtrl', ['$scope', '$location', 'IPUser',
function ($scope, $location, IPUser) {
  $scope.user = IPUser.user;
  $scope.login_link = IPUser.login_link;
  $scope.logout_link = IPUser.logout_link;

  $scope.loc = $location.$$url;

  $scope.isNavbarActive = function (navBarPath) {
    return navBarPath === $scope.loc;
  };
}]);
