<?php

global $IssuePress;
$sections = get_ip_sections(); 

if ( !empty( $sections ) ) : ?>

<ul id="support-sections" class="support-section-list">

	<?php	foreach ( $sections as $section ) : ?>

<li id="support-section-<?php echo $section->term_id; ?>" class="support-section">

	<div class="support-section-title">
		<h3><a href="<?php echo ip_section_permalink($section); ?>"><?php echo $section->name; ?></a></h3>
	</div>

	<div class="support-section-description">
	<?php if( !empty($section->description) ) : ?>
		<p><?php echo $section->description; ?></p>
	<?php endif; ?>
	</div>

	<div class="entry-meta">
		<span class="support-section-open-count support-section-count"><?php echo ip_get_section_open_count($section); ?> <?php _e( "Open", $IssuePress->get_plugin_name() ); ?></span>
		<span class="support-section-total-count support-section-count"><?php echo ip_get_section_total_count($section); ?> <?php _e( "Total", $IssuePress->get_plugin_name() ); ?></span>
		<span class="suport-section-updated entry-date"><?php _e( "Updated:", $IssuePress->get_plugin_name() ); ?> <time class="entry-date" datetime="<?php echo ip_get_section_updated($section, 'c'); ?>"><?php echo ip_get_section_updated($section); ?></time></span>
	</div>

</li>

	<?php endforeach;  ?>

</ul>

<?php else: ?>


<div id="no-support-sections" class="ip-not-found">
	<h3>There are currently no Support Sections.</h2>
</div>


<?php endif; ?>

