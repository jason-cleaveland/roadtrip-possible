<?php
/**
 * Shortcode for ld_course_resume
 *
 * @since 3.1.4
 *
 * @package LearnDash\Shortcodes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Builds the `ld_course_resume` shortcode output.
 *
 * @global boolean $learndash_shortcode_used
 *
 * @since 3.1.4
 *
 * @param array  $atts {
 *    An array of shortcode attributes.
 *
 *    @type int     $course_id  Optional. Course ID. Default 0.
 *    @type int     $user_id    Optional. User ID. Default current user ID.
 *    @type string  $label      Optional. Resume label. Default empty.
 *    @type string  $html_class Optional. The resume CSS classes. Default 'ld-course-resume'.
 *    @type string  $html_id    Optional. The value for id HTML attribute. Default empty.
 *    @type boolean $button     Optional. Whether to show button. Default true.
 * }
 * @param string $content The shortcode content.
 *
 * @return string The `ld_course_resume` shortcode output.
 */
function rtp_course_resume_shortcode() {
  $content = '';
  $user_id = get_current_user_id();
  $resume_course_id = learndash_get_last_active_course( $user_id );

	if ( ( ! empty( $user_id ) ) && ( ! empty( $resume_course_id ) ) ) {

		$course_status = learndash_course_status( $resume_course_id, $user_id, true );
    
		if ( 'in-progress' === $course_status ) {
			$user_course_last_step_id = learndash_user_course_last_step( $user_id, $resume_course_id );
			if ( ! empty( $user_course_last_step_id ) ) {
				$progress = learndash_get_course_progress( null, $user_course_last_step_id, $resume_course_id );
        
				if ( ( isset( $progress['next'] ) ) && ( is_a( $progress['next'], 'WP_Post' ) ) ) {
					$user_course_last_step_id = $progress['next']->ID;
				}
        
				$course_permalink = learndash_get_step_permalink( $user_course_last_step_id, $resume_course_id );
				if ( ! empty( $course_permalink ) ) {
          $content .= '<div class="fl-button-group-button rtp-left-nav">';
          $content .= '<a class="ld-course-resume ld-button" href="'.$course_permalink.'">' . 'Resume Course' . '</a>';
          $content .= '</div>';
				}
			}
		}
	}

	return $content;
}
add_shortcode( 'rtp_course_resume', 'rtp_course_resume_shortcode', 10, 2 );