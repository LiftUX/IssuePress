'use strict';

angular.module('ui.gravatar', ['md5']).
    directive('gravatarImage', ['md5', function (md5) {
        return {
            restrict:"A",
            replace: true,
            scope: {
              secure: '@secure',
              email: '@email',
              s: '@s',
              r: '@r',
              d: '@d'
            },
            template: '<img src="{{src}}" />',
            controller: ['$scope', '$element', '$attrs', function($scope, $element, $attrs){

              if(typeof $scope.email !== 'undefined')
                var emailHash = md5.createHash($scope.email.toLowerCase());
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
    }]);
