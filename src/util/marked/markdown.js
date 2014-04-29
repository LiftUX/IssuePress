angular.module('ui.markdown', [])

.factory('marked', ['$window', function($window) {

  if($window.marked)
    return $window.marked;

}])

.directive('markdown', ['$timeout', '$interpolate', 'marked', function($timeout, $interpolate, marked){
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
//      'body': '@body',
    },
    link: function(scope, element, attrs) {
      scope.rawText = element.text();

      scope.$watch(function(){
        return element.text();
      }, function(newVal, oldVal) {
        if(newVal && newVal !== scope.rawText) {
          element.html(marked(element.text()));
        }
      });
    },
    template: '<div class="rendered-markdown" data-ng-transclude></div>'
  };
}]);


