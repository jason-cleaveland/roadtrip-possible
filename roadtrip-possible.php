<?php
/**
 * Main plugin header.
 *
 * Plugin Name: Roadtrip Possible
 * Description: All Roadtrip Possible functions and api integrations
 * Version:     1.0.0
 * Plugin URI:  https://roadtrippossible.com
 * Author:      Meadowhawk Development
 * Author URI:  https://meadowhawkdevelopment.com
 */

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}





/**
* Define the constants here
*
*
*/
$plugin_data = get_file_data( __FILE__, ['Version' => 'Version'], false );
$plugin_version = $plugin_data['Version'];
define('RTP_VERSION', $plugin_version);
define( 'RTP_DIR', plugin_dir_path( __FILE__ ) );
define( 'RTP_URL', plugin_dir_url( __FILE__ ) );
define( 'VBOUT_KEY', ['api_key' => '5217613224305806103283355'] );





/**
 * Make sure the required plugins are activated
 * 
 * 
 */
function rtp_required_plugins() {
  $required_plugins = [
    'Latepoint' => 'latepoint/latepoint.php',
    'MetaBox' => 'meta-box/metabox.php',
    'Learndash' =>'sfwd-lms/sfwd_lms.php',
    'Forminator' => 'forminator/forminator.php'
  ];
  if( is_admin() && current_user_can( 'activate_plugins' ) ) {
    foreach( $required_plugins as $k => $v ) {

      if ( !is_plugin_active( $v ) ) {
        // the error if those plugins aren't active
        $notice = '<div class="error"><p>Sorry, but Roadtrip Possible Plugin requires '.$k.' to be installed and active.</p></div>';
        // fire the error
        add_action('admin_notices', function() use ( $notice ) {echo $notice; } );
        // deactivate Roadtrip Possible
        deactivate_plugins( plugin_basename( __FILE__ ) ); 
        if ( isset( $_GET['activate'] ) ) {
            unset( $_GET['activate'] );
        }
      }
    }
  }
}

add_action( 'admin_init', 'rtp_required_plugins' );




/**
* Required files
*
*
*/
require_once RTP_DIR.'admin/admin-functions.php';
require_once RTP_DIR.'admin/error-logging.php';

require_once RTP_DIR.'lib/cpt-calendar.php';
require_once RTP_DIR.'lib/cpt-faq.php';
require_once RTP_DIR.'lib/cpt-landing.php';
require_once RTP_DIR.'lib/cpt-training.php';
require_once RTP_DIR.'lib/cpt-next-steps.php';
require_once RTP_DIR.'lib/passwordless-login.php';
require_once RTP_DIR.'lib/redirect.php';

require_once RTP_DIR.'vendor/vbout/required-files.php';
require_once RTP_DIR.'vendor/vbout/rtp-vbout-interface.php';
require_once RTP_DIR.'vendor/latepoint/latepoint-customizations.php';
require_once RTP_DIR.'vendor/learndash/admin.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/breadcrumbs.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/mark-complete.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/materials-archive.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/step-framework-materials.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/step-framework-navigation.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/resume-course.php';
require_once RTP_DIR.'vendor/learndash/shortcodes/video-display.php';

require_once RTP_DIR.'vendor/forminator/form-processing.php';
// require_once RTP_DIR.'vendor/forminator/hidden-field.php';

require_once RTP_DIR.'public/header.php';
require_once RTP_DIR.'public/footer.php';
require_once RTP_DIR.'public/menu.php';

require_once RTP_DIR.'test.php';





/**
 * Load Assets.
 *
 */
function custom_enqueue_files() {
	// if this is not the front page, abort.
	// if ( ! is_front_page() ) {
	// 	return;
	// }

	// loads a CSS file in the head.
	// wp_enqueue_style( 'highlightjs-css', RTP_URL . 'assets/css/style.css' );
  
  // loads a js file in the head.
	wp_enqueue_script( 'fa', RTP_URL . 'assets/js/fontawesome.min.js' );
  wp_enqueue_script( 'fa-solid', RTP_URL . 'assets/js/solid.min.js' );

	/**
	 * loads JS files in the footer.
	 */
	wp_enqueue_script( 'magnific-popup', RTP_URL . 'assets/js/magnific-popup.min.js', [], '', true );

  
  // Remove Latepoint Styles
  wp_dequeue_style( 'latepoint-main-front' );
  // Remove Latepoint Scripts
  wp_dequeue_script( 'latepoint-main-front' );
  wp_dequeue_script( 'sprintf' );
  wp_dequeue_script( 'jquery-mask' );
  wp_dequeue_script( 'atepoint-main-front-js-extra' );
  // Remove Default LearnDash Styles
  wp_dequeue_style( 'learndash_quiz_front_css' );
  wp_dequeue_style( 'jquery-dropdown-css' );
  wp_dequeue_style( 'learndash_lesson_video' );
  wp_dequeue_style( 'learndash-front' );
  wp_dequeue_style( 'learndash-front-inline' );
  // Remove Default LearnDash Scripts
  wp_dequeue_script( 'learndash-front-js-extra' );
  wp_dequeue_script( 'learndash-front-js' );

  // Check if we're on a calendar page and re-enqueue the latepoint scripts and styles
  if( is_singular( 'rtp_calendar_page' ) ) {
    // Enqueue Latepoint Styles
    wp_enqueue_style( 'latepoint-main-front' );
    // Enqueue Latepoint Scripts
    wp_enqueue_script( 'latepoint-main-front' );
    wp_enqueue_script( 'sprintf' );
    wp_enqueue_script( 'jquery-mask' );
    wp_enqueue_script( 'atepoint-main-front-js-extra' );
  }
  // check if we're on a learndash page or page that need learndash styles/scripts
  $learndash_cpt = [
    'sfwd-courses', 
    'sfwd-lessons', 
    'sfwd-topic', 
    'sfwd-quiz', 
    'sfwd-assignment' 
  ];
  //insert any pages_ids containing learndash components here
  $learndash_pages = [
    2921,
  ];
	if( is_singular( $learndash_cpt ) || is_page( $learndash_pages ) 
  ) {
		// Add Styles Back In
    wp_enqueue_style( 'learndash_quiz_front_css' );
    wp_enqueue_style( 'jquery-dropdown-css' );
    wp_enqueue_style( 'learndash_lesson_video' );
    wp_enqueue_style( 'learndash-front' );
    wp_enqueue_style( 'learndash-front-inline' );
    // add Scripts back in
    wp_enqueue_script( 'learndash-front-js-extra' );
    wp_enqueue_script( 'learndash-front-js' );
  };
}

add_action( 'wp_enqueue_scripts', 'custom_enqueue_files', 9999 );






// Calls the function that sends the password reset confirmation to the user
if ( ! function_exists( 'wp_password_change_notification' ) ) {
  function wp_password_change_notification( $user ) {
    rtp_password_change_notification( $user );
  }
}