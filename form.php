<?php
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
    	wp_die( __('You do not have sufficient permissions to access this page.') );
    }
 
    // add error/update messages
 
    // check if the user have submitted the settings
    // WordPress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'calc_messages', 'calc_messages', __( 'Settings Saved', 'calc-system-configuration' ), 'updated' );
    }
 
    // show error/update messages
    settings_errors( 'calc_messages' );
?>

	<div class="wrap">
		<h1 class="wp-heading-inline"><?php esc_html_e( get_admin_page_title(), 'calc-system-configuration' ); ?></h1>
		<hr class="wp-header-end">
		<form method="post" id="" action="<?php echo esc_html( admin_url( 'options.php' ) ); ?>">
		<?php settings_fields( 'calc-system-configuration-group' ); ?>
		<?php do_settings_sections( 'calc-system-configuration-group' ); ?>
		<h2 class="title">Website URLs</h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="home">Home Url:</label></th>
					<td>
						<input name="home" type="text" id="home" value="<?php echo esc_attr(get_option('home')); ?>" class="regular-text">
						<p class="description">You can add link from <a href="<?php echo esc_html( admin_url( 'options-general.php' ) ); ?>">General Settings</a> too.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="siteurl">Site Url:</label></th>
					<td><input name="siteurl" type="text" id="siteurl" value="<?php echo esc_attr(get_option('siteurl')); ?>" class="regular-text"></td>
				</tr>
				<tr>
					<th scope="row"><label for="api_url">API Url:</label></th>
					<td><input name="api_url" type="text" id="api_url" value="<?php echo esc_attr(get_option('api_url')); ?>" class="regular-text"></td>
				</tr>
			</tbody>
		</table>
		<h2 class="title">User URLs</h2>
		<table class="form-table" role="presentation">
			<tbody>
				<tr>
					<th scope="row"><label for="login">Login Url:</label></th>
					<td>
						<input name="login" type="text" id="login" value="<?php echo esc_attr(get_option('login')); ?>" class="regular-text">
						<p class="description">Add Shortcode from menu in url <a href="<?php echo esc_html( admin_url( 'nav-menus.php' ) ); ?>">Menus</a> <kbd>[LOGIN]</kbd>.</p>
					</td>
				</tr>
				<tr>
					<th scope="row"><label for="signup">Signup Url:</label></th>
					<td>
						<input name="signup" type="text" id="signup" value="<?php echo esc_attr(get_option('signup')); ?>" class="regular-text">
						<p class="description">Add Shortcode from menu in url <a href="<?php echo esc_html( admin_url( 'nav-menus.php' ) ); ?>">Menus</a> <kbd>[SIGNUP]</kbd>.</p>
					</td>
				</tr>
			</tbody>
		</table>
		<div id="saved"></div>
			<?php
			//submit_button( __( 'Save Settings', 'textdomain' ), 'primary', 'wpdocs-save-settings', true, $other_attributes );
			submit_button();
			?>
		</form>
	</div>
	<div id="wp_file_manager"></div>

