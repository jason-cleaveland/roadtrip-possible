<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}


/**
* Outputs copyright content
*
*
*/
function rtp_footer_copyright() {
  $current = date('Y');
  $site = home_url();
  $link = '<a class="rtp-footer-link c-transition" href="'.$site.'"> Roadtrip Possible</a>';
  $content = '<p>Copyright &copy; 2017-'.$current.$link.'</p>';
  return $content;
}