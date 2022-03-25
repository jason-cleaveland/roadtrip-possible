<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Silence is golden.
}


/**
* Limit submission to one email address per form
*
*/
function rtp_limit_submissions( $submit_errors, $form_id, $field_data_array ) {
  // get all forms
  $form_objects = Forminator_API::get_forms();
  foreach( $form_objects as $form_object ) {
    $form_array[] = $form_object->to_array();
  }
  // get only forms with registration in the title
  foreach( $form_array as $form ) {
    if( strpos( $form['name'], 'registration' ) !== false ) {
      $restricted_forms[] = $form['id'];
    }
  }
  // create the actual error message
  if( empty( $submit_errors ) && in_array( $form_id, $restricted_forms ) ) {
    $error_message = 'It looks like you\'ve already signed up with that email address. Maybe try a different one?';
    foreach( $field_data_array as $field ){
      // check if we have an email field
      if( strpos( $field['name'], 'email' ) !== false ) {
        global $wpdb;
				$table_meta = $wpdb->prefix . 'frmt_form_entry_meta';
				$table_entry = $wpdb->prefix . 'frmt_form_entry';
        // check if we have a matching email address
        $email_count = $wpdb->get_var( $wpdb->prepare( "
            SELECT COUNT(1) 
            FROM $table_meta as meta 
            LEFT JOIN $table_entry as entries 
            ON meta.entry_id = entries.entry_id 
            WHERE meta.meta_key LIKE 'email%' 
            AND meta.meta_value=%s 
            AND entries.form_id = %d 
            LIMIT 1;
          ", 
          $field['value'], 
          $form_id 
          ) 
        );
        // if there is a field that already has that email registered then throw the error
				if( $email_count > 0 ){ $submit_errors[][$field['name']] = $error_message;	}
				break;
			}
    }
  }
  return $submit_errors;
}

add_filter( 'forminator_custom_form_submit_errors', 'rtp_limit_submissions', 10, 3 );





/**
* Format the data from the forminator form to a keyed array and send to the VBOUT API function
* 
*/
function format_data_to_send_to_vbout( $entry, $form_id, $form_data ) {
  // set up contact_data array  
  $contact_data = [];
  // get forms with a vbout field
  $forms = rtp_get_vbout_forms();
  // make sure we're on a vbout form
  if( !in_array( $form_id, $forms ) ) {
//     return;
  }
  // get the current form data
  $form = Forminator_API::get_form( $form_id );
  // get the field data
  $fields = $form->get_fields_as_array();
  // create an array of field names from the field IDs 
  write_log($fields);
  foreach( $fields as $field ) {
    // check what type of field we're on
    if( $field['type'] == 'name') {
      $field_data['first_name'] = $field['id'];
      $field_data['last_name'] = $field['id'];      
    } elseif( $field['type'] == 'address') {
      $field_data['address_1'] = $field['id'];
      $field_data['address_2'] = $field['id'];
      $field_data['country'] = $field['id'];
      $field_data['state'] = $field['id'];
      $field_data['city'] = $field['id'];
      $field_data['zip_code'] = $field['id'];
    } else {
      if( isset($field['field_label']) && !empty($field['field_label']) ) {
        $field_data[str_replace(' ', '_', strtolower($field['field_label']))] = $field['id'];
      }
    }
  }
  write_log($field_data);
  write_log($form_data);
  // transform the form data into a keyed array with a usable form key  
  foreach( $form_data as $data ) {
    if( $data['name'] == '_forminator_user_ip' ) {
      $contact_data['user_ip'] = $data['value'];
    } else {
      if( strpos( $data['name'], 'name' ) === 0 ) {
        $contact_data['first_name'] = $data['value']['first-name'];
        $contact_data['last_name'] = $data['value']['last-name'];       
      } elseif( strpos( $data['name'], 'address' ) === 0 ) {
        $contact_data['address_1'] = $data['value']['street_address'];
        $contact_data['address_2'] = $data['value']['address_line'];
        $contact_data['country'] = 'United States';
        $contact_data['state'] = $data['value']['state'];
        $contact_data['city'] = $data['value']['city'];
        $contact_data['zip_code'] = $data['value']['zip'];
      } elseif ( strpos( $data['name'], 'phone' ) === 0 ) {
        $contact_data['cell_phone'] = $data['value'];
      } else {
        $field_id = array_search ($data['name'], $field_data);    
        $contact_data[$field_id] = $data['value'];
      }
    }
  }
  write_log($contact_data);
  // send the data to the VBOUT API
  $vbout = new Rtp_Vbout($contact_data);
}
add_action( 'forminator_custom_form_submit_before_set_fields', 'format_data_to_send_to_vbout', 10, 3);




/**
* get all forms with Vbout fields
*
*
*/
function rtp_get_vbout_forms() {
  // get all the forms
  $forms = Forminator_API::get_forms();
  // loop through them and find the ones with Vbout fields
  foreach( $forms as $form ) {
    foreach( $form->fields as $fields) {  
      // make sure we have a field label
      if( !isset($fields->raw['field_label']) || empty($fields->raw['field_label']) ) {
        continue;
      } else {
        $field = str_replace(' ', '_', strtolower($fields->raw['field_label']));
      }
      // check if it is vbout field
      if( strpos( $field, 'vbout' ) !== false ) {
        $vbout_forms_array_raw[] = $form->id;
      } else {
        continue;
      }
    }    
  }
  //return an array of those with Vbout fields
  return array_values(array_unique($vbout_forms_array_raw));
}