

angular.module('components.ticketList', [])

.directive('ipTicketList', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
    },
    templateUrl: IP_PATH + '/app/components/ticket-list/ticket-list.tpl.html'
  }
})

.directive('ipTicketListItem', function() {
  return {
    restrict: 'A',
    replace: true,
    transclude: true,
    scope: {
      'title': '@title',
      'meta': '@meta',
      'comment': '@comment',
      'href': '@href'
    },
    templateUrl: IP_PATH + '/app/components/ticket-list/ticket-list-item.tpl.html'
  }
});
