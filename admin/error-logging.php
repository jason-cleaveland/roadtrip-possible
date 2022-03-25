<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}

/**
* write info to log file
*
*/
if ( !function_exists('write_log') ) {
  function write_log($log) {
    if (true === WP_DEBUG) {
      if (is_array($log) || is_object($log)) {
          error_log(print_r($log, true));
      } else {
          error_log($log);
      }
    }
  }
}