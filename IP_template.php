<?php
/*
Template Name: IssuePress
*/
?>
<!doctype html>
<html data-ng-app="issuepress">
<head>
  <title>IssuePress</title>
  <meta name="viewport" content="width=device-width,initial-scale=1">

  <script>
    var IP_PATH = "<?php echo UP_IssuePress::get_IP_path(); ?>";

    var IP_Vars = {};
    IP_Vars.API_PATH = "<?php echo UP_IssuePress::get_IP_API_path(); ?>";
    IP_Vars.repos = <?php echo UP_IssuePress::get_IP_repo_json(); ?>;
    IP_Vars.data = <?php echo UP_IssuePress::get_IP_data(); ?>;
    IP_Vars.root = "<?php echo UP_IssuePress::get_IP_root(); ?>";
    IP_Vars.user = <?php echo UP_IssuePress::get_IP_user(); ?>;
    IP_Vars.login = "<?php echo UP_IssuePress::get_IP_login(); ?>";
    IP_Vars.logout = "<?php echo UP_IssuePress::get_IP_logout(); ?>";
    IP_Vars.Auth_user = <?php echo UP_IssuePress::get_IP_auth_user(); ?>;
  </script>

  <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

<?php echo UP_IssuePress::get_IP_sidebars(); ?>
<?php do_action('ip_head'); ?>

</head>
<body>


<div class="content">
  <div ip-header></div>

  <div class="left-column" data-ng-view>
  </div>

  <div class="right-column" data-ng-switch="" on="sidebar">

    <div data-ng-switch-when="dashboard.tpl.html">
      <div data-ng-include=" 'ip-dashboard-side' "></div>
    </div>

    <div data-ng-switch-when="sections.tpl.html">
      <div ng-include=" 'ip-sections-side' "></div>
    </div>

    <div data-ng-switch-when="repo.tpl.html">
      <div ng-include=" 'ip-section-side' "></div>
    </div>

    <div data-ng-switch-when="issue.tpl.html">
      <div ng-include=" 'ip-issue-side' "></div>
    </div>

    <!-- Note: no create issue declaration, it will not have a sidebar. -->

    <div data-ng-switch-default></div>

  </div>

</div>

</body>
</html>
