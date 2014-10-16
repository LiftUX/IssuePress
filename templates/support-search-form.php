<?php
global $IssuePress;

?>

<form method="get" id="ip-search-form" class="ip-form" action="<?php echo home_url( '/' ); ?>">
	<label class="screen-reader-text hidden ip-search-form-query-label ip-form-label" for="ip-search-form-query">Search Support For:</label>
	<input id="ip-search-form-query" name="s"  placeholder="<?php esc_attr_e( "Search Support...", $IssuePress->get_plugin_name() ); ?>" type="text">	
	<?php ip_search_process_fields(); ?>
	<input type="submit" id="ip-search-form-submit" class="button submit" value="<?php esc_attr_e( 'Search Support', $IssuePress->get_plugin_name() ); ?>">
</form>
