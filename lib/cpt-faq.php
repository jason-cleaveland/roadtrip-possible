<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/******* 
* TO DO:
* Create a Taxonomy to determine which FAQs should be dispayed where (landing page, main site, etc.)
* Get Oxy Extras: https://oxyextras.com/
* Create the main FAQ page: https://wpdevdesign.com/posts-accordion-using-oxyextras/
* Integrate the search shortcode below
*
*
*/



/**
* Initiate Frequently Asked Questions Custom Post Type
* Code at bootm of file
*
*/
if ( ! function_exists('rtp_faq') ) {
  add_action( 'init', 'rtp_faq', 0 );
}





/**
* Shortcode for FAQ search
*
*/
function rtp_faq_search_shortcode( $atts ) {
  ob_start();
    ?>
   
      <div id="rtp-search" class="fl-module">
        <div class="fl-module-content fl-node-content">
          <div class="pp-search-form-wrap">
            <div class="pp-search-form__container rtp-search-form-icon">
              <i class="fa fa-search" aria-hidden="true"></i>
              <input type="text" onkeyup="filterAccordion()" placeholder="Search" class="pp-search-form__input rtp-search-form-input" title="Search" value="" id="search-faq">
            </div>
          </div>	
        </div>
      </div>
    <script>
    function filterAccordion() {
        // Declare variables
        var input, filter, q, a, i, c = 0;
        input = document.getElementById('search-faq');
        filter = input.value.toUpperCase();
        if( filter === '' && 
            jQuery('.pp-accordion-item').find('.pp-accordion-content').is(':visible')) {
    //         jQuery('.pp-accordion-item').find('.pp-accordion-button').trigger('click');
        }
        jQuery(".pp-accordion-item").each(function(){
            q = jQuery(this).find(".pp-accordion-button-label");
            a = jQuery(this).find(".pp-accordion-content");
            if (q.html().toUpperCase().indexOf(filter) > -1 || a.html().toUpperCase().indexOf(filter) > -1 ) {
                jQuery(this).removeAttr('style');
                if( jQuery(this).find('.pp-accordion-content').is(':hidden') ) {
    //                 jQuery(this).find('.pp-accordion-button').trigger('click');
                    c = 0;
                    jQuery('.no-result').remove();
                }
            } else {
                jQuery(this).css('display', "none");
                c = c + 1;
            }
        });

        if( jQuery(".pp-accordion-item").length <= parseInt( c ) && jQuery('.no-result').length <= 0 ) {
             jQuery(".pp-accordion").prepend( '<div class="no-result">Sorry! We couldn\'t find anything like that. Please try again.</div>');
        }
    }
    </script>
    <?php
  return ob_get_clean();
}

add_shortcode( 'rtp_faq_search', 'rtp_faq_search_shortcode' );




/**
* Register Frequently Asked Questions Custom Post Type
*
*/
if ( ! function_exists('rtp_faq') ) {
  function rtp_faq() {

    $labels = [
      'name'                  => _x( 'FAQs', 'Post Type General Name', 'rtp' ),
      'singular_name'         => _x( 'FAQ', 'Post Type Singular Name', 'rtp' ),
      'menu_name'             => __( 'FAQs', 'rtp' ),
      'name_admin_bar'        => __( 'FAQ', 'rtp' ),
      'archives'              => __( 'FAQ Archives', 'rtp' ),
      'attributes'            => __( 'FAQ Attributes', 'rtp' ),
      'parent_item_colon'     => __( 'Parent FAQ:', 'rtp' ),
      'all_items'             => __( 'All FAQs', 'rtp' ),
      'add_new_item'          => __( 'Add New FAQ', 'rtp' ),
      'add_new'               => __( 'Add New', 'rtp' ),
      'new_item'              => __( 'New FAQ', 'rtp' ),
      'edit_item'             => __( 'Edit FAQ', 'rtp' ),
      'update_item'           => __( 'Update FAQ', 'rtp' ),
      'view_item'             => __( 'View FAQ', 'rtp' ),
      'view_items'            => __( 'View FAQs', 'rtp' ),
      'search_items'          => __( 'Search FAQ', 'rtp' ),
      'not_found'             => __( 'Not found', 'rtp' ),
      'not_found_in_trash'    => __( 'Not found in Trash', 'rtp' ),
      'featured_image'        => __( 'Featured Image', 'rtp' ),
      'set_featured_image'    => __( 'Set featured image', 'rtp' ),
      'remove_featured_image' => __( 'Remove featured image', 'rtp' ),
      'use_featured_image'    => __( 'Use as featured image', 'rtp' ),
      'insert_into_item'      => __( 'Insert into FAQ', 'rtp' ),
      'uploaded_to_this_item' => __( 'Uploaded to this FAQ', 'rtp' ),
      'items_list'            => __( 'FAQs list', 'rtp' ),
      'items_list_navigation' => __( 'FAQs list navigation', 'rtp' ),
      'filter_items_list'     => __( 'Filter FAQs list', 'rtp' ),
    ];
    $args = [
      'label'                 => __( 'FAQ', 'rtp' ),
      'description'           => __( 'Frequently asked questions', 'rtp' ),
      'labels'                => $labels,
      'supports'              => [
        'title', 
        'editor' 
       ],
      'rewrite'               => [
        'slug' => 'faq'
      ],
      'hierarchical'          => false,
      'public'                => true,
      'show_ui'               => true,
      'show_in_menu'          => true,
      'menu_position'         => 5,
      'menu_icon'             => 'dashicons-editor-help',
      'show_in_admin_bar'     => true,
      'show_in_nav_menus'     => true,
      'can_export'            => true,
      'has_archive'           => false,
      'exclude_from_search'   => false,
      'publicly_queryable'    => true,
      'capability_type'       => 'page',
      'show_in_rest'          => false,
    ];
    register_post_type( 'rtp_faq', $args );

  }
  add_action( 'init', 'rtp_faq', 0 );
}