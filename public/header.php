<?php
// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}


/**
* Load Google Tag manager Script in <head>
*
*/
function rtp_gtm_head_code() {
  // don't load for admins
  if( current_user_can('manage_options') ) {
    return;
  } 
  ?>
    <!-- Google Tag Manager -->
    <script>
      (function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
      new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
      j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
      'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
      })(window,document,'script','dataLayer','GTM-NQT5LTL');
    </script>
    <!-- End Google Tag Manager -->
  <?php
}

add_action( 'wp_head', 'rtp_gtm_head_code', 1 );




/**
* Output the GTM No script code
*
*/
function rtp_body_open_output() {
  // don't load for admins
  if( current_user_can('manage_options') ) {
    return;
  } 
  ?>
  <!-- Google Tag Manager (noscript) -->
  <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-NQT5LTL"
  height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
<?php
}

add_action( 'ct_before_builder', 'rtp_body_open_output' );