<?php
global $IssuePress;

?>

<form method="post" id="ip-search-form" class="ip-form" action="<?php get_permalink(); ?>">
	<label class="screen-reader-text hidden ip-search-form-query-label ip-form-label" for="ip-search-form-query">Search Support For:</label>
	<input id="ip-search-form-query" name="ip-search-form-query"  placeholder="<?php esc_attr_e( "Search Support...", $IssuePress->get_plugin_name() ); ?>" type="text">	
	<?php ip_search_process_fields(); ?>
	<button type="submit" id="ip-search-form-submit" name="ip-search-form-submit" class="button submit"><?php esc_attr_e( 'Search Support', $IssuePress->get_plugin_name() ); ?></button>
</form>
