<?php
/**
 * Registration of the Admin dashboard website url widget
 */
function calc_add_dashboard_widgets() {
    // wp_add_dashboard_widget( 'calc_system_confige_website_url',  __('System Configuration (Website URLs)', 'calc-system-configuration'), 'calc_system_confige_website_url' );
    add_meta_box( 'calc_system_confige_website_url', esc_html__( 'System Configuration (Website URLs)', 'calc-system-configuration' ), 'calc_system_confige_website_url', 'dashboard', 'side', 'high' );
    add_meta_box( 'calc_system_confige_user_url', esc_html__( 'System Configuration (User URLs)', 'calc-system-configuration' ), 'calc_system_confige_user_url', 'dashboard', 'side', 'high' );
}

// hook to register the Admin dashboard widget
add_action( 'wp_dashboard_setup', 'calc_add_dashboard_widgets' );

/**
 * Output the html content of the admin dashboard website url widget
 */
function calc_system_confige_website_url() {
  // detect the current user to get his phone number
  $user = wp_get_current_user();
  ?>
  <form id="calc_form" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" >
    <!-- controlls on which function the post will send -->
    <input type="hidden" name="calc_action" id="calc_action" value="calc_website_url_data">
    <?php wp_nonce_field( 'calc_nonce', 'calc_nonce_field' ); ?>
    <div class="input-text-wrap" id="home-wrap">
      <label for="home">Home Url:</label>
      <input type="text" name="home" id="home" value="<?php echo esc_attr(get_option('home')); ?>" class="regular-text">
    </div>
    <div class="input-text-wrap" id="siteurl-wrap">
      <label for="siteurl">Site Url:</label>
      <input type="text" name="siteurl" id="siteurl" value="<?php echo esc_attr(get_option('siteurl')); ?>" class="regular-text">
    </div>
    <div class="input-text-wrap" id="api-wrap">
      <label for="api_url">Api Url:</label>
      <input type="text" name="api_url" id="api_url" value="<?php echo esc_attr(get_option('api_url')); ?>" class="regular-text">
    </div>
    <p>
      <input name="save-data" id="save-data" class="button button-primary" value="Save Settings" type="submit"> 
      <a id="message" style="margin-left: 10px;"></a>
      <br class="clear">
    </p>
  </form>
  <a href="<?php echo esc_url( admin_url( 'admin.php?page=system-configuration' ) ); ?>">System Configuration</a>
<?php
}

/**
 * Output the html content of the admin dashboard user url widget
 */
function calc_system_confige_user_url() {
  // detect the current user to get his phone number
  $user = wp_get_current_user();
  ?>
  <form id="calc_user_url" action="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" method="post" >
    <!-- controlls on which function the post will send -->
    <input type="hidden" name="calc_user_url_action" id="calc_user_url_action" value="calc_user_url_data">
    <?php wp_nonce_field( 'calc_nonce', 'calc_nonce_field' ); ?>
    <div class="input-text-wrap" id="login-wrap">
      <label for="login">Login:</label>
      <input type="text" name="login" id="login" value="<?php echo esc_attr(get_option('login')); ?>" class="regular-text">
    </div>
    <div class="input-text-wrap" id="signup-wrap">
      <label for="signup">Signup:</label>
      <input type="text" name="signup" id="signup" value="<?php echo esc_attr(get_option('signup')); ?>" class="regular-text">
    </div>
    <p>
      <input name="save-data" id="save-data" class="button button-primary" value="Save Settings" type="submit"> 
      <a id="msg_u_u" style="margin-left: 10px;"></a>
      <br class="clear">
    </p>
  </form>
  <a href="<?php echo esc_url( admin_url( 'admin.php?page=system-configuration' ) ); ?>">System Configuration</a>
<?php
}

/**
 * Saves the data from the admin dashboard website url widget
 */
function calc_save_website_url_data() {
  $msg = '';
  if(array_key_exists('nonce', $_POST) AND wp_verify_nonce( $_POST['nonce'], 'calc_nonce' ) ) 
  {
    // Update theme opation
    update_option( 'home',    $_POST['home'] );
    update_option( 'siteurl', $_POST['siteurl'] );
    update_option( 'api_url', $_POST['api_url'] );
    // Success message
    $msg = 'Settings Saved';
  }
  else
  {
    // Error message
    $msg = 'Settings unsaved';
  }
  wp_send_json( $msg );
}

/**
 * ajax hook for registered users
 */
 add_action( 'wp_ajax_calc_website_url_data', 'calc_save_website_url_data' );


/**
 * Saves the data from the admin dashboard user url widget
 */
function calc_save_user_url_data() {
  $msg = '';
  if(array_key_exists('nonce', $_POST) AND wp_verify_nonce( $_POST['nonce'], 'calc_nonce' ) ) 
  {
    // Update theme opation
    update_option( 'login',    $_POST['login'] );
    update_option( 'signup', $_POST['signup'] );
    // Success message
    $msg = 'Settings Saved';
  }
  else
  {
    // Error message
    $msg = 'Settings unsaved';
  }
  wp_send_json( $msg );
}

/**
 * ajax hook for registered users
 */
 add_action( 'wp_ajax_calc_user_url_data', 'calc_save_user_url_data' );

