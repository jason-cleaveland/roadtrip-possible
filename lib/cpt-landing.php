<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}




/**
* Set up the video select field
*
*/
function rtp_landing_page_fields( $meta_boxes ) {
    $prefix = 'rtp_landing_';

    $meta_boxes[] = [
        'title'      => __( 'Landing Page Settings', 'rtp' ),
        'post_types' => ['rtp_landing_page'],
//         'context'    => 'after_title',
//         'style'      => 'seamless',
        'fields'     => [
            [
                'name'       => __( 'Presto Video', 'rtp' ),
                'id'         => $prefix . 'presto_video',
                'type'       => 'post',
                'post_type'  => ['pp_video_block'],
                'field_type' => 'select_advanced',
                'required'   => true,
                'placeholder' => 'Select a Video',
            ],
        ],
    ];

    return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'rtp_landing_page_fields' );




/**
* Registers the landing page post type
*
*/
function rtp_landing_page_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Landing Pages', 'rtp' ),
		'singular_name'            => esc_html__( 'Landing Page', 'rtp' ),
		'add_new'                  => esc_html__( 'Add New', 'rtp' ),
		'add_new_item'             => esc_html__( 'Add new landing page', 'rtp' ),
		'edit_item'                => esc_html__( 'Edit Landing Page', 'rtp' ),
		'new_item'                 => esc_html__( 'New Landing Page', 'rtp' ),
		'view_item'                => esc_html__( 'View Landing Page', 'rtp' ),
		'view_items'               => esc_html__( 'View Landing Pages', 'rtp' ),
		'search_items'             => esc_html__( 'Search Landing Pages', 'rtp' ),
		'not_found'                => esc_html__( 'No landing pages found', 'rtp' ),
		'not_found_in_trash'       => esc_html__( 'No landing pages found in Trash', 'rtp' ),
		'parent_item_colon'        => esc_html__( 'Parent Landing Page:', 'rtp' ),
		'all_items'                => esc_html__( 'All Landing Pages', 'rtp' ),
		'archives'                 => esc_html__( 'Landing Page Archives', 'rtp' ),
		'attributes'               => esc_html__( 'Landing Page Attributes', 'rtp' ),
		'insert_into_item'         => esc_html__( 'Insert into landing page', 'rtp' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this landing page', 'rtp' ),
		'featured_image'           => esc_html__( 'Featured image', 'rtp' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'rtp' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'rtp' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'rtp' ),
		'menu_name'                => esc_html__( 'Landing Pages', 'rtp' ),
		'filter_items_list'        => esc_html__( 'Filter landing pages list', 'rtp' ),
		'filter_by_date'           => esc_html__( 'Filter landing pages by date', 'rtp' ),
		'items_list_navigation'    => esc_html__( 'Landing pages list navigation', 'rtp' ),
		'items_list'               => esc_html__( 'Landing Pages list', 'rtp' ),
		'item_published'           => esc_html__( 'Landing Page published', 'rtp' ),
		'item_published_privately' => esc_html__( 'Landing page published privately', 'rtp' ),
		'item_reverted_to_draft'   => esc_html__( 'Landing page reverted to draft', 'rtp' ),
		'item_scheduled'           => esc_html__( 'Landing Page scheduled', 'rtp' ),
		'item_updated'             => esc_html__( 'Landing Page updated', 'rtp' ),
		'text_domain'              => esc_html__( 'rtp', 'rtp' ),
	];
	$args = [
		'label'               => esc_html__( 'Landing Pages', 'rtp' ),
		'labels'              => $labels,
		'description'         => '',
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'query_var'           => true,
		'can_export'          => true,
		'delete_with_user'    => false,
		'has_archive'         => false,
    'show_in_rest'        => false,
		'rest_base'           => '',
		'show_in_menu'        => true,
    'menu_position'       => '',
		'menu_icon'           => 'dashicons-media-document',
		'capability_type'     => 'post',
		'supports'            => ['title', 'thumbnail', 'author'],
		'taxonomies'          => [],
		'rewrite'             => [
      'slug' => 'offer',
			'with_front' => false,
		],
	];

	register_post_type( 'rtp_landing_page', $args );
}

add_action( 'init', 'rtp_landing_page_register_post_type' );