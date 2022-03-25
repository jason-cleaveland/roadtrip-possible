<?php
// Exit if accessed directly.
defined( 'ABSPATH' ) || exit;

/**
* Shortcode for step/framework navigation
*
*/
function rtp_video_display_shortcode() {
  
//   $media = rwmb_meta( $field_id );

//   return $breadcrumbs;
}

add_shortcode( 'rtp_breadcrumbs', 'rtp_video_display_shortcode' );