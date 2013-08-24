angular.module('AppState', [])

.factory('IPAppState', function(){

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
});
