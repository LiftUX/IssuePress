
angular.module('components.issueThread', [])


.directive('ipIssueComment', function() {
  return {
    restrict: 'A',
    replace: false,
    transclude: true,
    scope: {
      'title': '@title',
      'author': '@author',
      'authorEmail': '@authorEmail',
      'staff': '@staff',
      'action': '@action',
      'timeago': '@timeago',
      'meta': '@meta',
      'follow': '@follow',
      'tags': '@tags',
    },
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-comment.tpl.html'
  }
})

.directive('ipIssueForm', function() {
  return {
    restrict: 'A',
    replace: true,
    scope: {
      'user': '@user',
    },
    templateUrl: IP_PATH + '/app/components/issue-thread/issue-form.tpl.html'
  }
})
