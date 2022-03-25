<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}




/**
* Check to see if we have a legit request for the training
*
*/
function rtp_check_training_url_param() {
  // ignore this for admins
  if( is_admin() || current_user_can('manage_options') ) {
    return;
  } 
  // make sure we're on a training page
  $url = $_SERVER['REQUEST_URI'];
  $prev_url = isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : false;
  if( strpos( $url, '/training/' ) === false ) {
    return;
  }
  // make sure we have a key and it is correct
  if( 
    isset($_GET['k']) &&
    !empty($_GET['k']) &&
    $_GET['k'] == 'QXJlIHlvdSB0aGUga2V5bWFzdGVyPw=='  
  ) {
    return;
  }
  // check if we have a referrer
  if( isset($prev_url) && !empty($prev_url) ) {
    wp_redirect($prev_url);
    exit; 
  } else {
    // redirect to home
    wp_redirect(home_url());
    exit; 
  }
}

add_action('init', 'rtp_check_training_url_param' );






/**
* Set up the video select field
*
*/
function rtp_training_page_fields( $meta_boxes ) {
    $prefix = 'rtp_training_';

    $meta_boxes[] = [
        'title'      => __( 'Training Settings', 'rtp' ),
        'post_types' => ['rtp_training_page'],
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
            ],
        ],
    ];

    return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'rtp_training_page_fields' );





/**
* Registers the training page post type
*
*/
function rtp_training_page_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Training Pages', 'rtp' ),
		'singular_name'            => esc_html__( 'Training Page', 'rtp' ),
		'add_new'                  => esc_html__( 'Add New', 'rtp' ),
		'add_new_item'             => esc_html__( 'Add new training page', 'rtp' ),
		'edit_item'                => esc_html__( 'Edit Training Page', 'rtp' ),
		'new_item'                 => esc_html__( 'New Training Page', 'rtp' ),
		'view_item'                => esc_html__( 'View Training Page', 'rtp' ),
		'view_items'               => esc_html__( 'View Training Pages', 'rtp' ),
		'search_items'             => esc_html__( 'Search Training Pages', 'rtp' ),
		'not_found'                => esc_html__( 'No training pages found', 'rtp' ),
		'not_found_in_trash'       => esc_html__( 'No training pages found in Trash', 'rtp' ),
		'parent_item_colon'        => esc_html__( 'Parent Training Page:', 'rtp' ),
		'all_items'                => esc_html__( 'All Training Pages', 'rtp' ),
		'archives'                 => esc_html__( 'Training Page Archives', 'rtp' ),
		'attributes'               => esc_html__( 'Training Page Attributes', 'rtp' ),
		'insert_into_item'         => esc_html__( 'Insert into training page', 'rtp' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this training page', 'rtp' ),
		'featured_image'           => esc_html__( 'Featured image', 'rtp' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'rtp' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'rtp' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'rtp' ),
		'menu_name'                => esc_html__( 'Training Pages', 'rtp' ),
		'filter_items_list'        => esc_html__( 'Filter training pages list', 'rtp' ),
		'filter_by_date'           => esc_html__( 'Filter training pages by date', 'rtp' ),
		'items_list_navigation'    => esc_html__( 'Training pages list navigation', 'rtp' ),
		'items_list'               => esc_html__( 'Training Pages list', 'rtp' ),
		'item_published'           => esc_html__( 'Training Page published', 'rtp' ),
		'item_published_privately' => esc_html__( 'Training page published privately', 'rtp' ),
		'item_reverted_to_draft'   => esc_html__( 'Training page reverted to draft', 'rtp' ),
		'item_scheduled'           => esc_html__( 'Training Page scheduled', 'rtp' ),
		'item_updated'             => esc_html__( 'Training Page updated', 'rtp' ),
		'text_domain'              => esc_html__( 'rtp', 'rtp' ),
	];
	$args = [
		'label'               => esc_html__( 'Training Pages', 'rtp' ),
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
		'menu_icon'           => 'dashicons-desktop',
		'capability_type'     => 'post',
		'supports'            => [
      'title', 
      'thumbnail', 
      'author'
    ],
		'taxonomies'          => [],
		'rewrite'             => [
      'slug' => 'training',
			'with_front' => false,
		],
	];

	register_post_type( 'rtp_training_page', $args );
}

add_action( 'init', 'rtp_training_page_register_post_type' );




