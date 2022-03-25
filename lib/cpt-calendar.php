<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}




/**
* Set up the video select field
*
*/
function rtp_calendar_page_fields( $meta_boxes ) {
    $prefix = 'rtp_calendar_';

    $meta_boxes[] = [
      'title'      => __( 'Calendar Settings', 'rtp' ),
      'post_types' => ['rtp_calendar_page'],
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
        [
          'name'       => __( 'Next Steps Page', 'rtp' ),
          'id'         => $prefix . 'next_steps_page',
          'desc'      => 'Which next steps page should this calendar redirect to?',
          'type'       => 'post',
          'post_type'  => ['rtp_next_steps_page'],
          'field_type' => 'select_advanced',
          'required'   => true,
        ],
        [
          'type' => 'select_advanced',
          'name' => 'Vbout Target List',
          'id'   => $prefix . 'vbout_target_list',
          'options' => rtp_vbout_list_options(),
          'placeholder' => 'Select a Target List',
          'desc'      => 'Which VBout list should people who complete this form be added to? NOTE: if this is different from the list they are currently on, it will remove them from that list.',
          'required'   => true,
        ],
        [
          'type' => 'text',
          'name' => 'Vbout Tag Added',
          'id'   => $prefix . 'vbout_tag_added',
          'std' => 'false',
          'desc'      => 'Which tags should be added to the contact\'s VBout profile? Note: input false here for no tag added.',
          'required'   => true,
        ],
          [
          'type' => 'text',
          'name' => 'Vbout Tag Removed',
          'id'   => $prefix . 'vbout_tag_removed',
          'std' => 'false',
          'desc'      => 'Which tags should be removed from the contact\'s VBout profile? Note: input false here for no tag removed.',
          'required'   => true,
        ],
        [
          'type' => 'text',
          'name' => 'Vbout Activity',
          'id'   => $prefix . 'vbout_activity',
          'std' => 'false',
          'desc'      => 'Which activity should be added to the contact\'s VBout profile? Note: input false here for no activity.',
          'required'   => true,
        ],
      ],
    ];

    return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'rtp_calendar_page_fields' );






/**
* Registers the calendar page post type
*
*/
function rtp_calendar_page_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Calendar Pages', 'rtp' ),
		'singular_name'            => esc_html__( 'Calendar Page', 'rtp' ),
		'add_new'                  => esc_html__( 'Add New', 'rtp' ),
		'add_new_item'             => esc_html__( 'Add new calendar page', 'rtp' ),
		'edit_item'                => esc_html__( 'Edit Calendar Page', 'rtp' ),
		'new_item'                 => esc_html__( 'New Calendar Page', 'rtp' ),
		'view_item'                => esc_html__( 'View Calendar Page', 'rtp' ),
		'view_items'               => esc_html__( 'View Calendar Pages', 'rtp' ),
		'search_items'             => esc_html__( 'Search Calendar Pages', 'rtp' ),
		'not_found'                => esc_html__( 'No calendar pages found', 'rtp' ),
		'not_found_in_trash'       => esc_html__( 'No calendar pages found in Trash', 'rtp' ),
		'parent_item_colon'        => esc_html__( 'Parent Calendar Page:', 'rtp' ),
		'all_items'                => esc_html__( 'All Calendar Pages', 'rtp' ),
		'archives'                 => esc_html__( 'Calendar Page Archives', 'rtp' ),
		'attributes'               => esc_html__( 'Calendar Page Attributes', 'rtp' ),
		'insert_into_item'         => esc_html__( 'Insert into calendar page', 'rtp' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this calendar page', 'rtp' ),
		'featured_image'           => esc_html__( 'Featured image', 'rtp' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'rtp' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'rtp' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'rtp' ),
		'menu_name'                => esc_html__( 'Calendar Pages', 'rtp' ),
		'filter_items_list'        => esc_html__( 'Filter calendar pages list', 'rtp' ),
		'filter_by_date'           => esc_html__( 'Filter calendar pages by date', 'rtp' ),
		'items_list_navigation'    => esc_html__( 'Calendar pages list navigation', 'rtp' ),
		'items_list'               => esc_html__( 'Calendar Pages list', 'rtp' ),
		'item_published'           => esc_html__( 'Calendar Page published', 'rtp' ),
		'item_published_privately' => esc_html__( 'Calendar page published privately', 'rtp' ),
		'item_reverted_to_draft'   => esc_html__( 'Calendar page reverted to draft', 'rtp' ),
		'item_scheduled'           => esc_html__( 'Calendar Page scheduled', 'rtp' ),
		'item_updated'             => esc_html__( 'Calendar Page updated', 'rtp' ),
		'text_domain'              => esc_html__( 'rtp', 'rtp' ),
	];
	$args = [
		'label'               => esc_html__( 'Calendar Pages', 'rtp' ),
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
		'menu_icon'           => 'dashicons-calendar-alt',
		'capability_type'     => 'post',
		'supports'            => ['title', 'thumbnail', 'author'],
		'taxonomies'          => [],
		'rewrite'             => [
      'slug' => 'calendar',
			'with_front' => false,
		],
	];

	register_post_type( 'rtp_calendar_page', $args );
}

add_action( 'init', 'rtp_calendar_page_register_post_type' );




/**
* Get an array of lists to select from
*
*/
function rtp_vbout_list_options() {
  $app = new EmailMarketingWS(VBOUT_KEY);
  $all_lists = $app->getMyLists();
  foreach($all_lists['items'] as $list_array ) {
    $dedashed_name = str_replace(' - ', '_', $list_array['name']);
    $name = str_replace(' ', '_', strtolower($dedashed_name));
    $lists[$name] = $list_array['name'];
//     $lists[$list_array['id']] = $list_array['name'];
  }
  return $lists;
}