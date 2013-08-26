angular.module('ui.breadcrumbs', [])
.factory('breadcrumbs', ['$rootScope', '$location', function($rootScope, $location){

  // Dashboard bc item
  var dashboard = {
    href: '/dashboard',
    title: 'Dashboard'
  };

  // Sections bc item
  var sections = {
    href: '/sections',
    title: 'Sections'
  };

  // Build BC items based on path
  var setBreadcrumbs = function(){
    var path = $location.path();
    bcArray = [];

    if(path === '/dashboard') {
      bcArray = [dashboard];
    } else if(path === '/sections') {
      bcArray = [dashboard,sections];
    } else {
      bcArray = [dashboard,sections, {
        href: path,
        title: path.substr(1, path.length)
      }];
    }

    return bcArray;
  }

  // Init with 
  var breadcrumbs = setBreadcrumbs();
  var breadcrumbsService = {};
  
  //we want to update breadcrumbs only when a route is actually changed
  //as $location.path() will get updated imediatelly (even if route change fails!)
  $rootScope.$on('$routeChangeSuccess', function(event, current){

    breadcrumbs = setBreadcrumbs();

  });

  breadcrumbsService.getAll = function() {
    return breadcrumbs;
  };

  breadcrumbsService.getFirst = function() {
    return breadcrumbs[0] || {};
  };

  return breadcrumbsService;
}]);
