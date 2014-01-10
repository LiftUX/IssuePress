angular.module('components.release', []).directive('ipRelease', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/release/release.tpl.html',
    controller: function($scope) {
      $scope.release = {
        title: "Example Release Title",
        href: "https://github.com/LiftUX/IssuePress/releases/tag/v-0.1",
        date: "2013-08-24T12:36:13-07:00",
      };
    }
  };
});

