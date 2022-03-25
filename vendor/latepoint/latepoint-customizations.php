<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}



/**
* inject js to calendar pages to set up redirect
*
*
*/
function rtp_calendar_page_redirect() {
  // check if we're on a calendar page
  $uri = $_SERVER['REQUEST_URI'];
  if( strpos( $uri, '/calendar/') === false ) {
    return;
  }
  // get the id of the post to redirect to for this calendar
  $next_steps_id = rwmb_meta( 'rtp_calendar_next_steps_page' );
  // make sure we have a post to redirect to
  if( !isset($next_steps_id) || empty($next_steps_id) ) {
    return;
  }
  $vbout_data['target_list'] = rwmb_meta( 'rtp_calendar_vbout_target_list' );
  $vbout_data['add_tag'] = rwmb_meta( 'rtp_calendar_vbout_tag_added' );
  $vbout_data['remove_tag'] = rwmb_meta( 'rtp_calendar_vbout_tag_removed' );
  $vbout_data['activity'] = rwmb_meta( 'rtp_calendar_vbout_activity' );
  $query_string = http_build_query($vbout_data);
  // get the url of the post
  $next_steps_url = get_permalink( $next_steps_id );
  // output the JS
  ?>
    <script>
      jQuery.ajaxSetup({
        dataFilter: function (data, type) {
          var myData = JSON.parse(data);
          var currentStep = myData.step_name;
          if(currentStep == 'confirmation' ) {
            var parser = new DOMParser();
            var doc = parser.parseFromString(myData.message, "text/html");
            var bookingId = doc.querySelector('.confirmation-number').querySelectorAll('strong')[0].innerText;
            var nextUrl = "<?php echo $next_steps_url;?>";
            var queryString = "<?php echo $query_string; ?>"; 
            var encoded = encodeURIComponent(btoa("b=" + bookingId + "&" + queryString));
            window.location = nextUrl + "?" + encoded;
            return;
          }
          return data;
        }
      });
    </script>
  <?php
}

add_action( 'wp_footer', 'rtp_calendar_page_redirect' );




/**
* transforms time saved as mins since midnight into localized 12-hour time
*
*/
function rtp_adjust_appt_time($date, $mins, $tz) {
  // create 24-hour time from mins
  $hours = floor($mins / 60);
  $minutes = floor($mins % 60);
  $mil_time = str_pad($hours, 2, "0", STR_PAD_LEFT).':'.str_pad($minutes, 2, "0", STR_PAD_LEFT);
  // if the timezone is chicago, transform into 12 hour time
  if( $tz == 'America/Chicago' ) {
    $time = date('g:i a', strtotime($mil_time));
  } else {
    // create the chicago timezone onject first
    $date = new DateTime( $date.' '.$mil_time.':'.'00', new DateTimeZone('America/Chicago'));
    // transform tinot new timezone
    $date->setTimezone(new DateTimeZone($tz));
    $time =  $date->format('g:i a');
  }
  return $time;
} 





/**
* Sends appointment data to Vbout 
* and creates array of data to send to the confirmation page
*
*/
function rtp_process_booking() {
  //   check if we're on the calendar success page
  $uri = $_SERVER['REQUEST_URI'];
  if( strpos( $uri, '/next-steps/') === false ) {
    return;
  }
  // make sure we're not on the oxygen edit page
  if( isset($_GET['action']) && $_GET['action'] === 'ct_save_components_tree' ) {
    return;
  }
  // make sure we have a booking
  if( isset($_GET) && !empty($_GET) ) {
    $query = htmlspecialchars(array_key_first($_GET));
    parse_str(base64_decode($query), $url_data);
    $booking_id = $url_data['b'];
    if(!$booking_id) {
      // redirect home of we don't have a booking ID
      wp_redirect( home_url() ); 
      exit; 
    }
  } else {
    // redirect home of we don't have a $_GET param
    wp_redirect( home_url() ); 
    exit; 
  }
  global $wpdb;
  $ip_address = $_SERVER['REMOTE_ADDR'];
  $prefix = $wpdb->prefix;
  $bookings_table = $prefix.'latepoint_bookings'; 
  $bookings_services_table = $prefix.'latepoint_services';
  $customer_table = $prefix.'latepoint_customers';
  $customer_meta_table = $prefix.'latepoint_customer_meta';
  $agent_table = $prefix.'latepoint_agents';
  $vbout_target_list = $url_data['target_list'];
  $vbout_add_tag = $url_data['add_tag'];
  $vbout_remove_tag = $url_data['remove_tag'];
  $vbout_activity = $url_data['activity'];
  // get booking data 	
  $booking_data = $wpdb->get_row( 
    "
      SELECT
        id,
        start_date,
        end_date,
        start_time,
        end_time,
        customer_id,
        service_id,
        agent_id,
        created_at
      FROM $bookings_table 
      WHERE id = $booking_id
    ", ARRAY_A );
  $service_id = $booking_data['service_id'];
  $booking_service = $wpdb->get_row( 
  "
    SELECT name
    FROM $bookings_services_table 
    WHERE id = $service_id
  ", ARRAY_A );
  $booking_data['service'] = $booking_service['name'];

  // get customer data
  $customer_id = $booking_data['customer_id'];
  $customer_data = $wpdb->get_row( 
  "
    SELECT 
      id,
      first_name,
      last_name,
      email,
      created_at
    FROM $customer_table 
    WHERE id = $customer_id
  ", ARRAY_A );
  $customer_timezone = $wpdb->get_row( 
  "
    SELECT meta_value
    FROM $customer_meta_table 
    WHERE object_id = $customer_id
  ", ARRAY_A );
  $customer_data['ip'] = $ip_address;
  $customer_data['timezone'] = $customer_timezone['meta_value'];
  // rework the timezone
  switch( $customer_timezone['meta_value'] ) {
    case 'America/New_York':
      $customer_data['tz_label'] = 'Eastern';
      break;
    case 'America/Chicago':
      $customer_data['tz_label'] = 'Central';
      break;
    case 'America/Phoenix':
      $customer_data['tz_label'] = 'Mountain';
      break;
    default:
      $customer_data['tz_label'] = 'Pacific';
  }

  // get agent data
  $agent_id = $booking_data['agent_id'];
  $agent_data = $wpdb->get_row( 
  "
    SELECT
      id,
      first_name,
      last_name,
      email,
      avatar_image_id,
      created_at      
    FROM $agent_table 
    WHERE id = $agent_id
  ", ARRAY_A );
  
  // 

  // set raw appointment data
  $appt_data = [
    'id' => $booking_data['service_id'],
    'service' => $booking_data['service'],
    'booking_date' => date('n/j/Y', strtotime($booking_data['start_date'])),
    'booking_start' => rtp_adjust_appt_time( 
      $booking_data['start_date'],
      $booking_data['start_time'], 
      $customer_data['timezone'] ),
    'booking_end' => rtp_adjust_appt_time( 
      $booking_data['start_date'], 
      $booking_data['end_time'], 
      $customer_data['timezone'] ),
    'timezone' => $customer_data['tz_label'],
    'first_name' => $customer_data['first_name'],
    'last_name' => $customer_data['last_name'],
    'email_address' => $customer_data['email'],
    'vbout_target_list' => $vbout_target_list,
    'vbout_add_tag' => $vbout_add_tag,
    'vbout_remove_tag' => $vbout_remove_tag,
    'vbout_activity' => $vbout_activity,
    'agent' => $agent_data['first_name'],
    'created_at' => date('h:m a', strtotime($booking_data['created_at'])),
    'created_on' => date('n/j/Y', strtotime($booking_data['created_at'])),
  ];
  // send the appt data to Vbout
  $vbout = new Rtp_Vbout($appt_data);
  // send data to the success page
  ?>
    <div id="div_block-95-2854" class="ct-div-block c-margin-bottom-s">
      <div id="div_block-91-2854" class="ct-div-block rtp-underline-dotted">
        <h6 id="headline-92-2854" class="ct-headline c-h6">
          Contact Info
        </h6>
      </div>
      <div id="div_block-102-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-103-2854" class="ct-div-block c-right">
           <div id="headline-104-2854" class="ct-text-block c-bold">
            First Name:
          </div>
        </div>
        <div id="div_block-105-2854" class="ct-div-block ">
          <div id="text_block-106-2854" class="ct-text-block">
            <?php echo $appt_data['first_name'];?>
          </div>
        </div>
      </div>
      <div id="div_block-65-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-66-2854" class="ct-div-block c-right">
           <div id="headline-68-2854" class="ct-text-block c-bold">
            Last Name:
          </div>
        </div>
        <div id="div_block-69-2854" class="ct-div-block ">
          <div id="text_block-70-2854" class="ct-text-block">
            <?php echo $appt_data['last_name'];?>
          </div>
        </div>
      </div>
      <div id="div_block-76-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-77-2854" class="ct-div-block c-right">
           <div id="headline-78-2854" class="ct-text-block c-bold">
            Email:
          </div>
        </div>
        <div id="div_block-79-2854" class="ct-div-block ">
          <div id="text_block-80-2854" class="ct-text-block">
            <?php echo $appt_data['email_address'];?>
          </div>
        </div>
      </div>
    </div>
    <div id="div_block-285-2854" class="ct-div-block c-margin-bottom-s">
      <div id="div_block-286-2854" class="ct-div-block rtp-underline-dotted">
         <h6 id="headline-287-2854" class="ct-headline c-h6">
          Appointment Info
        </h6>
      </div>
      <div id="div_block-288-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-289-2854" class="ct-div-block c-right">
           <div id="headline-290-2854" class="ct-text-block c-bold">
            Date:
          </div>
        </div>
        <div id="div_block-291-2854" class="ct-div-block ">
          <div id="text_block-292-2854" class="ct-text-block">
            <?php echo $appt_data['booking_date'];?>
          </div>
        </div>
      </div>
      <div id="div_block-293-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-294-2854" class="ct-div-block c-right">
           <div id="headline-295-2854" class="ct-text-block c-bold">
          Start Time:
          </div>
        </div>
        <div id="div_block-296-2854" class="ct-div-block ">
          <div id="text_block-297-2854" class="ct-text-block">
            <?php echo $appt_data['booking_start'];?>
          </div>
        </div>
      </div>
      <div id="div_block-298-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-299-2854" class="ct-div-block c-right">
           <div id="headline-300-2854" class="ct-text-block c-bold">
            End Time:
          </div>
        </div>
        <div id="div_block-301-2854" class="ct-div-block ">
          <div id="text_block-302-2854" class="ct-text-block">
            <?php echo $appt_data['booking_end'];?>
          </div>
        </div>
      </div>
      <div id="div_block-303-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-304-2854" class="ct-div-block c-right">
           <div id="headline-305-2854" class="ct-text-block c-bold">
            Time Zone:
          </div>
        </div>
        <div id="div_block-306-2854" class="ct-div-block ">
          <div id="text_block-307-2854" class="ct-text-block">
            <?php echo $appt_data['timezone'];?>
          </div>
        </div>
      </div>
      <div id="div_block-308-2854" class="ct-div-block c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-309-2854" class="ct-div-block c-right">
           <div id="headline-310-2854" class="ct-text-block c-bold">
            Consultant:
          </div>
        </div>
        <div id="div_block-311-2854" class="ct-div-block ">
          <div id="text_block-312-2854" class="ct-text-block">
            <?php echo $appt_data['agent'];?>
          </div>
        </div>
      </div>
      <div id="div_block-313-2854" class="ct-div-block   c-columns-1-2 c-columns-gap-xs">
        <div id="div_block-314-2854" class="ct-div-block c-right">
           <div id="headline-315-2854" class="ct-text-block c-bold">
            Location:
          </div>
        </div>
        <div id="div_block-316-2854" class="ct-div-block ">
          <div id="text_block-317-2854" class="ct-text-block">
            TBD
          </div>
        </div>
      </div>
    </div>
    <div id="text_block-318-2854" class="ct-text-block c-text-s c-margin-bottom-s">
      Booking submitted on: <b><?php echo $appt_data['created_on'];?></b> at: 
      <b><?php echo $appt_data['created_at'];?></b> Central
    </div>
    <a id="link-345-2854" class="ct-link c-btn-alt" href="http://https://roadtrippossible.com/wp-admin/admin-post.php?action=latepoint_route_call&amp;route_name=bookings__ical_download&amp;latepoint_booking_id=<?php echo $appt_data['id'];?>" target="_self">
      <div id="fancy_icon-346-2854" class="ct-fancy-icon ">
        <svg id="svg-fancy_icon-346-2854" style="margin-right: 10px;">
          <use xlink:href="#FontAwesomeicon-calendar"></use>
        </svg>
      </div>
      <div id="text_block-347-2854" class="ct-text-block ">Add to My Calendar</div>
    </a>
    <script>
    document.addEventListener("DOMContentLoaded", function(event) {
      var firstName = '<?php echo $appt_data['first_name']; ?>';
      var lastName = '<?php echo $appt_data['last_name']; ?>';
      var email = '<?php echo $appt_data['email_address']; ?>';
      document.getElementById('forminator-field-text-1').setAttribute('value', firstName);
      document.getElementById('forminator-field-text-2').setAttribute('value', lastName);
      document.getElementById('forminator-field-email-1').setAttribute('value', email);
    });
    </script>
  <?php
}

