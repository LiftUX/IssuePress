'use strict';

angular.module('ui.gravatar', ['md5'])

.factory('gravatar', ['md5', function(md5){

  var gravatarService = {};

  gravatarService.getEmailHash = function(email) {
    return md5.createHash(email.toLowerCase());
  }

  return gravatarService;

}])

.directive('gravatarImage', function () {
  return {
    restrict:"A",
    replace: true,
    scope: {
      secure: '@secure',
      gravatarHash: '@gravatarHash',
      s: '@s',
      r: '@r',
      d: '@d'
    },
    template: '<img ng-show="src" src="{{src}}" />',
    controller: ['$scope', '$element', '$attrs', function($scope, $element, $attrs){

      if(typeof $scope.gravatarHash !== 'undefined')
        var emailHash = $scope.gravatarHash;
      else
        var emailHash = '00000000000000000000000000000000';

      var protocol = $scope.secure ? 'https://secure' : 'http://';
      var src = protocol + 'gravatar.com/avatar/' + emailHash + '?d=mm&';

      if(typeof $scope.s !== 'undefined') 
        src += 's=' + $scope.s + '&';

      if(typeof $scope.r !== 'undefined')  
        src += 'r=' + $scope.r + '&';

      if(typeof $scope.d !== 'undefined')
        src += 'd=' + encodeURIComponent($scope.d);

      $scope.src = src;
    }],

  };
});
