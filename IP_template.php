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
<?php do_action('ip_head'); ?>

  <!--[if lt IE 9]><script src="//html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->

</head>
<body>

<script>
  var IP_PATH = "<?php echo UP_IssuePress::get_IP_path(); ?>";

  var IP_Vars = {};
  IP_Vars.IP_API_PATH = "<?php echo UP_IssuePress::get_IP_API_path(); ?>";
  IP_Vars.IP_repos = <?php echo UP_IssuePress::get_IP_repo_json(); ?>;
  IP_Vars.IP_data = <?php echo UP_IssuePress::get_IP_data(); ?>;
  IP_Vars.IP_root = "<?php echo UP_IssuePress::get_IP_root(); ?>";
  IP_Vars.IP_user = <?php echo UP_IssuePress::get_IP_user(); ?>;
  IP_Vars.IP_login = "<?php echo UP_IssuePress::get_IP_login(); ?>";
  IP_Vars.IP_logout = "<?php echo UP_IssuePress::get_IP_logout(); ?>";
  IP_Vars.IP_Auth_user = <?php echo UP_IssuePress::get_IP_auth_user(); ?>;
</script>

<?php echo UP_IssuePress::get_IP_sidebars(); ?>

<div class="content">
  <div ip-header></div>

  <div class="left-column" data-ng-view>
  </div>

  <div class="right-column" data-ng-switch="" on="sidebar">

    <div data-ng-switch-when="http://local.wp.com/wp-content/plugins/IssuePress/src/app/dashboard/dashboard.tpl.html"
         data-ng-include=" ' ip-dashboard-side ' "></div>

    <div data-ng-switch-when="http://local.wp.com/wp-content/plugins/IssuePress/src/app/sections/sections.tpl.html" 
         ng-include=" 'ip-sections-side' "></div>

    <div data-ng-switch-when="http://local.wp.com/wp-content/plugins/IssuePress/src/app/repo/repo.tpl.html" 
         ng-include=" 'ip-section-side' "></div>

    <div data-ng-switch-when="http://local.wp.com/wp-content/plugins/IssuePress/src/app/issue/issue.tpl.html" 
         ng-include=" 'ip-issue-side' "></div>

    <!-- Note no create issue declaration, it will not have a sidebar. -->

    <div data-ng-switch-default>

      <section class="support-sections">
        <div class="section-title">
          All Support Sections
        </div>
        <div class="section-nav">
          <a href="">Most Recent</a>
          <a href="">All</a>
        </div>
        <a href="" class="support-section">
          <div class="support-section-following">Follow</div>
          <div class="support-section-title">GarageBand Theme</div>
          <div class="support-section-date">December 27th, 2012</div>
        </a>
        <a href="" class="support-section">
          <div class="support-section-following">Follow</div>
          <div class="support-section-title">GarageBand Theme</div>
          <div class="support-section-date">December 27th, 2012</div>
        </a>
        <a href="" class="support-section">
          <div class="support-section-following">Following</div>
          <div class="support-section-title">GarageBand Theme</div>
          <div class="support-section-date">December 27th, 2012</div>
        </a>
        <a href="" class="support-section">
          <div class="support-section-following">Following</div>
          <div class="support-section-title">GarageBand Theme</div>
          <div class="support-section-date">December 27th, 2012</div>
        </a>
      </section>

      <div ip-ticket-list title="Tickets I'm Following">
        <div ip-ticket-list-item
             title="Issue with the Music Player for the homepage on Garage Band"
             meta="imbradmiller said an hour ago"
             href="#test-item">This is exactly the issue I'm having and I fixed it by</div>

        <div ip-ticket-list-item
             title="Issue with the Music Player for the homepage on Garage Band"
             meta="imbradmiller said an hour ago"
             href="#test-item">This is exactly the issue I'm having and I fixed it by</div>

        <div ip-ticket-list-item
             title="Issue with the Music Player for the homepage on Garage Band"
             meta="imbradmiller said an hour ago"
             href="#test-item">This is exactly the issue I'm having and I fixed it by</div>
      </div>

    </div>

  </div>


</div>

</body>
</html>
