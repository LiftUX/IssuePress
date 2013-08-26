angular.module('ui.breadcrumbs', [])
.factory('breadcrumbs', ['$rootScope', '$location', '$routeParams', function($rootScope, $location, $routeParams){

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
    } 
    
    if (typeof $routeParams.repo != 'undefined'){ 
      var repo = $routeParams.repo;
      bcArray = [dashboard,sections, {
        href: '/'+repo,
        title: $routeParams.repo
      }];
      
      // Check if it's a specific issue template
      if (typeof $routeParams.issue != 'undefined'){
        bcArray.push({
          href: path,
          title: "Issue #" + $routeParams.issue
        });

      // Check if it's a Create Issue template
      } else if(path === '/' + repo + '/issue/new') {
        bcArray.push({
          href: path,
          title: "Create Issue"
        });
      }

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
