<?php
/**
* Error Modal
*/
?>
<div class="modal-backdrop" data-modal="post-edit-error" data-modal-backdrop></div>
<div class="modal-content small" data-modal="post-edit-error">
	<div class="modal-content-body">
		<h3><?php _e('The address could not be found at this time.', 'wpsimplelocator'); ?></h3>
	</div>
	<div class="modal-content-footer">
		<a href="#" class="wpsl-cancel-trash button" data-dismiss="modal"><?php _e('Cancel', 'wpsimplelocator'); ?></a>
		<a href="#" class="wpsl-address-confirm button-primary" data-simple-locator-confirm-no-address><?php _e('Save without location', 'wpsimplelocator'); ?></a>
	</div>
</div><!-- .modal-content -->