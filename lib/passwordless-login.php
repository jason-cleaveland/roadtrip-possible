<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/**
 * Shortcode for the passwordless login form
 *
 *
 */
function rtp_front_end_login(){
	ob_start();
	$account = ( isset( $_POST['user_email_username']) ) ? $account = sanitize_text_field( $_POST['user_email_username'] ) : false;
	$nonce = ( isset( $_POST['nonce']) ) ? $nonce = sanitize_key( $_POST['nonce'] ) : false;
	$error_token = ( isset( $_GET['rtp_error_token']) ) ? $error_token = sanitize_key( $_GET['rtp_error_token'] ) : false;
	$adminapp_error = ( isset( $_GET['rtp_adminapp_error']) ) ? sanitize_key( $_GET['rtp_adminapp_error'] ) : false;

	$sent_link = rtp_send_link($account, $nonce);
  // we have a user and a link was sent
	if( $account && !is_wp_error($sent_link) ){
		echo '<p class="rtp-box rtp-success">Go ahead and check your email. You\'ll receive your login link momentarily.</p>';
  // a user is logged in
  } elseif ( is_user_logged_in() ) {
		$rtp_current_user = wp_get_current_user();
    $display_name = $rtp_current_user->display_name;
    $logout = wp_logout_url( home_url('/log-in/') );
		echo '<p class="rtp-box rtp-alert">You are currently logged in as '.$display_name;
    echo ' (<a href="'.$logout.'" title="Log out of this account">Logout</a>)</p>';
	// something went wrong
  } else {
		if ( is_wp_error($sent_link) ){  
			echo '<p class="rtp-box rtp-error">' . apply_filters( 'rtp_error', $sent_link->get_error_message() ) . '</p>';
		}
    // token expired
		if( $error_token ) {
			echo '<p class="rtp-box rtp-error">' . apply_filters( 'rtp_invalid_token_error', 'Your token has probably expired. They only last for about 10 minutes. Give it another shot' ) . '</p>';
		}
    // admin approval compatibility
    if( $adminapp_error ) {
        echo '<p class="rtp-box rtp-error">' . apply_filters( 'rtp_admin_approval_error', 'Your account needs to be approved by an admin before you can log-in.' ) . '</p>';
    }

		?>
	<form name="rtploginform" id="rtploginform" action="" method="post">
		<p>
      <label type="hidden" for="user_email_username">Type your email below and we'll shoot you an email with a link to login. No Password required!</label>
			<input type="text" name="user_email_username" id="user_email_username" placeholder="Email Address" class="input" value="<?php echo esc_attr( $account ); ?>" size="25" />
			<input type="submit" name="rtp-submit" id="rtp-submit" class="rtp-primary-btn" value="Log In" />
		</p>
		<?php do_action('rtp_login_form'); ?>
		<?php wp_nonce_field( 'rtp_passwordless_login_request', 'nonce', false ) ?>

	</form>
<?php
	}

	$output = ob_get_contents();
	ob_end_clean();
	return $output;
}
add_shortcode( 'passwordless-login', 'rtp_front_end_login' );




/**
 * Checks to see if an account is valid. Either email or username
 *
 *
 */
function rtp_valid_account( $account ){
	if( is_email( $account ) ) {
		$account = sanitize_email( $account );
	} else {
		$account = sanitize_user( $account );
	}

	if( is_email( $account ) && email_exists( $account ) ) {
		return $account;
	}

	if( ! is_email( $account ) && username_exists( $account ) ) {
		$user = get_user_by( 'login', $account );
		if( $user ) {
			return $user->data->user_email;
		}
	}

	return new WP_Error( 'invalid_account', 'The email you provided does not exist. Please try again.' );
}




/**
 * Sends an email with the unique login link.
 *
 *
 */
function rtp_send_link( $email_account = false, $nonce = false ){
	if ( $email_account  == false ){
		return false;
	}
	$valid_email = rtp_valid_account( $email_account  );
	$errors = new WP_Error;
	if (is_wp_error($valid_email)){
		$errors->add('invalid_account', $valid_email->get_error_message());
	} else{
		$blog_name = get_bloginfo( 'name' );
		$blog_name = esc_attr( $blog_name );
    $user = get_user_by( 'email', $valid_email );
    $f_name = isset($user->first_name) && !empty($user->first_name) ? '&nbsp;'.$user->first_name : '';

		//Filters to change the content type of the email
		add_filter('wp_mail_content_type', function(){ return "text/html"; } );

		$unique_url = rtp_generate_url( $valid_email , $nonce );
		$subject = 'Login at Roadtrip Possible';
		$message = 'Hi there'.$f_name.',<br><br>You can go ahead and login by clicking the link below.<br><br>';
    $message .= '<a href="'.esc_url($unique_url).'" target="_blank">'.esc_url($unique_url).'</a><br><br>';
    $message .= 'But, you\'ll have to hurry, this link is only active for about 10 minutes.';
    $message .= 'After that you\'ll need to get a new link the same way you got this one.<br><br>';
    $message .= 'If you have any problems, you can just reply to this email and we\'ll get you all taken care of.<br><br>';
    $message .= 'So, let\'s go!<br><br>';
    $message .= 'Jana and Jason';
		$sent_mail = wp_mail( $valid_email, $subject, $message );

		if ( !$sent_mail ){
			$errors->add('email_not_sent', 'There was a problem sending your email. Please try again or shoot us an email at hello@roadtrippossible.com.');
		}
	}
	$error_codes = $errors->get_error_codes();

	if (empty( $error_codes  )){
		return false;
	}else{
		return $errors;
	}
}




/**
 * Generates unique URL based on UID and nonce
 *
 *
 */
function rtp_generate_url( $email = false, $nonce = false ){
	if ( $email  == false ){
		return false;
	}
	// get user id
	$user = get_user_by( 'email', $email );
	$token = rtp_create_onetime_token( 'rtp_'.$user->ID, $user->ID  );

	$arr_params = array( 'rtp_error_token', 'uid', 'token', 'nonce' );
	$url = remove_query_arg( $arr_params, rtp_current_page_url() );

  $url_params = array('uid' => $user->ID, 'token' => $token, 'nonce' => $nonce);
  $url = add_query_arg($url_params, $url);

	return $url;
}




/**
 * Automatically logs in a user with the correct nonce
 *
 * @since v.1.0
 *
 * @return string
 */
function rtp_autologin_via_url(){
	if( isset( $_GET['token'] ) && isset( $_GET['uid'] ) && isset( $_GET['nonce'] ) ){
		$uid = sanitize_key( $_GET['uid'] );
		$token  =  sanitize_key( $_REQUEST['token'] );
		$nonce  = sanitize_key( $_REQUEST['nonce'] );

		$hash_meta = get_user_meta( $uid, 'rtp_' . $uid, true);
		$hash_meta_expiration = get_user_meta( $uid, 'rtp_' . $uid . '_expiration', true);
		$arr_params = array( 'uid', 'token', 'nonce' );
		$rtp_current_page_urlrent_page_url = remove_query_arg( $arr_params, rtp_current_page_url() );

		require_once( ABSPATH . 'wp-includes/class-phpass.php');
		$wp_hasher = new PasswordHash(8, TRUE);
		$time = time();

		$wppb_generalSettings = get_option('wppb_general_settings', 'not_found');//profile builder settings are required for admin approval compatibility

		if ( ! $wp_hasher->CheckPassword($token . $hash_meta_expiration, $hash_meta) || $hash_meta_expiration < $time || ! wp_verify_nonce( $nonce, 'rtp_passwordless_login_request' ) ){
			wp_redirect( $rtp_current_page_urlrent_page_url . '?rtp_error_token=true' );
			exit;
		}else if ( defined('PROFILE_BUILDER_VERSION') && $wppb_generalSettings != 'not_found' && !empty( $wppb_generalSettings['adminApproval'] ) && $wppb_generalSettings['adminApproval'] == 'yes' && wp_get_object_terms( $uid, 'user_status' ) ){//admin approval compatibility
            wp_redirect( $rtp_current_page_urlrent_page_url . '?rtp_adminapp_error=true' );
            exit;
        }
		else {
			wp_set_auth_cookie( $uid );
			delete_user_meta($uid, 'rtp_' . $uid );
			delete_user_meta($uid, 'rtp_' . $uid . '_expiration');

			$total_logins = get_option( 'rtp_total_logins', 0);
			update_option( 'rtp_total_logins', $total_logins + 1);

			wp_safe_redirect( home_url() );
			exit;
		}
	}
}

add_action( 'init', 'rtp_autologin_via_url' );




/**
 * Create a nonce like token that you only use once based on transients
 *
 *
 */
function rtp_create_onetime_token( $action = -1, $user_id = 0 ) {
	$time = time();

	// random salt
	$key = wp_generate_password( 20, false );

	require_once( ABSPATH . 'wp-includes/class-phpass.php');
	$wp_hasher = new PasswordHash(8, TRUE);
	$string = $key . $action . $time;

	// we're sending this to the user
	$token  = wp_hash( $string );
	$expiration = apply_filters('rtp_change_link_expiration', $time + 60*10);
	$expiration_action = $action . '_expiration';

	// we're storing a combination of token and expiration
	$stored_hash = $wp_hasher->HashPassword( $token . $expiration );
  
  // adjust the lifetime of the token. currently 10 min.
	update_user_meta( $user_id, $action , $stored_hash );
	update_user_meta( $user_id, $expiration_action , $expiration );
	return $token;
}

/**
 * Returns the URL for the current page
 *
 *
 */
function rtp_current_page_url() {
    $req_uri = $_SERVER['REQUEST_URI'];

    $home_path = trim( parse_url( home_url(), PHP_URL_PATH ), '/' );
    $home_path_regex = sprintf( '|^%s|i', preg_quote( $home_path, '|' ) );

    // Trim path info from the end and the leading home path from the front.
    $req_uri = ltrim($req_uri, '/');
    $req_uri = preg_replace( $home_path_regex, '', $req_uri );
    $req_uri = trim(home_url(), '/') . '/' . ltrim( $req_uri, '/' );

    return $req_uri;
}
