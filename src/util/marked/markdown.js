angular.module('ui.markdown', [])

.factory('marked', ['$window', function($window) {

  if($window.marked)
    return $window.marked;

}])

.directive('markdown', ['$timeout', 'marked', function($timeout, marked){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
//      'body': '@body',
    },
    link: function(scope, element, attrs) {
      var timeoutID = $timeout(function() {
        element.html(marked(element.text()));
      }, 1000);
    },
    template: '<div class="rendered-markdown" data-ng-transclude></div>'
  };
}]);

