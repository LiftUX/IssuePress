var IP = angular.module('issuepress', [
  'ngRoute',
  'header',
  'dashboard',
  'sections',
  'repo',
  'issue',
  'create-issue',
  'fourohfour',
  'components.search',
  'components.message',
  'components.sections',
  'components.release',
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
    .when('/404', {
      templateUrl: IP_PATH + '/app/fourohfour/fourohfour.tpl.html',
    })
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
      redirectTo: "/404"
    });
});

IP.run(function($rootScope, $templateCache, $location) {
  $rootScope.$on('$routeChangeSuccess', function(scope, current) {

    if(!current.loadedTemplateUrl)
      return;

    var tpl = current.loadedTemplateUrl.split("/");
    var tplPart = tpl[tpl.length - 1];
    $rootScope.sidebar = tplPart;
  });
});

