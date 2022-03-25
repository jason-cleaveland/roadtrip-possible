<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}




/**
* Disables Gutenberg Editor For Topics and Lessons
*
*/
function rtp_disable_gutenberg( $current_status, $post_type ) {
    $disabled_post_types = [ 'sfwd-topic', 'sfwd-lessons' ];
    if ( in_array( $post_type, $disabled_post_types, true ) ) {
        $current_status = false;
    }
    return $current_status;
}
add_filter( 'use_block_editor_for_post_type', 'rtp_disable_gutenberg', 10, 2 );




/**
* Removes the content editor for topics and lessons
*
*/
function rtp_remove_content_area() {
  remove_post_type_support( 'sfwd-topic', 'editor' );
  remove_post_type_support( 'sfwd-lessons', 'editor' );
}
add_action('init', 'rtp_remove_content_area');




/**
* Creates the materials metabox for the editor screen 
* for both lessons and topics
*/

function rtp_lms_metabox( $meta_boxes ) {
  $prefix = 'rtp_lms_';
	$meta_boxes[] = [
		'title'           => 'Content',
		'id'              => 'rtp-lms-content',
    'post_types'      => ['sfwd-topic', 'sfwd-lessons'],
    'fields' =>  [
      [
        'id'    => $prefix.'video',
        'name' => esc_html__( 'Video Link' ),
        'tooltip' =>  esc_html__( 'Paste a link to a video. Must be a valid URL like this: https://google.com.' ),
        'type'  => 'oembed',
      ],
      [
        'id'    => $prefix.'time',
        'name' => esc_html__( 'Length of Video' ),
        'tooltip' =>  esc_html__( 'How long is this video?' ),
        'type'  => 'time',
        'js_options' => [
            'controlType'     => 'slider',
            'showButtonPanel' => true,
            'timeFormat' => 'm:ss',
        ],
      ],
      [
        'type' => 'divider',
      ],
      [
        'id' => $prefix.'materials',
        'name' => esc_html__( 'Materials' ),
        'tooltip' =>  esc_html__( 'Select the materials associated with this module.' ),
        'type'        => 'post',
        'post_type'   => 'rtp_material',
        'field_type'  => 'select_advanced',
        'placeholder' => 'Select a material',
        'query_args'  => [
          'post_status'    => 'publish',
          'posts_per_page' => - 1,
        ],
        'clone' => true,
        'sort_clone' => true,
      ],
    ],
    ];
	return $meta_boxes;
};

add_filter( 'rwmb_meta_boxes', 'rtp_lms_metabox');