<?php global $post; ?>

<div class="ip-support-meta">
	<span>Section: <?php  echo get_ip_the_sections( ', ', $post->ID ) ; ?> </span>
	<span>Status: <?php echo get_support_request_status( $post->ID ); ?></span>
</div>


