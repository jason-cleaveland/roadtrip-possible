<?php


/**
* This does not work as of 3/3/2022
* throws error: Call to undefined method RTP_Custom_Forminator_hidden_Field_Values::get_property()
in /var/web/site/public_html/wp-content/plugins/roadtrip-possible/vendor/forminator/hidden-field.php on line 204
* https://gist.github.com/arrkiin/49c6407bd36c819a6902f4b360d850ae
*
*/

if ( ! class_exists( 'RTP_Custom_Forminator_hidden_Field_Values' ) ) {
    
    class RTP_Custom_Forminator_hidden_Field_Values {

        private static $_instance = null;

        public static function get_instance() {

            if( is_null( self::$_instance ) ){
                self::$_instance = new RTP_Custom_Forminator_hidden_Field_Values();
            }

            return self::$_instance;
            
        }
      
      
      
      
        /**
        *
        *
        */
        private function __construct() {
            
            $this->init();

        }
      
      
      
      
        /**
        *
        *
        */
        public function init(){
            
            add_filter( 'forminator_custom_form_submit_field_data', array( $this, 'wpmudev_forminator_custom_form_submit_field_data' ), 10, 2 );
            add_filter( 'forminator_vars_list', array( $this, 'wpmudev_forminator_vars_lists' ) );
            add_filter( 'forminator_replace_variables', array( $this, 'wpmudev_forminator_replace_variables' ) );
            add_filter( 'forminator_field_hidden_field_value', array( $this, 'wpmudev_forminator_field_hidden_field_value' ), 10, 3 );

        }
      
      
      
      
        /**
        *
        *
        */
        public function wpmudev_forminator_custom_form_submit_field_data( $field_data_array, $form_id ) {

            $custom_form = Forminator_Custom_Form_Model::model()->load( $form_id );

            $fields = array();

            if ( is_object( $custom_form ) ) {
                foreach( $custom_form->fields as $field ){
                    if( $field->raw['type'] === 'hidden' && $field->raw['default_value'] === 'time_his' ){
                        $fields[] = $field->raw['element_id'];
                    }
                }
            }

            if( empty( $fields ) ){
                return $field_data_array;
            }

            // Add current timestamp
            foreach( $field_data_array as &$field ){
                if( in_array( $field['name'], $fields ) ){
                    $field['value'] = date_i18n( 'H:i:s', forminator_local_timestamp( time() ), true );
                }
            }
            

            return $field_data_array;

        }

      
      
      
        /**
        *
        *
        */    
        public function wpmudev_forminator_vars_lists( $vars_list ){
//             // get the vbout lists - located: cpt-calendar.php
//             $vbout_lists = rtp_vbout_list_options();
//             // massasge the arry to add some context
//             foreach($vbout_lists as $k => $v ) {
//               $new_key = 'vbout_'.$k;
//               $new_value = 'Vbout '.$v;
//               $new_vbout_lists[$new_key] = $new_value;
//             }

            $vars_list = array(
                'user_ip'      => 'User IP Address',
                'date_mdy'     => 'Date (mm/dd/yyyy)',
                'date_dmy'     => 'Date (dd/mm/yyyy)',
                'time_his' 	   => 'Time (H:i:s)',
                'embed_id'     => 'Embed Post/Page ID',
                'embed_title'  => 'Embed Post/Page Title',
                'embed_url'    => 'Embed URL',
                'user_agent'   => 'HTTP User Agent',
                'refer_url'    => 'HTTP Refer URL',
                'user_id'      => 'User ID',
                'user_name'    => 'User Display Name',
                'user_email'   => 'User Email',
                'user_login'   => 'User Login',
                'custom_value' => 'Custom Value',
            );
            // merge the arrays
//             $vars = array_merge($new_vbout_lists, $vars_list);

            return $vars_list;

        }

      
      
      
        /**
        *
        *
        */      
        public function wpmudev_forminator_replace_variables( $content ) {
        
            // If we have no variables, skip
            if ( strpos( $content, '{' ) !== false ) {
                
                $time = date_i18n( 'H:i:s', forminator_local_timestamp(), true );
                $content = str_replace( '{time_his}', $time, $content );

            }
        
            return $content;

        }

      
      
      
        /**
        *
        *
        */      
        public function wpmudev_forminator_field_hidden_field_value( $value, $saved_value, $field ){

            $value       = '';
            $saved_value = Forminator_Field::get_property( 'default_value', $field );
            $embed_url   = forminator_get_current_url();
//             $vbout_prefix = 'vbout';
    d($saved_value);
            switch( $saved_value ) {
//               case str_starts_with($saved_value, $vbout_prefix):
//                     $value = substr($saved_value, strlen($vbout_prefix));
                case "user_ip":
                    $value = Forminator_Geo::get_user_ip();
                    break;
                case "time_his":
                    $value = date_i18n( 'H:i:s', forminator_local_timestamp(), true );
                    break;
                case "date_mdy":
                    $value = date_i18n( 'm/d/Y', forminator_local_timestamp(), true );
                    break;
                case "date_dmy":
                    $value = date_i18n( 'd/m/Y', forminator_local_timestamp(), true );
                    break;
                case "embed_id":
                    $value = forminator_get_post_data( 'ID' );
                    break;
                case "embed_title":
                    $value = forminator_get_post_data( 'post_title' );
                    break;
                case "embed_url":
                    $value = $embed_url;
                    break;
                case "user_agent":
                    $value = $_SERVER[ 'HTTP_USER_AGENT' ];
                    break;
                case "refer_url":
                    $value = isset ( $_SERVER['HTTP_REFERER'] ) ? $_SERVER['HTTP_REFERER'] : $embed_url;
                    break;
                case "user_id":
                    $value = forminator_get_user_data( 'ID' );
                    break;
                case "user_name":
                    $value = forminator_get_user_data( 'display_name' );
                    break;
                case "user_email":
                    $value = forminator_get_user_data( 'user_email' );
                    break;
                case "user_login":
                    $value = forminator_get_user_data( 'user_login' );
                    break;
                case "custom_value":
                    $value = self::get_property( 'custom_value', $field );
                    break;
                default:
                    break;
            }

            return $value;

        }
		
    }

  
  
  
    /**
    *
    *
    */
    if ( ! function_exists( 'RTP_Custom_Forminator_hidden_Field_Values' ) ) {

        function RTP_Custom_Forminator_hidden_Field_Values() {
          return RTP_Custom_Forminator_hidden_Field_Values::get_instance();
        };

      
      
    /**
    *
    *
    */
		add_action( 'plugins_loaded', 'RTP_Custom_Forminator_hidden_Field_Values', 10 );
		
    }
}