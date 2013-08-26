angular.module('AppState', [])

.factory('IPAppState', ['$location', function($location){

  console.log($location.path());
  console.log($location.url());

  var appState = {
    breadcrumbs:
    [
      {
        href: '#/dashboard',
        title: 'Dashboard'
      },
      {
        href: '#/sections',
        title: 'Sections'
      },
      {
        href: '#/Kawa',
        title: 'Kawa'
      }
    ]
  };

  return appState;
}]);
