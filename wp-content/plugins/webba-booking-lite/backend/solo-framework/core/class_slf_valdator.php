<?php
// Solo Framework validator
if ( ! defined( 'ABSPATH' ) ) exit;

class SLFValidator{

	public static function getAllowedTables(){
        return array( 'wbk_email_templates', 'wbk_gg_calendars', 'wbk_appointments', 'wbk_service_categories', 'wbk_coupons' );
    } 
   

    public static function checkNameField( $value, $condition, $environment ){
        global $wpdb;
        $value = sanitize_text_field( trim( $value ) );
        if ( strlen( $value ) > $condition[1] || strlen( $value ) < $condition[0] ) {
            return false;        
        }  
        $row_id = $environment[0];
        $table_name = $environment[1];

        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }

        $found_id = $wpdb->get_var( $wpdb->prepare( 
                                                    "
                                                        SELECT id 
                                                        FROM $table_name 
                                                        WHERE name = '%s'
                                                    ", 
                                                     $value
                                                ) );
         
        if( $found_id == NULL ){
            return true;
        } else {
            if( $row_id == $found_id ){
                return true;
            } else {
                return false;
            }
        }
	}
    public static function checkEmailTemplateField( $value, $condition, $environment ) {
        $table_name = $environment[1];
        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }
        if ( strlen( $value ) > $condition[1] || strlen( $value ) < $condition[0] ) {
            return false;        
        } 
        return true;
    }
    public static function checkText( $value, $condition, $environment ){
        $table_name = $environment[1];
        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }
        $value = sanitize_text_field( trim( $value ) );
       if ( strlen( $value ) > $condition[1] || strlen( $value ) < $condition[0] ) {
            return false;        
        } 
        return true;
    }
    public static function checkInteger( $value, $condition, $environment ){
        $table_name = $environment[1];
        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }
        $value =  intval( sanitize_text_field( trim( $value ) ) );
        if ( $value > $condition[1] ||  $value  < $condition[0] ) {
            return false;        
        } 
        return true;
    }
     public static function checkIntegerOrNull( $value, $condition, $environment ){       
        $table_name = $environment[1];
        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }
        if( trim( $value ) == '' ){
            return true;
        }
        $value =  intval( sanitize_text_field( trim( $value ) ) );
        if ( $value > $condition[1] ||  $value  < $condition[0] ) {
            return false;        
        } 
        return true;
    }
    public static function checkDate( $value, $condition, $environment ){
        $table_name = $environment[1];
        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }
        $value =  intval( sanitize_text_field( trim( $value ) ) );
        if( $value <  strtotime('today midnight') ){
            return false;
        }

        return true;
    }
    public static function checkEmail( $value, $condition, $environment ){
        $table_name = $environment[1];
        if( !in_array( $table_name, self::getAllowedTables() ) ){
            return false;
        }
        $value =  sanitize_text_field( trim( $value ) );
        if ( !preg_match( '/^([a-z0-9_\.-]+)@([a-z0-9_\.-]+)\.([a-z\.]{2,10})$/', $value ) ) {
            return false;
        }  
        return true;       
    }
}

?>