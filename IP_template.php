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
var IP_repos = <?php echo UP_IssuePress::get_IP_repo_json(); ?>;

var IP_root = "<?php echo UP_IssuePress::get_IP_root(); ?>";

var IP_PATH = "<?php echo UP_IssuePress::get_IP_path(); ?>";
</script>

<div class="content">
  <div ip-header></div>

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

</body>
</html>
