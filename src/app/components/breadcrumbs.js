
angular.module('components.breadcrumbs', []).directive('ipBreadcrumbs', function() {
  return {
    restrict: 'A',
    replace: true,
    template: 
      '<section class="breadcrumb">' +
        '<a href="">Sections</a>' +
        '<a href="">Garage Band</a>' +
        '<a href="">Music Player won\'t work</a>' + 
      '</section>'
  }
});

