<?php
namespace SimpleLocator\Listeners;

use SimpleLocator\Repositories\SettingsRepository;

class QuickEdit extends AJAXListenerBase
{
	/**
	* Settings Repository
	*/
	private $settings;

	public function __construct()
	{
		$this->settings = new SettingsRepository;
		$this->validateNonce();
		$this->validate();
		$this->save();
	}

	/**
	 * Validate the Nonce for security
	 */
	private function validateNonce()
	{
		if (!isset($_GET['nonce']) || !wp_verify_nonce($_GET['nonce'], 'wpsl_quick_edit_nonce')) {
			$this->error(__('Security check failed.', 'simple-locator'));
		}
	}

	/**
	 * Validate the Data (post ID)
	 */
	private function validate()
	{
		if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
			$this->error(__('Invalid post ID provided.', 'simple-locator'));
		}
	}

	/**
	 * Save the Data with strict validation and sanitization
	 */
	private function save()
	{
		$latitude_field = $this->settings->getGeoField('lat');
		$longitude_field = $this->settings->getGeoField('lng');
		$fields = $_GET;
		$post_id = intval(sanitize_text_field($_GET['id']));

		// Check user permission
		if (!current_user_can('edit_post', $post_id)) {
			$this->error(__('You do not have permission to edit this post.', 'simple-locator'));
		}

		// List of allowed meta fields
		$allowed_fields = ['wpsl_address', 'wpsl_address_two', 'wpsl_city', 'wpsl_state', 'wpsl_zip', 'wpsl_country', 'wpsl_phone', 'wpsl_custom_geo', 'wpsl_website', $latitude_field, $longitude_field];

		foreach ($fields as $key => $value) {
			if ($key == 'action' || $key == 'id' || $key == 'nonce') continue;

			$meta_key = 'wpsl_' . $key;
			if (!in_array($meta_key, $allowed_fields)) continue;

			// Special handling for custom_geo
			if ($key == 'custom_geo') {
				$value = ($value === 'true') ? 'true' : 'false';
			}

			// Sanitize based on field type
			$meta_value = $this->sanitizeField($key, $value);
			if ($meta_value === false) continue;

			update_post_meta($post_id, $meta_key, $meta_value);
		}

		$this->success(__('The location was successfully saved.', 'simple-locator'));
	}

	/**
	 * Sanitize field value based on its type
	 * @param string $key
	 * @param mixed $value
	 * @return mixed sanitized value or false if invalid
	 */
	private function sanitizeField($key, $value)
	{
		// Numeric fields (latitude, longitude)
		if (in_array($key, ['latitude', 'longitude'])) {
			return is_numeric($value) ? floatval($value) : false;
		}
		// URL fields
		if ($key == 'website') {
			return esc_url_raw($value);
		}
		// Phone number fields
		if ($key == 'phone') {
			return preg_replace('/[^0-9+\-\s]/', '', $value);
		}
		// Default: sanitize as plain text
		return sanitize_text_field($value);
	}
}