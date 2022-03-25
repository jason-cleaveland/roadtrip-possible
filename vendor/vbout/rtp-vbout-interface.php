<?php


class Rtp_Vbout {
  


  // data array from the form
  public $form_array = [
      'first_name' => '',
      'last_name' => '',
      'email_address' => '',
      'vbout_target_list' => '',
      'vbout_add_tag' => '',
      'vbout_remove_tag' => '',
      'vbout_activity' => '',
      'user_ip' => '',
    ];
  
  // email address from the form
  public $email_address = '';
  
  // email address from the form
  public $contact = '';
  
  // data array formatted for vbout
  public $vbout_array = [];
  
  // array of contact lists in vbout
  private $lists = [];
  
  // the vbout api object
  private $app;
  

  
  
  
  /**
  *
  *
  */
  function __construct( $form_input_array = [] ) {
    // init vbout app
    $this->app = $this->app();
    $this->form_array = array_merge( $this->form_array, $form_input_array );
    $this->email_address = $this->form_array['email_address'];
    $this->contact = $this->get_contact();
    $this->vbout_array = $this->send_data();
  }
  
  
  
  /**
  * massage the data to fit the vbout standards
  *
  */
  private function send_data() {
    // set up variables
    $contact = $this->contact;
    $form_array = $this->form_array;
    $target_list_id = $this->get_target_list( $form_array['vbout_target_list'], false );
    $add_tag = $this->var_check( $form_array['vbout_add_tag'], false );
    $remove_tag = $this->var_check( $form_array['vbout_remove_tag'], false );
    $activity = $this->var_check( $form_array['vbout_activity'], false );
    $output_fields = [];
    // determine if we have a contact
    if( $contact ) {
      // get the contact's current list
      $current_list_id = $contact['listid'];
    }
    // get the target list array (for the fields)
    $target_list_array = $this->get_list_data( $target_list_id );
    // get the names of the target list fields as K=>V
    foreach( $target_list_array['fields'] as $field_id => $name ) {
      // add an underscore to the name to make them match up with our form array
      $field_name = str_replace(' ', '_', strtolower($name));
      // bind the field array data to the vbout field ID
      foreach( $form_array as $k => $v ) {
        if( $k == $field_name ) {
          $output_fields[$field_id] = $v;
        }
      }
    }
    // array of parameters to send to vbout to create a new contact
    $contact_params = [
      'email' => $form_array['email_address'],
      'ipaddress' => $form_array['user_ip'],
      'status' => 'active',
      // list to add the contact to
      'listid' => $target_list_id,
      // Additonal fields
      'fields' => $output_fields
    ];
    // maybe create a contact
    if( !$contact ) {
      // create the contact
      $this->create_contact( $contact_params );
      // then maybe add tags
      if( $add_tag ) {
        $add_tag = $this->add_tag( $contact_params['email'], $add_tag );
      }
      // then maybe add an activity
//       if( $activity ) {
//         $create_activity = $this->create_activity( $contact['id'], $activity );
//       }
    } else {
      // maybe add tag to existing contact
      if( $add_tag ) {
        $added_tag = $this->add_tag( $contact_params['email'], $add_tag );
      }
      // maybe remove tag
      if( $remove_tag ) {
        $removed_tag = $this->remove_tag( $contact_params['email'], $remove_tag );
      }
      // maybe move contact to a different list
      if( $target_list_id != $current_list_id ) {
        $move_contact = $this->move_contact( $contact['id'], $target_list_id, $current_list_id);
      }
      // maybe create an activity
      if( $activity ) {
        $create_activity = $this->create_activity( $contact['id'], $activity );
      }
    }  
  }




  /**
  * create an activity record for a contact in Vbout
  *
  */
  public function create_activity( $contact_id, $activity ) {
    // set up the params
    $params = [
        'id' => $contact_id,
        'description' => $activity,
        'datetime' => current_time('Y-m-d h:m'),
    ];
    // engage
    $results = $this->app->addActivity( $params );
  }

  
  
  
  
  /**
  * Move a contact to a new list
  *
  */
  public function move_contact( $contact_id = false, $destination_list_id = false, $current_list_id = false ) {
    // confirm that we have values here
    if( !$contact_id || !$destination_list_id || !$current_list_id ) {
      return;
    }
    // set the args
    $args = [
      'id' => $contact_id,
      'listid' => $destination_list_id,
      'sourceid' => $current_list_id,
    ];  
    // do the deed
    $list = $this->app->moveContact($args);
    return $list;
  }




  /**
  * Add a tag to the vbout contact
  *
  */
  public function add_tag( $email = false, $tag = false ) {
    if( !$email || !$tag ) {
      return;
    }
    $params = array(
      'email' => $email,
      'tagname' => $tag,
    );
    $results = $this->app->addTag( $params );
  }



  
  /**
  *
  *
  */
  public function remove_tag(  $email = false, $tag = false ) {
    if( !$email || !$tag ) {
      return;
    }
    $params = array(
      'email' => $email,
      'tagname' => $tag,
    );
    $results = $this->app->removeTag( $params );
  }




  /**
  * get the contact data if it exists
  *
  */
  public function get_contact() {    
    $contact = $this->app->searchContact($this->email_address);
    if( isset($contact['errorCode']) ) {
      return false;
    } else {
      return $contact[0];
    }
  }
  
  
  
  
  /**
  * get the data about the list
  *
  */
  public function get_list_data( $list_id ) {
    $list = $this->app->getMyList( $list_id );
    return $list;
  } 
  
  
  
  
  /**
  * get all lists and extract their name and Id for other use
  *
  */
  public function get_all_lists() {
    $lists = [];
    $vbout_lists = $this->app->getMyLists();
    foreach( $vbout_lists['items'] as $list_array ) {
      $dedashed_name = str_replace(' - ', '_', $list_array['name']);
      $name = str_replace(' ', '_', strtolower($dedashed_name));
      $lists[$name] = $list_array['id'];
    }
    return $lists;
  }  
  
  
  
  
  /**
  * Create a new contact in VBout
  *
  */
  public function create_contact( $params ) {
    $new_contact = $this->app->addNewContact( $params );
  }





  /**
  * return ID for a target list - if not, return id for potential
  *
  */
  private function get_target_list( $target_list_var ) {
    // check if there is a target list
    $target_list = $this->var_check( $target_list_var, false );
    $all_lists = $this->get_all_lists();
    if( $target_list ) {      
      return $all_lists[$target_list_var];
    } else {
      return $all_lists['potential_customer_organic'];
    }
  }
  
  
  
  
  /**
  * check for the presence of a variable
  *
  */
  private function var_check( $variable, $default = false ) {
    if( isset( $variable ) && !empty( $variable ) ) {
      return $variable;
    } else {
      return $default;
    }
  }

  
  
  
  /**
  * initialize the vbout api
  *
  */
  private function app() {
    $key = VBOUT_KEY;
    $app = new EmailMarketingWS($key);
    return $app;
  } 
  
}