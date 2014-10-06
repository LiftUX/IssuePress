<?php

global $IssuePress;
$sections = get_ip_sections(); 

if ( !empty( $sections ) ) : ?>

<ul id="support-sections" class="support-section-list">

	<?php	foreach ( $sections as $section ) : ?>

<li id="support-section-<?php echo $section->term_id; ?>" class="support-section">

	<div class="entry-header">
		<h3><a href="<?php echo ip_section_permalink($section); ?>"><?php echo $section->name; ?></a></h3>
	</div>

	<?php if( !empty($section->description) ) : ?>
	<div class="entry-content">
		<p><?php echo $section->description; ?></p>
	</div>
	<?php endif; ?>

	<div class="entry-meta">
		<p><?php echo $section->count; ?> <?php _e( "Support Requests", $IssuePress->get_plugin_name() ); ?></p>
	</div>

</li>

	<?php endforeach;  ?>

</ul>

<?php else: ?>


<div id="no-support-sections" class="ip-not-found">
	<h3>There are currently no Support Sections.</h2>
</div>


<?php endif; ?>

