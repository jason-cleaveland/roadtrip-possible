<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}


/**
* Redirects those without primary course access to a course landing page
* Not in use for the update to Oxygen builder
*

function rtp_course_no_access_landing_page() {
  
  $user_logged_in = is_user_logged_in();
  if( !$user_logged_in ) {
    return;
  }
  
  $admin_user = current_user_can( 'manage_options' );
  $settings = get_option( 'rtp_settings' );
  $default_redirect = $settings['rtp_default_redirect'];
  $primary_course_id = $settings['rtp_primary_course'];

  // turn off deafult behavior for admin
  if( $user_logged_in && $admin_user && !$default_redirect ) {
    return;
  }
  
  // set up access data
  global $post;
  $post_id = $post->ID;
  $user_id = get_current_user_id();  
  $access_data = rtp_get_course_access_data($user_id);

  if( $post_id != $primary_course_id ) {
    return;
  }

  if( !isset($access_data) || empty($access_data) ) {
    $access_data = [];
  }
  
  // check for access
  $access_check = in_array( $post_id, $access_data );
  // if we do not have access redirect to the course landing page
  if( !$access_check ) {
    wp_safe_redirect( home_url('offer/the-course-offer-page/') );
    exit;
  } 
}

add_action('template_redirect', 'rtp_course_no_access_landing_page');



/**
* All the redirect for all the pages
*
*/
function rtp_public_pages_redirect() {
  global $post;
  $user_logged_in = is_user_logged_in();
  $admin_user = current_user_can( 'manage_options' );
  $settings = get_option( 'rtp_settings' );
  $field_id = 'rtp_default_redirect';
  $default_redirect = $settings[$field_id];
  
  // turn off deafult behavior for admin
  if( $user_logged_in && $admin_user && !$default_redirect ) {
    return;
  }
  
  // set up page arrays
  $public_pages = [
//     4, // coming-soon
//     116, // landing
//     2023, // not ready yet
//     1757, // payment
  ];
  $lms_pages = [
//     2553, // faq
//     2551, // mini-courses
//     1784, // profile
//     701, // planner
//     2076, // support
    2921, // the course
//     2409, // welcome-aboard
//     2583, // Landing page for current customers with no access to the primary course
  ];
  $taxonomies = [
    'ld_course_category',
    'ld_course_tag',
    'ld_lesson_category',
    'ld_lesson_tag',
    'ld_topic_category',
    'ld_topic_tag',
  ];
  $logged_in_archive_post_types = [
    'sfwd-courses',
    'sfwd-lessons',
    'sfwd-topic',
    'rtp_landing_page',
    'rtp_material',
  ];
  $logged_out_archive_post_types = [
    'rtp_course',
    'sfwd-courses',
    'sfwd-lessons',
    'sfwd-topic',
    'rtp_landing_page',
    'rtp_material',
  ];
  
  // Redirect to the main course page
  if( $user_logged_in &&
    (
      is_page($public_pages) or
      is_tax($taxonomies) or
      is_post_type_archive($logged_in_archive_post_types) or 
      is_singular('sfwd-courses')
    )
  ) { 
    wp_safe_redirect( home_url('/the-course/') );
    exit;
  }
   
  // redirect to the home page
  if( !$user_logged_in &&
    (
      is_page($lms_pages) or
      is_tax($taxonomies) or
      is_singular('rtp_course') or
      is_singular('rtp_material') or
      is_singular('sfwd-courses') or
      is_singular('sfwd-lessons') or
      is_singular('sfwd-topic') or
      is_post_type_archive($logged_out_archive_post_types) or
      // course materials category for posts
      is_category( 224 )
    )
  ) {
    wp_safe_redirect( home_url() );
    exit;
  }
}
add_action('template_redirect', 'rtp_public_pages_redirect');















