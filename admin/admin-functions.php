<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/**
* removes the smartcrawl metabox for these post types
*
*/
function rtp_remove_smartcrawl_metabox() {

  global $pagenow;
  // make sure we're on a post edit screen and get the post type
  if( 'post.php' === $pagenow && isset($_GET['post']) && !empty($_GET['post']) ){
    $post_id = $_GET['post'];
    $post_type = get_post_type($post_id);
  }
  // or a new post screen and get the post type
  if( 'post-new.php' === $pagenow && isset($_GET['post_type']) && !empty($_GET['post_type']) ) {
    $post_type = $_GET['post_type'];
  }
  // if we don't have a post time - move along
  if( !isset($post_type) || empty($post_type) ) {
    return;
  }
  // list of excluded post types
  $excluded_post_types = [
    'ct_template',
    'sfwd-courses',
    'sfwd-lessons',
    'sfwd-topic',
    'sfwd-quiz',
    'sfwd-certificates',
    'sfwd-transactions',
    'sfwd-essays',
    'sfwd-assignment',
    'rtp_material',
    'rtp_faq',
  ];
  // if we're not in the list - move along
  if ( !in_array( $post_type, $excluded_post_types )  ) {
    return;
  }
  // actually remove the metabox
  remove_action( 'admin_menu', array( Smartcrawl_Metabox::get(), 'smartcrawl_create_meta_box' ) );
}

add_action( 'admin_menu', 'rtp_remove_smartcrawl_metabox', 0 );