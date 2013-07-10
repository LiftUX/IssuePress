
angular.module('components.breadcrumbs', []).directive('ipBreadcrumbs', function() {
  return {
    restrict: 'A',
    replace: true,
    template: 
      '<section class="breadcrumb">' +
        '<a href="">Support</a>' +
        '<a href="">Garage Band</a>' +
        '<a href="">Music Player won\'t work</a>' + 
      '</section>'
  }
});

