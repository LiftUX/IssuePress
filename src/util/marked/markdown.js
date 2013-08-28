angular.module('ui.markdown', [])

.factory('marked', ['$window', function($window) {

  if($window.marked)
    return $window.marked;

}])

.directive('markdown', ['marked', function(marked) {
  return {
    restrict: 'EACM',
    replace: true,
    scope: {},
    transclude: true,
    template: '<div class="rendered-markdown">{{content}}</div>',
    controller: ['$scope', '$element', '$attrs', '$transclude', 'marked', function($scope, $element, $attrs, $transclude, marked) {
      marked($transclude, function(err, content){
        if(err) throw err;
        $scope.content = content;
      });
    }],
  }
}]);
