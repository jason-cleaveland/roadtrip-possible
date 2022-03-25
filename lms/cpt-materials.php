<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}



/**
* Creates the materials metabox for the editor screen
*
*/

add_filter( 'rwmb_meta_boxes', 'rtp_material_metabox' );

function rtp_material_metabox( $meta_boxes ) {
  $prefix = 'material_';

  $meta_boxes[] = [
    'title'      => 'Materials',
    'id'         => 'materials',
    'post_types' => ['rtp_material'],
    'fields'     => [
      [
        'name'       => 'Category',
        'id'         => $prefix . 'category',
        'type'       => 'taxonomy',
        'placeholder' => 'Select a Material Category',
        'taxonomy'   => ['material_category'],
        'field_type' => 'select_advanced',
        'add_new'    => true,
//         'required'   => true,
        'tooltip'    => [
            'icon'     => '',
            'position' => 'top',
            'content'  => 'Which category does this material belong to?',
        ],
      ],
      [
        'id' => $prefix.'type',
        'type' => 'select_advanced',
        'name' => esc_html__( 'Type of Material' ),
        'tooltip' =>  esc_html__( 'What type of material is this?' ),
        'multiple' => false,
        'placeholder' => 'Select a Type of Material',
        'select_all_none' => false,
        'options' => [
          'pdf' => 'PDF',
          'spreadsheet' => 'Spreadsheet',
          'link' => 'Link',  
          'video' => 'Video',
        ],
      ],
      [
        'id' => $prefix.'description',
        'type' => 'textarea',
        'name' => esc_html__( 'Description', 'obh' ), 
        'tooltip' => esc_html__( 'A short description of the material.' ),
      ],
      [
        'id' => $prefix.'video_link',
        'type' => 'url',
        'name' => esc_html__( 'Video Link' ),
        'tooltip' =>  esc_html__( 'Paste a valid Vimeo URL that includes both the video ID and the security hash: https://vimeo.com/585972921/0fbb00ee34 - or it will not work.' ),
        'visible' => [ $prefix.'type', '=', 'video' ]
      ],
      [
        'id' => $prefix.'link',
        'type' => 'url',
        'name' => esc_html__( 'Link' ),
        'tooltip' =>  esc_html__( 'Paste a link to a resource. Must be a valid URL like this: https://google.com.' ),
        'visible' => [ $prefix.'type', '=', 'link' ]
      ],
      [
        'id' => $prefix.'sheet_id',
        'type' => 'text',
        'name' => esc_html__( 'Google Sheet ID' ),
        'tooltip' =>  esc_html__( 'Paste the ID of a Google Sheet. This file will be opened in drive and allow the end user to save the template into their own Google Drive for editing.' ),
        'visible' => [ 
          $prefix.'type', 
          'in', 
          [
            'spreadsheet',
          ] 
        ]
      ],
      [
        'id' => $prefix.'document_id',
        'type' => 'text',
        'name' => esc_html__( 'Document ID' ),
        'tooltip' =>  esc_html__( 'Paste the link of a file saved to Google Drive. Be sure the file is set to be share with anyone with the link. Be sure to copy the entire link. This file will be opened as a preview with the option to downlod or print.' ),
        'visible' => [ 
          $prefix.'type', 
          'in', 
          [
            'pdf',
          ] 
        ]
      ],
    ],
  ];
  return $meta_boxes;
}

 

/**
* Creates the materials post type
*
*/
function rtp_materials_register_post_type() {
	$labels = [
		'name'                     => esc_html__( 'Materials', 'rtp-i18n' ),
		'singular_name'            => esc_html__( 'Material', 'rtp-i18n' ),
		'add_new'                  => esc_html__( 'Add New', 'rtp-i18n' ),
		'add_new_item'             => esc_html__( 'Add new material', 'rtp-i18n' ),
		'edit_item'                => esc_html__( 'Edit Material', 'rtp-i18n' ),
		'new_item'                 => esc_html__( 'New Material', 'rtp-i18n' ),
		'view_item'                => esc_html__( 'View Material', 'rtp-i18n' ),
		'view_items'               => esc_html__( 'View Materials', 'rtp-i18n' ),
		'search_items'             => esc_html__( 'Search Materials', 'rtp-i18n' ),
		'not_found'                => esc_html__( 'No materials found', 'rtp-i18n' ),
		'not_found_in_trash'       => esc_html__( 'No materials found in Trash', 'rtp-i18n' ),
		'parent_item_colon'        => esc_html__( 'Parent Material:', 'rtp-i18n' ),
		'all_items'                => esc_html__( 'All Materials', 'rtp-i18n' ),
		'archives'                 => esc_html__( 'Material Archives', 'rtp-i18n' ),
		'attributes'               => esc_html__( 'Material Attributes', 'rtp-i18n' ),
		'insert_into_item'         => esc_html__( 'Insert into material', 'rtp-i18n' ),
		'uploaded_to_this_item'    => esc_html__( 'Uploaded to this material', 'rtp-i18n' ),
		'featured_image'           => esc_html__( 'Featured image', 'rtp-i18n' ),
		'set_featured_image'       => esc_html__( 'Set featured image', 'rtp-i18n' ),
		'remove_featured_image'    => esc_html__( 'Remove featured image', 'rtp-i18n' ),
		'use_featured_image'       => esc_html__( 'Use as featured image', 'rtp-i18n' ),
		'menu_name'                => esc_html__( 'Materials', 'rtp-i18n' ),
		'filter_items_list'        => esc_html__( 'Filter materials list', 'rtp-i18n' ),
		'filter_by_date'           => esc_html__( '', 'rtp-i18n' ),
		'items_list_navigation'    => esc_html__( 'Materials list navigation', 'rtp-i18n' ),
		'items_list'               => esc_html__( 'Materials list', 'rtp-i18n' ),
		'item_published'           => esc_html__( 'Material published', 'rtp-i18n' ),
		'item_published_privately' => esc_html__( 'Material published privately', 'rtp-i18n' ),
		'item_reverted_to_draft'   => esc_html__( 'Material reverted to draft', 'rtp-i18n' ),
		'item_scheduled'           => esc_html__( 'Material scheduled', 'rtp-i18n' ),
		'item_updated'             => esc_html__( 'Material updated', 'rtp-i18n' ),
	];
	$args = [
		'label'               => esc_html__( 'Materials', 'rtp-i18n' ),
		'labels'              => $labels,
		'description'         => '',
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => false,
		'show_in_rest'        => false,
		'query_var'           => true,
		'can_export'          => true,
		'delete_with_user'    => true,
		'has_archive'         => false,
		'rest_base'           => '',
		'show_in_menu'        => 'learndash-lms',
		'menu_icon'           => 'dashicons-admin-generic',
		'capability_type'     => 'post',
		'supports'            => ['title', 'author'],
		'taxonomies'          => [],
		'rewrite'             => [
      'slug' => 'material',
			'with_front' => false,
		],
	];

	register_post_type( 'rtp_material', $args );
}

add_action( 'init', 'rtp_materials_register_post_type' );


/**
* Registers the Materials Category Taxonomy
*
*/
function rtp_materials_register_taxonomy() {
	$labels = [
		'name'                       => esc_html__( 'Material Categories', 'rtp-i18n' ),
		'singular_name'              => esc_html__( 'Material Category', 'rtp-i18n' ),
		'menu_name'                  => esc_html__( 'Material Categories', 'rtp-i18n' ),
		'search_items'               => esc_html__( 'Search Material Categories', 'rtp-i18n' ),
		'popular_items'              => esc_html__( 'Popular Material Categories', 'rtp-i18n' ),
		'all_items'                  => esc_html__( 'All Material Categories', 'rtp-i18n' ),
		'parent_item'                => esc_html__( 'Parent Material Category', 'rtp-i18n' ),
		'parent_item_colon'          => esc_html__( 'Parent Material Category', 'rtp-i18n' ),
		'edit_item'                  => esc_html__( 'Edit Material Category', 'rtp-i18n' ),
		'view_item'                  => esc_html__( 'View Material Category', 'rtp-i18n' ),
		'update_item'                => esc_html__( 'Update Material Category', 'rtp-i18n' ),
		'add_new_item'               => esc_html__( 'Add new material category', 'rtp-i18n' ),
		'new_item_name'              => esc_html__( 'New material category name', 'rtp-i18n' ),
		'separate_items_with_commas' => esc_html__( 'Separate material categories with commas', 'rtp-i18n' ),
		'add_or_remove_items'        => esc_html__( 'Add or remove material categories', 'rtp-i18n' ),
		'choose_from_most_used'      => esc_html__( 'Choose most used material categories', 'rtp-i18n' ),
		'not_found'                  => esc_html__( 'No material categories found', 'rtp-i18n' ),
		'no_terms'                   => esc_html__( 'No Material Categories', 'rtp-i18n' ),
		'filter_by_item'             => esc_html__( 'Filter by material category', 'rtp-i18n' ),
		'items_list_navigation'      => esc_html__( 'Material categories list pagination', 'rtp-i18n' ),
		'items_list'                 => esc_html__( 'Material Categories list', 'rtp-i18n' ),
		'most_used'                  => esc_html__( 'Most Used', 'rtp-i18n' ),
		'back_to_items'              => esc_html__( 'Back to material categories', 'rtp-i18n' ),
		'text_domain'                => esc_html__( 'rtp-i18n', 'rtp-i18n' ),
	];
	$args = [
		'label'              => esc_html__( 'Material Categories', 'rtp-i18n' ),
		'labels'             => $labels,
		'description'        => '',
		'public'             => true,
		'publicly_queryable' => true,
		'hierarchical'       => true,
		'show_ui'            => false,
		'show_in_menu'       => false,
		'show_in_nav_menus'  => false,
		'show_in_rest'       => false,
		'show_tagcloud'      => false,
		'show_in_quick_edit' => true,
		'show_admin_column'  => true,
		'query_var'          => true,
		'sort'               => false,
		'meta_box_cb'        => 'post_tags_meta_box',
		'rest_base'          => '',
		'rewrite'            => [
      'slug' => 'material-type',
			'with_front'   => false,
			'hierarchical' => false,
		],
	];
	register_taxonomy( 'material_category', ['rtp_material'], $args );
}
add_action( 'init', 'rtp_materials_register_taxonomy' );