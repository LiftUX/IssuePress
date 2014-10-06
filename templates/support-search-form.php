<?php
global $IssuePress;

?>

<form method="post" id="ip-search-form" class="ip-form" action="<?php get_permalink(); ?>">
	<label for="ip-search-form-query">Search Support Requests:</label>
	<input id="ip-search-form-query" name="ip-search-form-query"  placeholder="Search Query" type="text">	
	<?php ip_search_process_fields(); ?>
	<button type="submit" id="ip-search-form-submit" name="ip-search-form-submit" class="button submit"><?php _e( 'Search Support Requests', $IssuePress->get_plugin_name() ); ?></button>
</form>
