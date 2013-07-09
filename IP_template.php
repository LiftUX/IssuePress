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
var IP_repos = <?php echo UP_IssuePress::get_IP_repo_json(); ?>

var IP_root = "<?php echo UP_IssuePress::get_IP_root(); ?>"

IP.config(function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/dashboard', {
      templateUrl: '<?php echo UP_IssuePress::get_IP_path() . '/app/dashboard/dashboard.tpl.html'; ?>'
    })
    .when('/sections', {
      templateUrl: '<?php echo UP_IssuePress::get_IP_path() . '/app/sections/sections.tpl.html'; ?>'
    })
    .when('/:repo', {
      templateUrl: '<?php echo UP_IssuePress::get_IP_path() . '/app/repo/repo.tpl.html'; ?>'
    })
    .when('/:repo/:issue', {
      templateUrl: '<?php echo UP_IssuePress::get_IP_path() . '/app/issue/issue.tpl.html'; ?>'
    })
    .when('/:repo/:issue/new', {
      templateUrl: '<?php echo UP_IssuePress::get_IP_path() . '/app/create-issue/create-issue.tpl.html'; ?>'
    })
    .otherwise({
      redirectTo: "/dashboard"
    });

});

</script>

<div class="content">
  <ng-include src="'<?php echo UP_IssuePress::get_IP_path() . '/app/header/header.tpl.html'; ?>'">
  </ng-include>

  <div class="left-column" data-ng-view>
  </div>

  <div class="right-column">

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

    <section class="tickets-following">
      <div class="section-title">
        Tickets I'm Following
      </div>
      <a href="" class="ticket">
        <span class="ticket-title">Issue with the Music Player for the homepage on Garage Band</span>
        <span class="ticket-meta">imbradmiller said an hour ago</span>
        <span class="ticket-comment">This is exactly the issue I'm having and I fixed it by</span>
      </a>
      <a href="" class="ticket">
        <span class="ticket-title">Issue with the Music Player for the homepage on Garage Band</span>
        <span class="ticket-meta">imbradmiller said an hour ago</span>
        <span class="ticket-comment">This is exactly the issue I'm having and I fixed it by</span>
      </a>
      <a href="" class="ticket">
        <span class="ticket-title">Issue with the Music Player for the homepage on Garage Band</span>
        <span class="ticket-meta">imbradmiller said an hour ago</span>
        <span class="ticket-comment">This is exactly the issue I'm having and I fixed it by</span>
      </a>
    </section>

  </div>

</div>

</body>
</html>
