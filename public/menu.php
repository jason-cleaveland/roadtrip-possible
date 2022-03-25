<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/*
* Creates a login/logout link for the main menu
*
*/
function rtp_menu_items( $items, $args  ) {
  
  foreach( $items as $item ) { 
    // Login logout link
    if ( $item->url == '#log-in-log-out' ) {       
      if( is_user_logged_in() ) {
        $item->url = home_url('/the-course/');
        $item->post_title = 'The Course';
        $item->post_name = 'log-out';
        $item->title = 'The Course';
      } else {
        $item->url = 'https://roadtrippossible.com/log-in/';
        $item->post_title = 'Log In';
        $item->post_name = 'log-in';
        $item->title = 'Log In';
      }
    }
    // course login/logout link
     if ( $item->url == '#course-log-in-log-out' ) {       
      if( is_user_logged_in() ) {
        $item->url = wp_logout_url(home_url('/log-in/'));
        $item->post_title = 'Log Out';
        $item->post_name = 'log-out';
        $item->title = 'Log Out';
      }
    }
    // LMS Large Menu
    if( $item->ID == 1778 ) {
      $user = wp_get_current_user();
      $first_name = ucfirst($user->user_firstname);
      $item->title = 'Welcome Back ' . $first_name;
      $item->classes[] = 'rtp-lms-acct-menu';
    }
  }
  return $items;
}
add_filter( 'wp_nav_menu_objects', 'rtp_menu_items', 10, 2 );




/**
* Returns the login/course url for the footer link
*
*
*/
function rtp_login_course_footer_link() {
  if( is_user_logged_in() ) {
    return home_url('/the-course/');
  } else {
    return 'https://roadtrippossible.com/log-in/';
  }
}




/**
* Returns the label for the footer link
*
*/
function rtp_login_course_footer_label() {
  if( is_user_logged_in() ) {
    return 'The Course';
  } else {
    return 'Log In';
  }
}