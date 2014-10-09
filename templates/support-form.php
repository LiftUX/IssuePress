<?php 
global $IssuePress;

if( is_user_logged_in() ) : ?>

	<section id="ip-create-request-form-wrapper">
		<form method="post" id="ip-create-request-form" class="ip-form" action="<?php echo get_permalink(); ?>">
			<label class="ip-support-title-label ip-form-label" for="ip-support-title">Support Request Title:</label>
			<input class="ip-support-request-title ip-form-input" name="ip-support-title" placeholder="Add a descriptive, but brief title" type="text">	
			<?php echo get_ip_form_section_select(); ?>
			<label id="ip-form-description-label ip-form-label" for="ip-support-description">Support Request Description:</label>
			<textarea id="ip-support-request-description" name="ip-support-description" placeholder="<?php echo get_ip_form_placeholder(); ?>"></textarea>
			<?php ip_new_request_process_fields(); ?>
			<button type="submit" id="ip-form-submit" name="ip-support-submit" class="button submit"><?php _e( 'Create Support Request', $IssuePress->get_plugin_name() ); ?></button>
		</form>
	</section>

<?php else : ?>

	<section id="ip-create-request" class="ip-form no-user">
		<h4>You must be logged in to create a support request</h4>
		<p><a href="<?php echo wp_login_url( get_permalink() ); ?>">Log in now.</a></p>
	</section>

<?php endif; ?>



