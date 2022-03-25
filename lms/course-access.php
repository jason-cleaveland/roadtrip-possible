<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/**
* Gets the user access data from the user_meta
*
*/
function rtp_get_course_access_data($user_id) {
  $access_data = get_user_meta( $user_id, 'rtp_course_access', false );
  // Check if it is an array - if so return value
  if( is_array($access_data) ) {
    return $access_data;
  }
  // If not create array and return
  $access_array[] = $access_data;
  return $access_array;
}




/**
* Determines user access for a given post and displays a modal with a link to the landing page if there is no access
* https://iiiji.com/css-only-modal-lightbox-pop-up-on-page-load/
*/
function rtp_course_user_access(){
  if( !is_user_logged_in() ) {
    return;
  }
  
  // set up the post data
  global $post;
  $post_id = $post->ID;
  $post_type = $post->post_type;
  
  if( $post_type != 'rtp_course' ) {
    return;
  }

  // set up the user access data
  $user_id = get_current_user_id();  
  $access_data = rtp_get_course_access_data($user_id);

  if( !isset($access_data) || empty($access_data) ) {
    $access_data = [];
  }

  // check for access the main course and if so, give us access to everything
  $settings = get_option( 'rtp_settings' );
  $primary_course_id = $settings['rtp_primary_course'];
  $main_course_access = in_array( $primary_course_id, $access_data );
  
  if( $main_course_access ) {  
    // if we do not have access and the modal is edit - hide the modal
    if( !$main_course_access && !$post->rtp_course_enable_modal ) {
      return;
    }
    // if we have access and the modal is default - do not show modal
    if( $main_course_access && $post->rtp_course_enable_modal ) {
      return;
    }
  }
 
  if( !$main_course_access ) {
    // check for access to this course
    $this_course_access = in_array( $post_id, $access_data );
    // if we do not have access and the modal is edit - hide the modal
    if( !$this_course_access && !$post->rtp_course_enable_modal ) {
      return;
    }
    // if we have access and the modal is default - do not show modal
    if( $this_course_access && $post->rtp_course_enable_modal ) {
      return;
    }
  }

  // if I have access and the modal is off, I will see the modal
  // set up a default message for the modal
  if( isset($post->rtp_course_access_modal_content) && !empty($post->rtp_course_access_modal_content) ) {
    $modal_content = $post->rtp_course_access_modal_content;
  } else {
    $modal_content = '<p>You can get access though.</p>';
    $modal_content .= '<p>You can learn more by clicking the button below!</p>';
  }
  
  // set up the landing page to link to
  if( isset($post->rtp_course_landing_page) && !empty($post->rtp_course_landing_page) ) {
    $landing_page_link = get_permalink($post->rtp_course_landing_page);
  } else {
    $landing_page_link = home_url();
  }
     

// Display the actual modal
?>
<div id="rtp-no-access-modal" class="rtp-modal rtp-animate-opacity">
   <div class="rtp-modal-content">
      <div class="rtp-modal-inner">
        <div class="rtp-modal-headline">
          <h2>Whoa there partner!</h2>
          <p>You can't access this content yet.</p>
        </div>
        <hr/>
        <div class="rtp-modal-body">
          <?php echo $modal_content ?>
        </div>
        <div id="">
          <a href="<?php echo $landing_page_link ?>" target="_blank" class="rtp-modal-button fl-button" role="button">
          <!-- <i class="fl-button-icon fl-button-icon-before fas fa-car-side" aria-hidden="true"></i> -->
          <span class="fl-button-text">I want to learn more!</span>
          </a>
        </div>
        <div id="">
          <a href="" id="rtp-not-now-modal" onclick="window.history.back(); return false;"> Not Right Now </a>
        </div>
      </div>
   </div>
</div>

<?php 
  
}

add_action( 'wp_footer', 'rtp_course_user_access' );