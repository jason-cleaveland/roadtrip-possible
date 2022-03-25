<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}




/**
* Set up the video select field
*
*/
function rtp_next_steps_page_fields( $meta_boxes ) {
    $prefix = 'rtp_next_steps_';

    $meta_boxes[] = [
      'title'      => __( 'Calendar Settings', 'rtp' ),
      'post_types' => ['rtp_next_steps_page'],
      //         'context'    => 'after_title',
      //         'style'      => 'seamless',
      'fields'     => [
        [
          'name'       => __( 'Presto Video', 'rtp' ),
          'id'         => $prefix . 'presto_video',
          'type'       => 'post',
          'post_type'  => ['pp_video_block'],
          'field_type' => 'select_advanced',
          'placeholder' => 'Select a Video',
          'desc'        => 'Which video should be displayed on this page?',
          'required'   => true,
        ],
      ],
    ];

    return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'rtp_next_steps_page_fields' );





/**
* Registers the next steps page post type
*
*/
function rtp_next_steps_page_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Next Steps Pages', 'rtp' ),
		'singular_name'            => esc_html__( 'Next Steps Page', 'rtp' ),
		'add_new'                  => esc_html__( 'Add New', 'rtp' ),
		'add_new_item'             => esc_html__( 'Add new next steps page', 'rtp' ),
		'edit_item'                => esc_html__( 'Edit Next Steps Page', 'rtp' ),
		'new_item'                 => esc_html__( 'New Next Steps Page', 'rtp' ),
		'view_item'                => esc_html__( 'View Next Steps Page', 'rtp' ),
		'view_items'               => esc_html__( 'View Next Steps Pages', 'rtp' ),
		'search_items'             => esc_html__( 'Search Next Steps Pages', 'rtp' ),
		'not_found'                => esc_html__( 'No next steps pages found', 'rtp' ),
		'not_found_in_trash'       => esc_html__( 'No next steps pages found in Trash', 'rtp' ),
		'parent_item_colon'        => esc_html__( 'Parent Next Steps Page:', 'rtp' ),
		'all_items'                => esc_html__( 'All Next Steps Pages', 'rtp' ),
		'archives'                 => esc_html__( 'Next Steps Page Archives', 'rtp' ),
		'attributes'               => esc_html__( 'Next Steps Page Attributes', 'rtp' ),
		'insert_into_item'         => esc_html__( 'Insert into next steps page', 'rtp' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this next steps page', 'rtp' ),
		'featured_image'           => esc_html__( 'Featured image', 'rtp' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'rtp' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'rtp' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'rtp' ),
		'menu_name'                => esc_html__( 'Next Steps Pages', 'rtp' ),
		'filter_items_list'        => esc_html__( 'Filter next steps pages list', 'rtp' ),
		'filter_by_date'           => esc_html__( 'Filter next steps pages by date', 'rtp' ),
		'items_list_navigation'    => esc_html__( 'Next Steps pages list navigation', 'rtp' ),
		'items_list'               => esc_html__( 'Next Steps Pages list', 'rtp' ),
		'item_published'           => esc_html__( 'Next Steps Page published', 'rtp' ),
		'item_published_privately' => esc_html__( 'Next Steps page published privately', 'rtp' ),
		'item_reverted_to_draft'   => esc_html__( 'Next Steps page reverted to draft', 'rtp' ),
		'item_scheduled'           => esc_html__( 'Next Steps Page scheduled', 'rtp' ),
		'item_updated'             => esc_html__( 'Next Steps Page updated', 'rtp' ),
		'text_domain'              => esc_html__( 'rtp', 'rtp' ),
	];
	$args = [
		'label'               => esc_html__( 'Next Steps Pages', 'rtp' ),
		'labels'              => $labels,
		'description'         => '',
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => false,
		'query_var'           => true,
		'can_export'          => true,
		'delete_with_user'    => false,
		'has_archive'         => false,
		'rest_base'           => '',
		'show_in_menu'        => true,
    'menu_position'       => '',
		'menu_icon'           => 'dashicons-desktop',
		'capability_type'     => 'post',
		'supports'            => ['title', 'thumbnail', 'author'],
		'taxonomies'          => [],
		'rewrite'             => [
      'slug' => 'next-steps',
			'with_front' => false,
		],
	];

	register_post_type( 'rtp_next_steps_page', $args );
}

add_action( 'init', 'rtp_next_steps_page_register_post_type' );