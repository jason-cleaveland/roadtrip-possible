<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/**
* Registers the course post type
*
*/
function rtp_course_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Courses', 'rtp' ),
		'singular_name'            => esc_html__( 'Course', 'rtp' ),
		'add_new'                  => esc_html__( 'Add New', 'rtp' ),
		'add_new_item'             => esc_html__( 'Add new course', 'rtp' ),
		'edit_item'                => esc_html__( 'Edit Course', 'rtp' ),
		'new_item'                 => esc_html__( 'New Course', 'rtp' ),
		'view_item'                => esc_html__( 'View Course', 'rtp' ),
		'view_items'               => esc_html__( 'View Courses', 'rtp' ),
		'search_items'             => esc_html__( 'Search Courses', 'rtp' ),
		'not_found'                => esc_html__( 'No courses found', 'rtp' ),
		'not_found_in_trash'       => esc_html__( 'No courses found in Trash', 'rtp' ),
		'parent_item_colon'        => esc_html__( 'Parent Course:', 'rtp' ),
		'all_items'                => esc_html__( 'All Courses', 'rtp' ),
		'archives'                 => esc_html__( 'Course Archives', 'rtp' ),
		'attributes'               => esc_html__( 'Course Attributes', 'rtp' ),
		'insert_into_item'         => esc_html__( 'Insert into course', 'rtp' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this course', 'rtp' ),
		'featured_image'           => esc_html__( 'Featured image', 'rtp' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'rtp' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'rtp' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'rtp' ),
		'menu_name'                => esc_html__( 'Courses', 'rtp' ),
		'filter_items_list'        => esc_html__( 'Filter courses list', 'rtp' ),
		'filter_by_date'           => esc_html__( '', 'rtp' ),
		'items_list_navigation'    => esc_html__( 'courses list navigation', 'rtp' ),
		'items_list'               => esc_html__( 'Courses list', 'rtp' ),
		'item_published'           => esc_html__( 'Course published', 'rtp' ),
		'item_published_privately' => esc_html__( 'course published privately', 'rtp' ),
		'item_reverted_to_draft'   => esc_html__( 'course reverted to draft', 'rtp' ),
		'item_scheduled'           => esc_html__( 'Course scheduled', 'rtp' ),
		'item_updated'             => esc_html__( 'Course updated', 'rtp' ),
		'text_domain'              => esc_html__( 'rtp', 'rtp' ),
	];
	$args = [
		'label'               => esc_html__( 'Courses', 'rtp' ),
		'labels'              => $labels,
		'description'         => 'Displays Courses',
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'query_var'           => true,
		'can_export'          => true,
		'delete_with_user'    => false,
		'has_archive'         => true,
		'rest_base'           => '',
		'show_in_menu'        => true,
    'menu_position'       => '',
		'menu_icon'           => 'dashicons-money',
		'capability_type'     => 'post',
		'supports'            => ['title', 'thumbnail', 'author'],
		'taxonomies'          => [],
		'rewrite'             => [
      'slug' => 'course',
			'with_front' => false,
		],
	];

	register_post_type( 'rtp_course', $args );
}

add_action( 'init', 'rtp_course_register_post_type' );




/**
* Adds settings field for course
*
*/
function rtp_course_fields( $meta_boxes ) {
$prefix = 'rtp_course_';

$meta_boxes[] = [

  'title'     => 'Course',
  'tabs'      => [
    'mc-content' => 'Content',
    'mc-settings' => 'Settings',
    'mc-access' => 'Access',
    'mc-steps' => 'Steps',
  ],
  'tab_style' => 'box',
  'tab_default_active' => 'content',
  'post_types' => ['rtp_course'],
  'context'    => 'after_title',
  'fields'     => [
    [
      'name'          => __( 'Course Type', 'rtp' ),
      'id'            => $prefix . 'course_type',
      'type'          => 'radio',
      'options'       => [
        'single_step' => __( 'Single Step', 'rtp' ),
        'multi_step' => __( 'Multi Step', 'rtp' ),
      ],
      'std'           => 'single_step',
  //     'required'      => true,
      'admin_columns' => [
        'position'   => 'after title',
        'title'      => 'Course Type',
        'sort'       => true,
        'searchable' => true,
        'filterable' => true,
      ],
      'inline'        => false,
      'tooltip' => [
        'position' => 'top',
        'content'  => __( 'What type of Course is this?', 'rtp' ),
      ],
      'tab' => 'mc-settings',
    ],
    [
      'name'          => __( 'Access Type', 'rtp' ),
      'id'            => $prefix . 'access_type',
      'type'          => 'radio',
      'options'       => [
        'free' => __( 'Free', 'rtp' ),
        'open' => __( 'Open', 'rtp' ),
        'paid' => __( 'Paid', 'rtp' ),
      ],
      'std'           => 'paid',
  //     'required'      => true,
      'admin_columns' => [
        'position'   => 'after title',
        'title'      => 'Access Type',
        'sort'       => true,
        'searchable' => true,
        'filterable' => true,
      ],
      'inline'        => false,
      'tooltip' => [
        'position' => 'top',
        'content'  => __( 'What type of access should users have for this Course?', 'rtp' ),
      ],
      'tab' => 'mc-settings',
    ],
    [
      'name'    => __( 'Base Price', 'rtp' ),
      'id'      => $prefix . 'base_price',
      'type'    => 'number',
  //     'required'      => true,
      'visible' => [
        'when'     => [['access_type', '=', 'paid']],
        'relation' => 'or',
      ],
      'tooltip' => [
        'position' => 'top',
        'content'  => 'Set a whole number - no decimals. Discounts will be set on landing pages.',
      ],
      'placeholder' => __( 'What is the base price for the Course?', 'rtp' ),
      'tab' => 'mc-settings',
    ],
    /**
    *
    * Access
    *
    **/
    [
      'name'        => __( 'Landing Page', 'rtp' ),
      'id'          => $prefix . 'landing_page',
      'type'        => 'post',
  //     'required'      => true,
      'post_type'   => ['rtp_landing_page'],
      'field_type'  => 'select_advanced',
      'placeholder' => __( 'Select a Landing Page', 'rtp' ),
      'visible' => [
        'when'     => [['access_type', '=', 'paid']],
        'relation' => 'or',
      ],
      'tooltip' => [
        'position' => 'top',
        'content'  => 'Which landing page should we redirect users to if they do not have access to this course?',
      ],
      'tab' => 'mc-access',
    ],
    [
      'name'    => 'Access Modal Content',
      'id'      => $prefix . 'access_modal_content',
      'type'    => 'wysiwyg',
      'raw'     => false,
      'options' => [
          'textarea_rows' => 8,
          'teeny'         => true,
      ],
      'tooltip' => [
        'position' => 'top',
        'content'  => 'This content will be displayed on the page if the user does not have access. It should provide them with some information on how to purchase access and why they should.',
      ],
      'tab' => 'mc-access',
    ],
    [
      'id'        => $prefix . 'enable_modal',
      'name'      => 'Enable Modal',
      'type'      => 'switch',
      'style'     => 'rounded',
      'on_label'  => 'Default',
      'off_label' => 'Edit',
      'std' => 1,
      'tooltip' => [
        'position' => 'top',
        'content'  => 'Default - users with access see the course, users without see the modal.<br />Edit - users with access see the modal, users without see the course.',
      ],
      'tab' => 'mc-access',
    ],
    /**
    *
    * Content
    *
    **/
    [
      'name'        => __( 'Video', 'rtp' ),
      'id'          => $prefix . 'video',
      'type'        => 'oembed',
      'placeholder' => __( 'What video do you want to display?', 'rtp' ),
      'tooltip'     => [
        'icon'     => '',
        'position' => 'top',
        'content'  => 'Paste a link to a video here.',
      ],
      'tab' => 'mc-content',
    ],
    [
      'name'        => __( 'Description', 'rtp' ),
      'id'          => $prefix . 'description',
      'type'        => 'textarea',
      'placeholder' => __( 'What is this course about?', 'rtp' ),
      'tooltip'     => [
        'icon'     => '',
        'position' => 'top',
        'content'  => 'Describe the content of the course or leave blank to not include a description.',
      ],
      'tab' => 'mc-content',
    ],
    [
      'name'       => __( 'Materials', 'rtp' ),
      'id'         => $prefix . 'materials',
      'type'       => 'post',
      'post_type'  => ['rtp_material'],
      'field_type' => 'select_advanced',
      'clone'      => true,
      'sort_clone' => true,
      'add_button' => __( 'Add Another Material', 'rtp' ),
      'tooltip'    => [
        'icon'     => '',
        'position' => 'top',
        'content'  => 'Select the materials to include with this course. Materials are created in the LMS menu.',
      ],
      'tab' => 'mc-content',
    ],
  ],
];

    return $meta_boxes;
}

add_filter( 'rwmb_meta_boxes', 'rtp_course_fields' );




/**
* Hide the steps tab for single step courses
*
**/
function rtp_hide_course_steps_tab( $conditions ) {
  $prefix = 'rtp_course_'; 
  $conditions['.rwmb-tab-mc-steps'] = array(
      'hidden' => array( $prefix . 'course_type', 'single_step' ),
  );
  return $conditions;
}

add_filter( 'rwmb_outside_conditions', 'rtp_hide_course_steps_tab' );


