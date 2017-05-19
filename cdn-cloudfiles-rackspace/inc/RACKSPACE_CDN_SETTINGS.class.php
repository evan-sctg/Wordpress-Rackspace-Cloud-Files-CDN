<?php

/**
* RACKSPACE_CDN_SETTINGS
*
* @since 0.0.1
*/

class RACKSPACE_CDN_SETTINGS
{


	/**
	* register settings
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	public static function register_settings()
	{
		register_setting(
			'RACKSPACE_CDN',
			'RACKSPACE_CDN',
			array(
				__CLASS__,
				'validate_settings'
			)
		);
	}


	/**
	* validation of settings
	*
	* @since   0.0.1
	* @change  1.0.3
	*
	* @param   array  $data  array with form data
	* @return  array         array with validated values
	*/

	public static function validate_settings($data)
	{
		return array(
			'url'		=> esc_url($data['url']),
			'username'		=> esc_attr($data['username']),
			'apikey'		=> esc_attr($data['apikey']),
			'container'		=> esc_attr($data['container']),
			'dirs'		=> esc_attr($data['dirs']),
			'excludes'	=> esc_attr($data['excludes']),
			'relative'	=> (int)($data['relative']),
			'https'		=> (int)($data['https'])
		);
	}


	/**
	* add settings page
	*
	* @since   0.0.1
	* @change  0.0.1
	*/

	public static function add_settings_page()
	{
		$page = add_options_page(
			'Rackspace CDN',
			'Rackspace CDN',
			'manage_options',
			'RACKSPACE_CDN',
			array(
				__CLASS__,
				'settings_page'
			)
		);
	}


	/**
	* settings page
	*
	* @since   0.0.1
	* @change  1.0.3
	*
	* @return  void
	*/

	public static function settings_page()
	{ ?>
		<div class="wrap">
			<h2>
				<?php _e("Rackspace CDN Settings", "cdn-cloudfiles-rackspace"); ?>
			</h2>

			<form method="post" action="options.php">
				<?php settings_fields('RACKSPACE_CDN') ?>

				<?php $options = RACKSPACE_CDN::get_options() ?>

				<table class="form-table">

					<tr valign="top">
						<th scope="row">
							<?php _e("CDN URL", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_url">
									<input type="text" name="RACKSPACE_CDN[url]" id="RACKSPACE_CDN_url" value="<?php echo $options['url']; ?>" size="64" class="regular-text code" />
								</label>

								<p class="description">
									<?php _e("CDN URL", "cdn-cloudfiles-rackspace"); ?>
								</p>
							</fieldset>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e("Rackspace CDN Username", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_username">
									<input type="text" name="RACKSPACE_CDN[username]" id="RACKSPACE_CDN_username" value="<?php echo $options['username']; ?>" size="64" class="regular-text code" />
								</label>

								<p class="description">
									<?php _e("Rackspace CDN Username", "cdn-cloudfiles-rackspace"); ?>
								</p>
							</fieldset>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row">
							<?php _e("Rackspace CDN API Key", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_apikey">
									<input type="text" name="RACKSPACE_CDN[apikey]" id="RACKSPACE_CDN_apikey" value="<?php echo $options['apikey']; ?>" size="64" class="regular-text code" />
								</label>

								<p class="description">
									<?php _e("Rackspace CDN API Key", "cdn-cloudfiles-rackspace"); ?>
								</p>
							</fieldset>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row">
							<?php _e("Rackspace CDN Container", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_container">
									<input type="text" name="RACKSPACE_CDN[container]" id="RACKSPACE_CDN_container" value="<?php echo $options['container']; ?>" size="64" class="regular-text code" />
								</label>

								<p class="description">
									<?php _e("Rackspace CDN Container", "cdn-cloudfiles-rackspace"); ?>
								</p>
							</fieldset>
						</td>
					</tr>


					<tr valign="top">
						<th scope="row">
							<?php _e("Included Directories", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_dirs">
									<input type="text" name="RACKSPACE_CDN[dirs]" id="RACKSPACE_CDN_dirs" value="<?php echo $options['dirs']; ?>" size="64" class="regular-text code" />
									<?php _e("Default: <code>wp-content/uploads</code>", "cdn-cloudfiles-rackspace"); ?>
								</label>

								<p class="description">
									<?php _e("Directories to point to the CDN ( Comma separated )", "cdn-cloudfiles-rackspace"); ?>
								</p>
							</fieldset>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e("Exclude from CDN", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_excludes">
									<input type="text" name="RACKSPACE_CDN[excludes]" id="RACKSPACE_CDN_excludes" value="<?php echo $options['excludes']; ?>" size="64" class="regular-text code" />
									<?php _e("Default: <code>.php</code>", "cdn-cloudfiles-rackspace"); ?>
								</label>

								<p class="description">
									<?php _e("Dont Point to CDN ( Comma seperated )", "cdn-cloudfiles-rackspace"); ?>
								</p>
							</fieldset>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e("Rewrite Local URLs", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_localurls">
									<input type="checkbox" name="RACKSPACE_CDN[localurls]" id="RACKSPACE_CDN_localurls" value="1" <?php checked(1, $options['localurls']) ?> />
									<?php _e("Rewrite local URLs", "cdn-cloudfiles-rackspace"); ?>
								</label>
							</fieldset>
						</td>
					</tr>

					<tr valign="top">
						<th scope="row">
							<?php _e("Rewrite HTTPS URLs", "cdn-cloudfiles-rackspace"); ?>
						</th>
						<td>
							<fieldset>
								<label for="RACKSPACE_CDN_https">
									<input type="checkbox" name="RACKSPACE_CDN[https]" id="RACKSPACE_CDN_https" value="1" <?php checked(1, $options['https']) ?> />
									<?php _e("Rewrite HTTPS URLs", "cdn-cloudfiles-rackspace"); ?>
								</label>
							</fieldset>
						</td>
					</tr>
				</table>

				<?php submit_button() ?>
			</form>
		</div><?php
	}
}
