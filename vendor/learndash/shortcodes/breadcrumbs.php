<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
* Shortcode for step/framework navigation
*
*/
function rtp_breadcrumbs_shortcode() {
  d($something);
  // if beaver builder is active
  if ( isset( $_GET['fl_builder'] ) ) {
    $notification = '<h3>This is the breadcrumbs section</h3>';
    return $notification;
  }
  
  $sep = '&nbsp; >>  &nbsp;';
  $home = '<a href="https://roadtrippossible.com/the-course">Course</a>';
  $step = null;
  $framework = null;
  //   get the current post
  $current_post = get_post();
  $current_post_id = $current_post->ID;
  //   get the current post type
  $current_post_type = $current_post->post_type;
  $current_post_label = $current_post_type == 'sfwd-lessons' ? 'step' : 'framework';
  $current_post_link = 'https://roadtrippossible.com/'.$current_post_label.'/'.$current_post->post_name.'/';
  $current_post_title = $current_post->post_title;
  //   if the current post is a step (lesson) assign the variable to the step and null out the framwork/topic variable
  if( $current_post_type == 'sfwd-lessons') {    
    $step = '<a href="'.$current_post_link.'">'.$current_post_title.'</a>';
    
  } elseif( $current_post_type == 'sfwd-topic') { 
    $parent_post_array = get_post_meta( $current_post_id, 'lesson_id' );
    $parent_step_id = reset($parent_post_array);
    $parent_step_link = get_permalink( $parent_step_id );
    $parent_step_title = get_the_title( $parent_step_id );
    $step = '<a href="'.$parent_step_link.'">'.$parent_step_title.'</a>';
    $framework = '<a href="'.$current_post_link.'">'.$current_post_title.'</a>';
  }
  //   if the current post is a framework (topic) 
  //   get the parent step (lesson) and assign that variable - get_post_meta lesson_id -> get_permalink( lesson_id )
  //   then Assign the framework (topic) variable
  if( is_null( $framework ) ) {
    $breadcrumbs = $home.$sep.$step;
  } else {
    $breadcrumbs = $home.$sep.$step.$sep.$framework;
  }
  return $breadcrumbs;
}

// add_shortcode( 'rtp_breadcrumbs', 'rtp_breadcrumbs_shortcode' );