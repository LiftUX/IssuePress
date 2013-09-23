var IP = angular.module('issuepress', [
  'header',
  'dashboard',
  'sections',
  'repo',
  'issue',
  'create-issue',
  'components.message',
  'components.sections',
  'components.breadcrumbs',
  'components.recentActivity',
  'components.ticketList',
  'components.issueThread',
  'ui.gravatar',
  'ui.timeago',
  'ui.markdown',
]);


IP.config(function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/dashboard', {
      templateUrl: IP_PATH + '/app/dashboard/dashboard.tpl.html',
    })
    .when('/sections', {
      templateUrl: IP_PATH + '/app/sections/sections.tpl.html',
    })
    .when('/:repo', {
      templateUrl: IP_PATH + '/app/repo/repo.tpl.html',
    })
    .when('/:repo/new', {
      templateUrl: IP_PATH + '/app/create-issue/create-issue.tpl.html',
    })
    .when('/:repo/:issue', {
      templateUrl: IP_PATH + '/app/issue/issue.tpl.html',
    })
    .otherwise({
      redirectTo: "/dashboard"
    });

});

IP.run(function($rootScope, $templateCache) {
//  $rootScope.$on('$viewContentLoaded', function() {
//    $templateCache.removeAll();
//  });

  $rootScope.$on('$routeChangeSuccess', function(scope, current, pre) {
    console.log(current.loadedTemplateUrl);
    var tpl = current.loadedTemplateUrl.split("/");
    var tplPart = tpl[tpl.length - 1];
    $rootScope.sidebar = tplPart;
  });
});

