<?php
/**
 * Shortcode for the mark course complete button
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// border-style: solid;
//     border-color: #1c9b4f;
//     border-width: 1px;
// }



/**
* Shortcode for step/framework navigation
*
*/

function rtp_mark_complete_shortcode( $atts ) {
//   use this to use a sa shortcode
//   $post_id = $atts['post_id'];
  $post_id = get_the_id();
  $course_step_post = get_post($post_id);
  $complete_button = rtp_mark_complete( $course_step_post );
  return $complete_button;
}

// add_shortcode( 'rtp_mark_complete', 'rtp_mark_complete_shortcode' );



/**
 * Outputs the HTML output to mark a course complete.
 *
 * Must meet requirements of course to mark the course as complete.
 *
 * @since 2.1.0
 *
 * @param WP_Post $post The `WP_Post` lesson or topic object.
 * @param array   $atts Optional. An array of attributes to mark course complete. Default empty array.
 *
 * @return string HTML output to mark course complete
 */
function rtp_mark_complete( $post, $atts = [] ) {
  
  if ( 
    isset( $_POST['rtp_mark_complete'] ) 
    && isset( $_POST['post'] ) 
    && intval( $_POST['post'] ) == $post->ID 
  ) {
    return '';
  }
 
  if ( isset( $_POST['rtp_mark_complete'] ) && isset( $_POST['post'] ) && intval( $_POST['post'] ) == $post->ID ) {
		return '';
	}

	$user_id = get_current_user_id();
  $course_id = learndash_get_course_id( $post->ID );
	$progress = learndash_get_course_progress( null, $post->ID );
  $completed = $progress['this']->completed;
  $nonce = wp_create_nonce( 'rtp_mark_complete_' . $user_id . '_' . $post->ID );

  if ( $completed ) {
    // this would be where the link to mark incomplete would  go
    //Tooltips: https://codepen.io/laviperchik/pen/PKMdKY
    ob_start();
    ?>
    <form id="rtp_mark_incomplete_button" class="ct-link oxel_icon_button__container c-btn-transparent" method="post" action="">
      <input type="hidden" value="<?php echo $post->ID ?>" name="post" />
      <input type="hidden" value="<?php echo $course_id ?>" name="course_id" />
      <input type="hidden" value="<?php echo $user_id ?>" name="user_id" />
      <input type="hidden" value="<?php echo $nonce ?>" name="rtp_mark_incomplete" />
      <button 
        class="learndash_mark_incomplete_button" 
        type="submit" 
        data-tooltip="Click here to mark incomplete">
          <i class="fas fa-circle-xmark"></i>
          <span class="fl-button-text">Mark Incomplete</span>
      </button>
    </form>
    <?php
    $output = ob_get_clean();
    return $output;
  }
  
  ob_start();
  ?>
    <form id="rtp_mark_complete_button" class="ct-link oxel_icon_button__container c-btn-transparent" method="post" action="">
      <input type="hidden" value="<?php echo $post->ID ?>" name="post" />
      <input type="hidden" value="<?php echo $course_id ?>" name="course_id" />
      <input type="hidden" value="<?php echo $user_id ?>" name="user_id" />
      <input type="hidden" value="<?php echo $nonce ?>" name="rtp_mark_complete" />
      <button class="learndash_mark_complete_button" type="submit">
        <i class="fas fa-circle-check"></i>
        <span class="fl-button-text">Mark Complete</span>
      </button>
    </form>
  <?php
    $output = ob_get_clean();
  return $output;
}




/**
 * Processes a request to mark a course complete.
 *
 * @global WP_Post $post Global post object.
 *
 * @since 2.1.0
 *
 * @param WP_Post|null $post Optional. The `WP_Post` object. Defaults to global post object.
 */
function rtp_mark_complete_process() {
	if ( 
    ( isset( $_POST['rtp_mark_complete'] ) ) 
    && ( ! empty( $_POST['rtp_mark_complete'] ) ) 
    && ( isset( $_POST['post'] ) ) 
    && ( ! empty( $_POST['post'] ) ) 
  ) {
    
    $course_id = 1742;
		$post_id = intval( $_POST['post'] );
  
		if ( isset( $_POST['user_id'] ) ) {
			$user_id = intval( $_POST['user_id'] );
		} else {
			$user_id = get_current_user_id();
		}

		if ( ! wp_verify_nonce( $_POST['rtp_mark_complete'], 'rtp_mark_complete_' . $user_id . '_' . $post_id ) ) {
			return;
		}

		$return = learndash_process_mark_complete( $user_id, $post_id, false, $course_id );

		if ( $return ) {
      // could put code to redirect to next step here
      // decided not to in order to prevent confusion and potential missed steps
			$next_link = get_permalink( $post_id );
		}

		if ( ! empty( $next_link ) ) {
				learndash_safe_redirect( $next_link );
    }
  }
}

add_action( 'wp', 'rtp_mark_complete_process' );



/**
 * Processes the request to mark a course or step incomplete.
 *
 * @global WP_Post $post Global post object.
 *
 * Fires on `wp` hook.
 *
 * @since 2.1.0
 *
 * @param WP $post Optional. The `WP` object. Default null.
 */
function rtp_mark_incomplete_process( $post = null ) {
	if ( 
    ( isset( $_POST['rtp_mark_incomplete'] ) ) 
    && ( ! empty( $_POST['rtp_mark_incomplete'] ) ) 
    && ( isset( $_POST['post'] ) ) 
    && ( ! empty( $_POST['post'] ) ) 
  ) {

    $course_id = 1742;
		$post_id = intval( $_POST['post'] );
    
    if ( isset( $_POST['user_id'] ) ) {
			$user_id = intval( $_POST['user_id'] );
		} else {
			$user_id = get_current_user_id();
		}

		if ( ! wp_verify_nonce( $_POST['rtp_mark_incomplete'], 'rtp_mark_complete_' . $user_id . '_' . $post_id ) ) {
			return;
		}

		$return = learndash_process_mark_incomplete( $user_id, $course_id, $post_id, false );
	}
}

add_action( 'wp', 'rtp_mark_incomplete_process' );