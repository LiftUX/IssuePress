var IP = angular.module('issuepress', [
  'header',
  'dashboard',
  'sections',
  'repo',
  'issue',
  'create-issue',
  'components.message',
  'components.breadcrumbs'
]);

// We'd typically declare IP.config here but working WP makes refereces to templateUrl tough. 
// Routes are defined in the IP_template.php file.

