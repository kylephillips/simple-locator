<?php
/**
* Error Modal
*/
?>
<div class="wpsl-modal-backdrop" data-wpsl-modal="post-edit-error" data-wpsl-modal-backdrop></div>
<div class="wpsl-modal-content small" data-wpsl-modal="post-edit-error">
	<div class="wpsl-modal-content-body">
		<h3><?php _e('The address could not be found at this time.', 'wpsimplelocator'); ?></h3>
	</div>
	<div class="wpsl-modal-content-footer">
		<a href="#" class="wpsl-cancel-trash button" data-wpsl-modal-close="modal"><?php _e('Cancel', 'wpsimplelocator'); ?></a>
		<a href="#" class="wpsl-address-confirm button-primary" data-simple-locator-confirm-no-address><?php _e('Save without location', 'wpsimplelocator'); ?></a>
	</div>
</div><!-- .modal-content -->