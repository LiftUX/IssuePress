angular.module('ui.markdown', [])

.factory('marked', ['$window', function($window) {

  if($window.marked)
    return $window.marked;

}])

.directive('markdown', ['marked', function(marked){
  return {
      restrict: 'A',
      replace: true,
      scope: {
        'body': '@body',
      },
      link: function(scope, element, attrs) {
        scope.$watch('body', function(newVal, oldVal) {
          if(newVal !== oldVal){ 
            element.html( marked(newVal) );
          }
        }, true);
      },
      template: '<div class="rendered-markdown"></div>'
    }
}]);

