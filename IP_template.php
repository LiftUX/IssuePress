<?php
/*
Template Name: IssuePress
*/
?>
<?php get_header(); ?>

<!--
<?php global $wp; ; echo "<pre>". var_export($wp, true) . " </pre>\n"; ?>

-->

<script>
var IP_repos = <?php echo UP_IssuePress::get_IP_repo_json(); ?>

var IP_root = "<?php echo UP_IssuePress::get_IP_root(); ?>"

</script>

<h1><?php the_title(); ?></h1>

<div id="issuepress-content">

<ul id="repo-list">
</ul>

</div>


<?php get_footer(); ?>
