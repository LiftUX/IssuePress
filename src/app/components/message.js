
angular.module('components.message', []).directive('ipMessage', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title'
    },
    template: 
      '<section class="message">' +
        '<div class="message-title">{{title}}</div>' +
        '<div class="message-content" data-ng-transclude></div>' + 
      '</section>'
  };
});
