<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;


/**
* Changed these to functions from shortcodes to make it easier to use in oxygen builder
*
*/


/**
* Creates the previous Link for the shortcode
*
*/
function rtp_prev_navigation_link_shortcode( $atts ) {
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification = 'prev link will be here';
    return $notification;
  }  
  $course_id = 1742;
  $current_post_id = get_the_id();
//   use this in the case of a shortcode
//   $current_post_id = (int)$atts['post_id'];
  $data = rtp_create_prev_next_array( $course_id, $current_post_id );
  return $data['prev']['link'];  
}

// add_shortcode( 'rtp_prev_link', 'rtp_prev_navigation_link_shortcode' );



/**
* Creates the previous title for the shortcode
*
*/
function rtp_prev_navigation_title_shortcode( $atts ) {
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification = 'prev title will be here';
    return $notification;
  }  
  $course_id = 1742;
  $current_post_id = get_the_id();
//   use this in the case of a shortcode
//   $current_post_id = (int)$atts['post_id'];
  $data = rtp_create_prev_next_array( $course_id, $current_post_id );
  $title_raw = $data['prev']['title'];
  $title = str_replace('Framework ','',$title_raw);
  return $title; 
}

// add_shortcode( 'rtp_prev_title', 'rtp_prev_navigation_title_shortcode' );



/**
* Creates the next link for the shortcode
*
*/
function rtp_next_navigation_link_shortcode( $atts ) {
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification = 'next link will be here';
    return $notification;
  }  
  $course_id = 1742;
  $current_post_id = get_the_id();
//   use this in the case of a shortcode
//   $current_post_id = (int)$atts['post_id'];
  $data = rtp_create_prev_next_array( $course_id, $current_post_id );
  return $data['next']['link'];  
}

// add_shortcode( 'rtp_next_link', 'rtp_next_navigation_link_shortcode' );



/**
* Creates the next title for the shortcode
*
*/
function rtp_next_navigation_title_shortcode( $atts ) {
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification = 'next title will be here';
    return $notification;
  }  
  $course_id = 1742;
  $current_post_id = get_the_id();
//   use this in the case of a shortcode
//   $current_post_id = (int)$atts['post_id'];
  $data = rtp_create_prev_next_array( $course_id, $current_post_id );
  $title_raw = $data['next']['title'];
  $title = str_replace('Framework ','',$title_raw);
  return $title;  
}

// add_shortcode( 'rtp_next_title', 'rtp_next_navigation_title_shortcode' );



/**
* Creates the link array for the shortcode
*
*/
function rtp_create_prev_next_array( $course_id, $current_step_id ) {
  
  $all_step_ids = LDLMS_Factory_Post::course_steps($course_id)->get_steps();
  $course_order = course_order_array($all_step_ids);
  [$prev, $next] = next_and_prev($course_order, $current_step_id);
  
  if( !isset($next) || empty($next) ) {
    $next_item = [
      'type' => 'Course',
      'title' => 'Home',
      'link' => 'https://roadtrippossible.com/the-course',
    ];
  } else {
    $next_post_obj = get_post($next);
    $next_item = [
      'type' => ($next_post_obj->post_type == 'sfwd-lessons' ? 'Step' : 'Framework'),
      'title' => $next_post_obj->post_title,
      'link' => get_permalink($next),
    ];
  }

  if( !isset($prev) || empty($prev) ) {
    $prev_item = [
      'type' => 'Course',
      'title' => 'Home',
      'link' => 'https://roadtrippossible.com/the-course',
    ];
  } else {
    $prev_post_obj = get_post($prev);
    $prev_item = [
      'type' => ($prev_post_obj->post_type == 'sfwd-lessons' ? 'Step' : 'Framework'),
      'title' => $prev_post_obj->post_title,
      'link' => get_permalink($prev),
    ];
  }

  $data = [
    'prev' => $prev_item,
    'next' => $next_item
  ];
  return $data;
}


/**
* Creates the course order array
*
*/
function course_order_array($array) {
   $return = [];
   foreach ($array['sfwd-lessons'] as $key => $value) {
     $return[] = $key;
     foreach( $value['sfwd-topic'] as $k => $v ) {
       $return[] = $k;
     }
   }
   return $return;

}



/**
* gets the ids of the next and previous posts
*
*/
function next_and_prev( $array, $search ) {
  $prev2  = null;
  $prev1  = null;
  $current = null;
  foreach( $array as $key => $value ) {
    [$prev2, $prev1, $current] = [$prev1, $current, $value];
    if( $prev1 === $search ) {
      return [$prev2, $current];
    }
  }
  if( $current === $search ) {
   return [$prev1, null];
  }
  return [null, null];
}