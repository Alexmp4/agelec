<?php
// check if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;
class WBK_Db_Utils {
	// create tables
	static function createTables() {
		global $wpdb;
		// service table
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_services (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            email varchar(128) default '',
	            description varchar(1024) default '',
	            business_hours varchar(255) default '',
	            users varchar(512) default '',
	            duration int unsigned NOT NULL,	            
	            step int unsigned NOT NULL,
	            interval_between int unsigned NOT NULL,
				form int unsigned NOT NULL default 0,
				quantity int unsigned NOT NULL default 1,
				price FLOAT NOT NULL DEFAULT 0,
				notification_template int unsigned NOT NULL default 0,
				reminder_template int unsigned NOT NULL default 0,
				payment_methods varchar(255) NOT NULL DEFAULT '',
	            prepare_time int unsigned NOT NULL default 0, 
         	    date_range varchar(128) default '',
         	   	gg_calendars varchar(512) default '',
	       		invoice_template int unsigned NOT NULL default 0,	       		
	       		multi_mode_limit varchar(128) NOT NULL default '',
	       		multi_mode_low_limit varchar(128) NOT NULL default '',
	       		priority int NOT NULL default 0,
	            UNIQUE KEY id (id)
	       		) 
		        DEFAULT CHARACTER SET = utf8
		        COLLATE = utf8_general_ci"
	    ); 
		// custom on/off days
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_days_on_off (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            service_id int unsigned NOT NULL,
	            day int unsigned NOT NULL,
	            status int unsigned NOT NULL,
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
	   	// custom locked timeslots
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_locked_time_slots (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            service_id int unsigned NOT NULL,
	            time int unsigned NOT NULL,
	            connected_id int unsigned NOT NULL default 0,
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		// appointments table
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_appointments (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            email varchar(128) default '',
	            phone varchar(128) default '',
	            description varchar(1024) default '',
	            extra varchar(2048) default '',
	            attachment varchar(1024) default '',
	           	service_id int unsigned NOT NULL,
				time int unsigned NOT NULL,
				day int unsigned NOT NULL,
				duration int unsigned NOT NULL,
				actual_duration int unsigned NOT NULL default 0,
				created_on int unsigned NOT NULL default 0,
				quantity int unsigned NOT NULL default 1,
				status varchar(255) default 'pending',
				payed  FLOAT NOT NULL DEFAULT 0,
				payment_id varchar(255) default '',
     			token varchar(255) NOT NULL DEFAULT '',
     			payment_cancel_token varchar(255) NOT NULL DEFAULT '',
     			admin_token varchar(255) NOT NULL DEFAULT '',   			
				expiration_time int unsigned NOT NULL default 0,
				time_offset int NOT NULL default 0,
				gg_event_id varchar(255) default '',
				coupon int NOT NULL default 0,
				payment_method varchar(255) default '',				
				paid_amount varchar(128) default '',
				lang varchar(255) default '',
				moment_price varchar(255) default '',
	            UNIQUE KEY id (id)
	        ) 
		        DEFAULT CHARACTER SET = utf8
		        COLLATE = utf8_general_ci"
	    );
	    // email templates
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_email_templates (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            template varchar(2000) default '',
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		// service categories
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_service_categories (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            category_list varchar(512) default '',
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		// google calendar
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_gg_calendars (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            access_token varchar(512) default '',
	            calendar_id  varchar(512) default '',           
	            user_id int unsigned NOT NULL,
	            mode varchar(128) default 'One-way',  
	            cache_content longtext NOT NULL default '',
	            cache_time int unsigned NOT NULL,               
                UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		// coupons
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_coupons (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            services varchar(512) default '',
	            date_range varchar(256) default '',
	            used int unsigned NOT NULL default 0,
	            amount_fixed int unsigned NOT NULL default 0,
	            amount_percentage int unsigned NOT NULL default 0,           
	            maximum int unsigned default NULL,
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);

	}
	// drop tables
	static function dropTables() {
		global $wpdb;
		$wpdb->query( 'DROP TABLE IF EXISTS wbk_services' );
	  	$wpdb->query( 'DROP TABLE IF EXISTS wbk_appointments' );
	  	$wpdb->query( 'DROP TABLE IF EXISTS wbk_locked_time_slots' );
		$wpdb->query( 'DROP TABLE IF EXISTS wbk_days_on_off' );
		$wpdb->query( 'DROP TABLE IF EXISTS wbk_email_templates' );
		$wpdb->query( 'DROP TABLE IF EXISTS wbk_gg_calendars' );	
	}
	// add fields used since 1.2.0
	static function update_1_2_0(){
		global $wpdb; 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'form' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `form` int unsigned NOT NULL default 0");
		}
 	}
	// add fields used since 1.3.0
	static function update_1_3_0(){
		global $wpdb; 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'quantity' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `quantity` int unsigned NOT NULL default 1");
		}
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'quantity' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `quantity` int unsigned NOT NULL default 1");
		}
 	}
	// add fields used since 3.0.0
	static function update_3_0_0(){
		global $wpdb; 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'price' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `price` FLOAT NOT NULL DEFAULT '0'");
		}
	 	$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'payment_methods' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `payment_methods` varchar(255) NOT NULL DEFAULT ''");
		}
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'status' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `status`  varchar(255) NOT NULL DEFAULT 'pending'");
		}
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'payed' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `payed` FLOAT NOT NULL DEFAULT 0");
		}
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'payment_id' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `payment_id` varchar(255) NOT NULL DEFAULT ''");
		}
 	}
 	// add tables and fields used since 3.0.3
	static function update_3_0_3(){
		global $wpdb;
		// email templates table
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_email_templates (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            template varchar(2000) default '',
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'notification_template' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `notification_template` int unsigned NOT NULL default 0");
		}		
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'reminder_template' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `reminder_template` int unsigned NOT NULL default 0");
		}	
	}
 	// add fields used since 3.0.15
	static function update_3_0_15(){
		global $wpdb;
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'prepare_time' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `prepare_time` int unsigned NOT NULL default 0");
		}
		self::createHtFile();
	}
	// add tables and fields used since 3.1.0
	static function update_3_1_0(){
		global $wpdb;	 
		if( get_option( 'wbk_3_1_0_upd', '' ) == 'done' ){
			return;
		}
		// create service category table
	   	$wpdb->query(
		        "CREATE TABLE IF NOT EXISTS wbk_service_categories(
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            category_list varchar(512) default '',
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		// add token and created_on fields into wbk_appointments
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'token' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `token` varchar(255) NOT NULL DEFAULT ''");
		}
		// add payment cancel tokend
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'payment_cancel_token' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `payment_cancel_token` varchar(255) NOT NULL DEFAULT''");
		}
		// add transaction started 
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'expiration_time' ){
				$found = true;
			}
		}
		if ( !$found ){
			$wpdb->query("ALTER TABLE `wbk_appointments` ADD `expiration_time` int unsigned NOT NULL default 0");
		}
		// extends description field
		$wpdb->query("ALTER TABLE `wbk_appointments` CHANGE `description` `description` VARCHAR(1024) NOT NULL DEFAULT ''");
		// add triggers
		if ( $wpdb->query("DROP TRIGGER IF EXISTS before_insert_wbk_appointments") ){
			$wpdb->query("CREATE TRIGGER before_insert_wbk_appointments
				BEFORE INSERT ON wbk_appointments 
	  			FOR EACH ROW
	  			SET new.token =  MD5(UUID_SHORT())");
		}
		$wpdb->update( 
			'wbk_appointments', 
			array( 'status' => 'approved' ), 
			array( 'status' => 'pending' ), 
			array( '%s' ), 
			array( '%s' ) 
		);
		$wpdb->update( 
			'wbk_appointments', 
			array( 'status' => 'paid_approved' ), 
			array( 'status' => 'paid' ), 
			array( '%s' ), 
			array( '%s' ) 
		);
		add_option( 'wbk_3_1_0_upd', 'done' );
		update_option( 'wbk_3_1_0_upd', 'done' );
	}
	// add fields used since 3.1.21
	static function update_3_1_21(){
		global $wpdb;	 
		if( get_option( 'wbk_3_1_21_upd', '' ) == 'done' ){
			return;
		}
		 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'date_range' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `date_range` varchar(128) NOT NULL DEFAULT ''");
		}
 
		add_option( 'wbk_3_1_21_upd', 'done' );
		update_option( 'wbk_3_1_21_upd', 'done' );
	}
	// update db structure according to 3.1.6
	static function update_3_1_6(){
		global $wpdb;	 
		if( get_option( 'wbk_3_1_6_upd', '' ) == 'done' ){
			return;
		}
		// extends email templates field
		$wpdb->query("ALTER TABLE `wbk_email_templates` CHANGE `template` `template` VARCHAR(20000) NOT NULL DEFAULT ''");
		add_option( 'wbk_3_1_6_upd', 'done' );
		update_option( 'wbk_3_1_6_upd', 'done' );
	}
	static function update_3_1_27(){
		global $wpdb;	 
		if( get_option( 'wbk_3_1_27_upd', '' ) == 'done' ){
			return;
		}	
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'gg_calendars' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `gg_calendars` varchar(512) NOT NULL DEFAULT ''");
		}
		add_option( 'wbk_3_1_27_upd', 'done' );
		update_option( 'wbk_3_1_27_upd', 'done' );
	}
	static function update_3_1_31(){
		global $wpdb;	 
		if( get_option( 'wbk_3_1_31_upd', '' ) == 'done' ){
			return;
		}	 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'invoice_template' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `invoice_template` int unsigned NOT NULL default 0");
		}
		add_option( 'wbk_3_1_31_upd', 'done' );
		update_option( 'wbk_3_1_31_upd', 'done' );
	}
	//update db structure to version 3.2.0
	static function update_3_2_0(){
		global $wpdb;	 
		if( get_option( 'wbk_3_2_0_upd', '' ) == 'done' ){
			return;
		}	
		// google calendar
	   	$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_gg_calendars (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            access_token varchar(512) default '',
	            calendar_id  varchar(512) default '',           
	            user_id int unsigned NOT NULL,
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);		
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'gg_calendars' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_services` ADD `gg_calendars` varchar(512) NOT NULL DEFAULT ''");
		}
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'gg_event_id' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `gg_event_id` varchar(512) NOT NULL DEFAULT ''");
		}
		add_option( 'wbk_3_2_0_upd', 'done' );
		update_option( 'wbk_3_2_0_upd', 'done' );
	}
	//update db structure to version 3.2.2
	static function update_3_2_2(){
		global $wpdb;	 
		if( get_option( 'wbk_3_2_2_upd', '' ) == 'done' ){
			return;
		}	
		 
		$table_name = 'wbk_locked_time_slots';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'connected_id' ){
				$found = true;
			}
		}
		if ( !$found ){
			 $wpdb->query("ALTER TABLE `wbk_locked_time_slots` ADD `connected_id` int unsigned NOT NULL default 0");
		}
		add_option( 'wbk_3_2_2_upd', 'done' );
		update_option( 'wbk_3_2_2_upd', 'done' );
	}
	//update db structure to version 3.2.3
	static function update_3_2_3(){
		global $wpdb;	 
		if( get_option( 'wbk_3_2_3_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'admin_token' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD `admin_token` varchar(255) NOT NULL DEFAULT ''");
		}
		add_option( 'wbk_3_2_3_upd', 'done' );
		update_option( 'wbk_3_2_3_upd', 'done' );
	}
	// update db structure to version 3.2.16
	static function update_3_2_16(){
		global $wpdb;
		if( get_option( 'wbk_3_2_16_upd', '' ) == 'done' ){
			return;
		}			 
		$wpdb->query("ALTER TABLE `wbk_services` CHANGE `description` `description` varchar(1024) default ''");	
		add_option( 'wbk_install_cn', time() );	
		add_option( 'wbk_3_2_16_upd', 'done' );
		update_option( 'wbk_3_2_16_upd', 'done' );
	}
	// update db structure to version 3.2.18
	static function update_3_2_18(){
		global $wpdb;	 
		if( get_option( 'wbk_3_2_18_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'time_offset' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD  time_offset int  NOT NULL default 0");
		}
		add_option( 'wbk_3_2_18_upd', 'done' );
		update_option( 'wbk_3_2_18_upd', 'done' );
	}
	// update db structure to version 3.2.21
	static function update_3_2_21(){
		global $wpdb;	 
		if( get_option( 'wbk_3_2_21_upd', '' ) == 'done' ){
			return;
		}			 
		$wpdb->query("ALTER TABLE `wbk_appointments` CHANGE `attachment` `attachment` VARCHAR(1024) NOT NULL DEFAULT ''");
		add_option( 'wbk_3_2_21_upd', 'done' );
		update_option( 'wbk_3_2_21_upd', 'done' );
	}
	// update db structure to version 3.3.7
	static function update_3_3_7(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_7_upd', '' ) == 'done' ){
			return;
		}			 
		$wpdb->query(
	        "CREATE TABLE IF NOT EXISTS wbk_coupons (
	            id int unsigned NOT NULL auto_increment PRIMARY KEY,
	            name varchar(128) default '',
	            services varchar(512) default '',
	            date_range varchar(256) default '',
	            used int unsigned NOT NULL default 0,
	            amount_fixed int unsigned NOT NULL default 0,
	            amount_percentage int unsigned NOT NULL default 0,           
	            maximum int unsigned default NULL,
	            UNIQUE KEY id (id)
	        ) 
	        DEFAULT CHARACTER SET = utf8
	        COLLATE = utf8_general_ci"
		);
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'coupon' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD coupon int NOT NULL default 0");
		}

		add_option( 'wbk_3_3_7_upd', 'done' );
		update_option( 'wbk_3_3_7_upd', 'done' );
	}
	// update db structure to version 3.3.7.1
	static function update_3_3_7_1(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_7_1_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'multi_mode_limit' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_services` ADD multi_mode_limit varchar(128) NOT NULL default ''");
		}  
		add_option( 'wbk_3_3_7_1_upd', 'done' );
		update_option( 'wbk_3_3_7_1_upd', 'done' );
	}
	// update db structure to version 3.3.9
	static function update_3_3_9(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_9_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_gg_calendars';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'mode' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_gg_calendars` ADD mode varchar(128) default 'One-way'");
		}  
		add_option( 'wbk_3_3_9_upd', 'done' );
		update_option( 'wbk_3_3_9_upd', 'done' );
	}
	// update db structure to version 3.2.12+
	static function update_3_3_12(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_12_upd', '' ) == 'done' ){
			return;
		}			 
		$wpdb->query("ALTER TABLE `wbk_appointments` CHANGE `extra` `extra` VARCHAR(2048) NOT NULL DEFAULT ''");
		add_option( 'wbk_3_3_12_upd', 'done' );
		update_option( 'wbk_3_3_12_upd', 'done' );
	}
	// update db structure to version 3.2.14
	static function update_3_3_14(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_14_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'payment_method' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD payment_method varchar(255) default ''");
		}  
		add_option( 'wbk_3_3_14_upd', 'done' );
		update_option( 'wbk_3_3_14_upd', 'done' );
	}
	// update db structure to version 3.2.14(1)
	static function update_3_3_14_1(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_14_1_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_gg_calendars';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'cache' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_gg_calendars` ADD cache_content longtext NOT NULL default ''");
			 $wpdb->query("ALTER TABLE `wbk_gg_calendars` ADD cache_time int unsigned NOT NULL default 0");
		}  
		add_option( 'wbk_3_3_14_1_upd', 'done' );
		update_option( 'wbk_3_3_14_1_upd', 'done' );
	}
 	// get services  
	static function getServices() {
	 	global $wpdb;
		$order_type = get_option( 'wbk_order_service_by', 'a-z' );
		if(	$order_type == 'a-z' ){
			$result = $wpdb->get_col( "SELECT id FROM wbk_services order by name asc" );
		} elseif ( $order_type == 'priority') {
			$result = $wpdb->get_col( "SELECT id FROM wbk_services order by priority desc" );		
		}
		return $result;
	}
	// update db structure to version 3.3.14.2
	static function update_3_3_14_2(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_14_2_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'multi_mode_low_limit' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_services` ADD multi_mode_low_limit varchar(128) NOT NULL default ''");
		}  
		add_option( 'wbk_3_3_14_2_upd', 'done' );
		update_option( 'wbk_3_3_14_2_upd', 'done' );
	}
	// update db structure to version 3.3.18
	static function update_3_3_18(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_18_upd', '' ) == 'done' ){
			return;
		}			 
		$wpdb->query("ALTER TABLE `wbk_appointments` CHANGE `extra` `extra` LONGTEXT NOT NULL DEFAULT ''");
		add_option( 'wbk_3_3_18_upd', 'done' );
		update_option( 'wbk_3_3_18_upd', 'done' );
	}
	// update db structure to version 3.4.0
	static function update_3_3_31(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_31_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'actual_duration' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD actual_duration int unsigned NOT NULL default 0");
		}  
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'created_on' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD created_on int unsigned NOT NULL default 0");
		}  
		add_option( 'wbk_3_3_31_upd', 'done' );
		update_option( 'wbk_3_3_31_upd', 'done' );
	}

	// update db structure to version 3.3.37
	static function update_3_3_37(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_37_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_services';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'priority' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_services` ADD priority int NOT NULL default 0");
		}  
		add_option( 'wbk_3_3_37_upd', 'done' );
		update_option( 'wbk_3_3_37_upd', 'done' );
	}
	// update db structure to version 3.3.41
	static function update_3_3_41(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_41_upd', '' ) == 'done' ){
			return;
		}			 
		$app_ids = $wpdb->get_col( 'SELECT id from wbk_appointments' );	
		foreach ( $app_ids as $app_id) {		
			$extra_data = $wpdb->get_var( $wpdb->prepare( " SELECT extra FROM wbk_appointments WHERE id = %d ", $app_id ) );	
			if( $extra_data == '' ){
				continue;
			}	  
			$extra_data_ids = explode( '###', $extra_data );
			$extras = array();
			foreach( $extra_data_ids as $extra_id ){
				if( trim( $extra_id ) == '' ){
					continue;
				}
				$value_pair = explode(':', $extra_id );
				if( count( $value_pair ) != 2 ){
					continue;
				}		
				$field_id = trim( $value_pair[0] );
				$field_id_label = $field_id;

			 
				$field_id_label = explode( ']', $field_id_label );
				if( count( $field_id_label ) != 2 ){
					continue;
				}
				$field_id_label = $field_id_label[1];
				$matches = array();
				preg_match( "/\[[^\]]*\]/", $field_id, $matches);
				$field_id = trim( $matches[0], '[]' );				
				
				$custom_field_value =  $value_pair[1];
				
				$custom_field_value = str_replace( '{colon}', ':', $custom_field_value );

				$extra = array();
				$extra[] = trim( $field_id );
				$extra[] = trim( $field_id_label );
				$extra[] = trim( $custom_field_value );		
				$extras[] = $extra;					 				
			}
			$extras = json_encode( $extras );
			 
			$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'extra' => $extras ), 
						array( 'id' => $app_id ), 
						array( '%s' ), 
						array( '%d' ) 
					);
			;
		}	
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'paid_amount' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD paid_amount varchar(128) default ''");
		}  
		$found = false;

		add_option( 'wbk_3_3_41_upd', 'done' );
		update_option( 'wbk_3_3_41_upd', 'done' );
	}
	// update db structure to version 3.3.42
	static function update_3_3_42(){
		global $wpdb;	 
		if( get_option( 'wbk_3_3_42_upd', '' ) == 'done' ){
			return;
		}			 
		$table_name = 'wbk_appointments';
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'lang' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD lang varchar(255) default ''");
		}
		$found = false;
		foreach ( $wpdb->get_col( "DESC " . $table_name, 0 ) as $column_name ) {
			if ( $column_name == 'moment_price' ){
				$found = true;
			}
		}
		if ( !$found ){ 
			 $wpdb->query("ALTER TABLE `wbk_appointments` ADD moment_price varchar(255) default ''");
		}  

		add_option( 'wbk_3_3_42_upd', 'done' );
		update_option( 'wbk_3_3_42_upd', 'done' );
	}
	// get services with same category
	static function getServicesWithSameCategory( $service_id ) {
	 	global $wpdb;
	 	$result = array();
	 	$categories = self::getServiceCategoryList();
	 	foreach ( $categories as $key => $value) {
	 		$services = self::getServicesInCategory( $key );
	 		if( in_array( $service_id, $services)){
		 	 	foreach($services as $current_service ) {
		 	 		if( $current_service != $service_id){
		 	 			$result[] = $current_service;
		 	 		}
		 	 	}
	 		}
	 	}
	 	$result = array_unique( $result );	 	 
		return $result;
	}
	// get service category list
	static function getServiceCategoryList(){
		global $wpdb;
		$categories = $wpdb->get_col( "SELECT id FROM wbk_service_categories" );
		$result = array();
		foreach( $categories as $category_id ) {
			$name =  $wpdb->get_var( $wpdb->prepare( " SELECT name FROM wbk_service_categories WHERE id = %d", $category_id ) );
			$result[ $category_id ] = $name;
		}
		return $result;
	}
	// get service category list
	static function getServicesInCategory( $category_id ){
		global $wpdb;
		$list =  $wpdb->get_var( $wpdb->prepare( " SELECT category_list FROM wbk_service_categories WHERE id = %d", $category_id ) );
		if( $list == '' ){
			return FALSE;
		} 
		return explode( ',', $list );
	}	
	// get category names by service
	static function getCategoryNamesByService( $service_id ){
		$categories = self::getServiceCategoryList();
		$result = array();
		foreach ( $categories as $key => $value ) {
			$services = self::getServicesInCategory( $key );
			if(  is_array( $services ) ){
				if( in_array( $service_id, $services ) ){
					$result[] = $value;
				}
			}
		}
		if( count( $result ) > 0 ){
			return implode( ', ', $result );
		} else {
			return '';
		}
	}
	// get not-admin users
	static function getNotAdminUsers() {
		$arr_users = array();
		$arr_temp = get_users( array( 'role__not_in' => array( 'subscriber', 'administrator'), 'fields' => 'user_login' ) );
		if ( count( $arr_temp ) > 0 ) {
			array_push( $arr_users, $arr_temp );  
		}
	 	return $arr_users;
	}	
	// get admin users
	static function getAdminUsers() {
		$arr_users = array();
		array_push( $arr_users, get_users( array( 'role' => 'administrator', 'fields' => 'user_login' ) ) );  
	 	return $arr_users;
	}	
	// check if service name is free
	static function isServiceNameFree( $value ) {
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( " SELECT COUNT(*) FROM wbk_services WHERE name = %s ", $value ) );
		if ( $count > 0 ){
			return false;
		} else {
			return true;
		}
	}
	// get CF7 forms
	static function getCF7Forms() {
		$args = array( 'post_type' => 'wpcf7_contact_form', 'posts_per_page' => -1 );
		$result = array();
		if( $cf7Forms = get_posts( $args ) ) {
			foreach( $cf7Forms as $cf7Form ) {
				$form = new stdClass();
				$form ->name = $cf7Form->post_title;
				$form->id = $cf7Form->ID;
				array_push( $result, $form );
			}
		}	
		return $result;	
	}
	// get service id by appointment id
	static function getServiceIdByAppointmentId( $appointment_id ){
		global $wpdb;
		$service_id = $wpdb->get_var( $wpdb->prepare( " SELECT service_id FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		if ( $service_id == null ){
			return false;
		} else {
			return $service_id;
		}
	}
	// get status by appointment id
	static function getStatusByAppointmentId( $appointment_id ){
		global $wpdb;
		$value = $wpdb->get_var( $wpdb->prepare( " SELECT status FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		if ( $value == null ){
			return false;
		} else {
			return $value;
		}
	}
	// get appointment id by tokend
	static function getAppointmentIdByToken( $token ){
		global $wpdb;	
		$appointment_id = $wpdb->get_var( $wpdb->prepare( " SELECT id FROM wbk_appointments WHERE token = %s ", $token ) );
		if ( $appointment_id == null ){
			return false;
		} else {
			return $appointment_id;
		}
	}
	// get category name by category id
	static function getCategoryNameByCategoryId( $category_id ){
		global $wpdb;	
		$category_name = $wpdb->get_var( $wpdb->prepare( " SELECT name FROM wbk_service_categories WHERE id = %d ", $category_id ) );
		if ( $category_name == null ){
			return false;
		} else {
			return $category_name;
		}
	}
	// get appointment id by admin tokend
	static function getAppointmentIdByAdminToken( $token ){
		global $wpdb;	
		$appointment_id = $wpdb->get_var( $wpdb->prepare( " SELECT id FROM wbk_appointments WHERE admin_token = %s ", $token ) );
		if ( $appointment_id == null ){
			return false;
		} else {
			return $appointment_id;
		}
	}
	// get tokend by appointment id
	static function getTokenByAppointmentId( $appointment_id ){
		global $wpdb;	
		$token = $wpdb->get_var( $wpdb->prepare( " SELECT token FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		if ( $token == null ){
			$token = uniqid();
 			$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'token' => $token ), 
						array( 'id' => $appointment_id), 
						array( '%s' ), 
						array( '%d' ) 
					);
 			return $token;
		} else {
			return $token;
		}
	}
	// get tokend by appointment id
	static function getAdminTokenByAppointmentId( $appointment_id ){
		global $wpdb;	
		$token = $wpdb->get_var( $wpdb->prepare( " SELECT admin_token FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		if ( $token == null ){
			$token = uniqid();
 			$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'admin_token' => $token ), 
						array( 'id' => $appointment_id), 
						array( '%s' ), 
						array( '%d' ) 
					);
 			return $token;
		} else {
			return $token;
		}
	}
	// get quantity by appointment id
	static function getQuantityByAppointmentId( $appointment_id ){
		global $wpdb;	
		$value = $wpdb->get_var( $wpdb->prepare( " SELECT quantity FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		if ( $value == null ){
			return false;
		} else {
			return $value;
		}
	}
	// get tomorrow appointments for the service
	static function getTomorrowAppointmentsForService( $service_id ) {
	 	global $wpdb;
	 	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$tomorrow = strtotime('tomorrow');
		$result = $wpdb->get_col( $wpdb->prepare( " SELECT id FROM wbk_appointments WHERE service_id=%d AND day=%d  ORDER BY time ", $service_id, $tomorrow  ) );
		date_default_timezone_set( 'UTC' );
		return $result;
	}
	// get future appointments for the service
	static function getFutureAppointmentsForService( $service_id, $days ) {
	 	global $wpdb;
	 	date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$tomorrow = strtotime('today + ' . $days . ' days');		 
		$result = $wpdb->get_col( $wpdb->prepare( " SELECT id FROM wbk_appointments WHERE service_id=%d AND day=%d  ORDER BY time ", $service_id, $tomorrow  ) );
		date_default_timezone_set( 'UTC' );
		return $result;
	}
 	// lock appointments of others services
	static function lockTimeSlotsOfOthersServices( $service_id, $appointment_id ){
		global $wpdb;
		// getting data about booked service 
		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			return FALSE;
		}
		if ( !$service->load() ) {
 			return FALSE;
		}
		$appointment = new WBK_Appointment();
		if ( !$appointment->setId( $appointment_id ) ) {
			return FALSE;
		}
		if ( !$appointment->load() ) {
 			return FALSE;
		}
		$start = $appointment->getTime();
		$end = $start + $appointment->getDuration() * 60 + $service->getInterval() * 60;
		// iteration over others services
		$autolock_mode = get_option( 'wbk_appointments_auto_lock_mode', 'all' );
		if( $autolock_mode == 'all' ){
			$arrIds = WBK_Db_Utils::getServices();
		} elseif( $autolock_mode == 'categories') {
			$arrIds = WBK_Db_Utils::getServicesWithSameCategory( $service_id );
	 	}
	 	if ( count( $arrIds ) < 1 ) {
	 		return TRUE;
	 	} 
	 	foreach ( $arrIds as $service_id_this ) {
 
	 		if ( $service_id == $service_id_this ){
	 			continue;
	 		}
	 		$service = new WBK_Service();
			if ( !$service->setId( $service_id_this ) ) {
				continue;
			}
			if ( !$service->load() ) {
	 			continue;
			}
			if( $service->getQuantity() > 1 &&  get_option( 'wbk_appointments_auto_lock_group', 'lock' ) == 'reduce' ){
				continue;
			}

			if( get_option( 'wbk_appointments_auto_lock_allow_unlock', 'allow' ) == 'disallow' /*&&  $service->getQuantity() == 1 */ ){
				continue;
			}

			$service_schedule = new WBK_Service_Schedule();
 			$service_schedule->setServiceId( $service_id_this );
 			$service_schedule->load();
 			$midnight = strtotime('today', $start );
 			$service_schedule->buildSchedule( $midnight, true, true );
		 	$this_duration = $service->getDuration() * 60  + $service->getInterval() * 60; 
			$timeslots_to_lock = $service_schedule->getNotBookedTimeSlots();

			 

			foreach ( $timeslots_to_lock as $time_slot_start ) {
				$cur_start = $time_slot_start;
				$cur_end = $time_slot_start + $this_duration;
			 	$intersect = false;
				if ( $cur_start == $start ){
					$intersect = true;					
				}
				if ( $cur_start > $start && $cur_start < $end ){
					$intersect = true;					
				}
				if ( $cur_end > $start && $cur_end <= $end  ){
					$intersect = true;					
				}
				if( $cur_start <= $start && $cur_end >= $end ){
					$intersect = true;
				}
				 
				if( $intersect == true ) {					
					if ( $wpdb->query( $wpdb->prepare( "DELETE FROM wbk_locked_time_slots WHERE time = %d and service_id = %d",  $time_slot_start, $service_id_this ) ) === false ){
						echo -1;
						die();
						return;
					}
					if ( $wpdb->insert( 'wbk_locked_time_slots', array( 'service_id' => $service_id_this, 'time' => $time_slot_start, 'connected_id' => $appointment_id ), array( '%d', '%d', '%d' ) ) === false ){
						echo -1;
						die();
						return;
					}			  				 
				}
 			}
	 	}
	}	
	// remove lock when appointment cancelled
	static function freeLockedTimeSlot( $appointment_id ){
		global $wpdb;
		$wpdb->query( $wpdb->prepare( "DELETE FROM wbk_locked_time_slots WHERE connected_id = %d",  $appointment_id ) );	
	}
	// set payment if for appointment()
	static function setPaymentId( $appointment_id, $payment_id ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'payment_id' => $payment_id ), 
						array( 'id' => $appointment_id), 
						array( '%s' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}
	}	
	// set payment if for appointment
	static function setPaymentCancelToken( $appointment_id, $cancel_token ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'payment_cancel_token' => $cancel_token ), 
						array( 'id' => $appointment_id ), 
						array( '%s' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}
	}	
	// get google event data for appointment
	static function getGoogleEventsData( $appointment_id, $event_data ){
 		global $wpdb;
 		$event_id_json = $wpdb->get_var( $wpdb->prepare( "SELECT gg_event_id FROM wbk_appointments WHERE id = %d", $appointment_id ) );
		if( $event_id_json == '' ){
			return array();
		}
		return json_decode( $event_id_json );
	}
	// check if google event id added
	static function idEventAddedToGoogle( $appointment_id ){
 	
		return TRUE;
	}
	// set google event data for appointment
	static function setGoogleEventsData( $appointment_id, $event_data ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'gg_event_id' => $event_data ), 
						array( 'id' => $appointment_id ), 
						array( '%s' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}
	}	

	// get amount by payment id 
	static function getAmountByPaymentId( $payment_id ){
		global $wpdb;
		if ( $payment_id == '' || !isset( $payment_id) ){
			return FALSE;
		}
		$quantity = $wpdb->get_var( $wpdb->prepare( "SELECT SUM(quantity) FROM wbk_appointments WHERE payment_id = %s", $payment_id ) );
		if ( $quantity == null ){
			return FALSE;
		}  
		$appointment_id = $wpdb->get_var( $wpdb->prepare( "SELECT id FROM wbk_appointments WHERE payment_id = %s", $payment_id ) );
		if ( $appointment_id == null ){
			return FALSE;
		}
		$service_id = WBK_Db_Utils::getServiceIdByAppointmentId( $appointment_id );
		$price = $wpdb->get_var( $wpdb->prepare( "SELECT price FROM wbk_services WHERE id = %d", $service_id ) );
		if ( $appointment_id == null ){
			return FALSE;
		}
		return array( $price, $quantity );
	}
	// update payment status
	static function updatePaymentStatus( $payment_id, $amount ){
		global $wpdb;	
		$result_pending = $wpdb->update( 
						'wbk_appointments', 
						array( 'status' => 'paid' ), 
						array( 'payment_id' => $payment_id, 'status' => 'pending' ), 
						array( '%s' ), 
						array( '%s', '%s' ) 
					);
		$result_approved = $wpdb->update( 
						'wbk_appointments', 
						array( 'status' => 'paid_approved' ), 
						array( 'payment_id' => $payment_id, 'status' => 'approved' ), 
						array( '%s' ), 
						array( '%s', '%s' ) 
					);
		if( ( $result_pending == false || $result_pending == 0 ) && ( $result_approved == false || $result_approved == 0 ) ){
			return FALSE;
		} else {

			$app_ids =  self::getAppointmentIdsByPaymentId( $payment_id );
			if( count( $app_ids ) > 0 ){
				wbk_email_processing_send_on_payment( $app_ids );
				WBK_Db_Utils::increeaseCouponUsage( $app_ids[0] );
				 
			}
			return TRUE;
		}
	}
	// update payment status
	static function updatePaymentStatusByIds( $app_ids ){
		foreach( $app_ids as $app_id ){
			global $wpdb;	
			$result_pending = $wpdb->update( 
							'wbk_appointments', 
							array( 'status' => 'paid' ), 
							array( 'id' => $app_id, 'status' => 'pending' ), 
							array( '%s' ), 
							array( '%d', '%s' ) 
						);
			$result_approved = $wpdb->update( 
							'wbk_appointments', 
							array( 'status' => 'paid_approved' ), 
							array( 'id' => $app_id, 'status' => 'approved' ), 
							array( '%s' ), 
							array( '%d', '%s' ) 
						);
		}
	}
	// update appointment status
	static function updateAppointmentStatus( $appointment_id, $status ){
		global $wpdb;	
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'status' => $status ), 
						array( 'id' => $appointment_id ), 
						array( '%s' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}
	}
	// get indexed names  
	static function getIndexedNames( $table ) {
	 	global $wpdb;
	 	$table = self::wbk_sanitize( $table );
		$result = $wpdb->get_results( "SELECT id, name from $table" );
		return $result;
	}  
	// get calenadrs related to user 
	static function getGgCalendarsByUser( $user_id ){
		global $wpdb;
	 	 
		$result = $wpdb->get_results( $wpdb->prepare(  "SELECT id, name from wbk_gg_calendars WHERE user_id = %d ", $user_id  ) );
		return $result;
	}
	static function getEmailTemplate( $id ){
		global $wpdb;
		$result =  $wpdb->get_var( $wpdb->prepare( " SELECT template FROM wbk_email_templates WHERE id = %d ", $id ) ); 
		return $result;
	}
	// $appointment_id provided to get the date and include in free results
	static function getFreeTimeslotsArray( $appointment_id ){
		$result = false;
		if( !is_numeric( $appointment_id ) ){
	        return $result;
	    }
	    $service_id = self::getServiceIdByAppointmentId( $appointment_id );
	    $service_schedule = new WBK_Service_Schedule();
	    if ( !$service_schedule->setServiceId( $service_id ) ){
	        return $result;
	    }
	    if ( !$service_schedule->load() ){
	        return $result;
	    }
	    $appointment = new WBK_Appointment();
		if ( !$appointment->setId( $appointment_id ) ) {
			return $result;
		}
		if ( !$appointment->load() ) {
 			return $result;
		}
	    $midnight = $appointment->getDay();
	    $day_status =  $service_schedule->getDayStatus( $midnight );
	    if ( $day_status == 0 ) {
	    	return $result;
	    }
	    $service_schedule->buildSchedule( $midnight );
	    $result = $service_schedule->getFreeTimeslotsPlusGivenAppointment( $appointment_id );
	    return $result;
	}
	// return blank array
	static function blankArray(){
		return array();
	}
	// create export file
	static function createHtFile(){
		$path =  __DIR__ . DIRECTORY_SEPARATOR . '..'.DIRECTORY_SEPARATOR . 'backend' . DIRECTORY_SEPARATOR . 'export' . DIRECTORY_SEPARATOR . '.htaccess';
		$content = "RewriteEngine On" . "\r\n";
		$content .=  "RewriteCond %{HTTP_REFERER} !^". get_admin_url() . 'admin.php\?page\=wbk-appointments' . '.* [NC]' . "\r\n";
		$content .= "RewriteRule .* - [F]";
		file_put_contents ( $path, $content );
	}
	// appointment status list
	static function getAppointmentStatusList( $condition = null ){
		$result = array( 'pending' => 	   array ( __( 'Awaiting approval', 'wbk' ), ''),
						 'approved'	=>     array ( __( 'Approved', 'wbk' ) , ''),
						 'paid'	=> 		   array ( __( 'Paid (awaiting approval)', 'wbk' ),  ''),
						 'paid_approved'=> array ( __( 'Paid (approved)', 'wbk' ), ''),
						 'arrived'	=>     array ( __( 'Arrived', 'wbk' ), ''),
						 'woocommerce'	=> array ( __( 'Managed by WooCommerce', 'wbk' ), ''),

					   );
		return $result;
	}
	// gg calendar mode list
	static function getGGCalendarModeList( $condition = null ){
		$result = array( 'One-way'  => array ( __( 'One-way (export)', 'wbk' ), ''),
						 'One-way-import'  => array ( __( 'One-way (import)', 'wbk' ), ''),		
						 'Two-ways'	=> array ( __( 'Two-ways', 'wbk' )	, ''),
					   );
		return $result;
	}
	// delete appointment by email - token pair
	static function deleteAppointmentByEmailTokenPair( $email, $token ){
		global $wpdb;	
		$appointment_id = self::getAppointmentIdByToken( $token );
		if( $appointment_id != false ){
			self::deleteAppointmentDataAtGGCelendar( $appointment_id );   
		}
		$deleted_count =  $wpdb->delete( 'wbk_appointments', array( 'email' =>  $email, 'token' => $token ), array( '%s', '%s' ) );
		if ( $deleted_count > 0 ){
			return true;
		} else {
			return false;
		}
	}
	// clear payment is by token 
	static function clearPaymentIdByToken( $token ){
		global $wpdb;
		$wpdb->update( 
			'wbk_appointments', 
			array( 'payment_id' => '' ), 
			array( 'payment_cancel_token' => $token ), 
			array( '%s' ), 
			array( '%s' ) 
		);
	}
	// get app ids by payment_id
	static function getAppointmentIdsByPaymentId( $payment_id ){
		global $wpdb;
 		$app_ids = $wpdb->get_col( $wpdb->prepare( 'select id from wbk_appointments where payment_id = %s', $payment_id ) );
 		return $app_ids;
	}
	static function	setAppointmentsExpiration( $appointment_id ){	
		global $wpdb;
		$expiration_time = get_option( 'wbk_appointments_expiration_time', '60' );
		if( !is_numeric( $expiration_time ) ){
			return;
		}
		if( intval( $expiration_time ) < 10 ){
			return;
		}
		$expiration_value = time() + $expiration_time * 60;
		$wpdb->update( 
			'wbk_appointments', 
			array( 'expiration_time' => $expiration_value ), 
			array( 'id' => $appointment_id ), 
			array( '%d' ), 
			array( '%d' ) 
		);
	}
	static function deleteExpiredAppointments(){
		global $wpdb;
		$time = time();
		if( get_option( 'wbk_appointments_delete_not_paid_mode', 'disabled' ) != 'disabled' ){
			$delete_rule = get_option( 'wbk_appointments_delete_payment_started', 'skip' );
			if ( $delete_rule == 'skip' ){		
				$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM wbk_appointments where payment_id = '' and  ( status='pending' or status='approved'  ) and payment_method <> 'Pay on arrival' and payment_method <> 'Bank transfer' and  expiration_time <> 0 and expiration_time < %d", $time ) );
			} elseif ( $delete_rule == 'delete') {
				$ids = $wpdb->get_col( $wpdb->prepare( "SELECT id FROM wbk_appointments where ( status='pending' or status='approved'  ) and payment_method <> 'Pay on arrival' and payment_method <> 'Bank transfer' and expiration_time <> 0 and expiration_time < %d", $time ) );
			}
			foreach ($ids  as $appointment_id ) {
				self::deleteAppointmentDataAtGGCelendar( $appointment_id );			
			}	
			if ( $delete_rule == 'skip' ){
				$wpdb->query( $wpdb->prepare( "DELETE FROM wbk_appointments where payment_id = '' and payment_method <> 'Pay on arrival' and payment_method <> 'Bank transfer' and ( status='pending' or status='approved'  ) and  expiration_time <> 0 and expiration_time < %d", $time ) );
			} elseif ( $delete_rule == 'delete') {
				$wpdb->query( $wpdb->prepare( "DELETE FROM wbk_appointments where  ( status='pending' or status='approved'  ) and payment_method <> 'Pay on arrival' and payment_method <> 'Bank transfer' and expiration_time <> 0 and expiration_time < %d", $time ) );
			}
		}

		$pending_expiration = get_option( 'wbk_appointments_expiration_time_pending', 0 );
		 
		if( WBK_Validator::checkInteger( $pending_expiration, 5, 500000 ) ){
			$old_point = time() - $pending_expiration * 60;
		 
			$wpdb->query( $wpdb->prepare( "DELETE FROM wbk_appointments where  ( status='pending' )   and created_on  < %d", $old_point ) );
		}

		if( get_option( 'wbk_gdrp', 'disabled' ) == 'enabled' ){
			$wpdb->query( $wpdb->prepare( "DELETE FROM wbk_appointments WHERE time < %d", $time ) );
		}

	}
	static function getQuantityFromConnectedServices( $service_id, $start, $end ){
		if( get_option( 'wbk_appointments_auto_lock', 'disabled' ) == 'disabled' ){
			return 0;
		}
		$autolock_mode = get_option( 'wbk_appointments_auto_lock_mode', 'all' );
		$arrIds = array();
		if( $autolock_mode == 'all' ){
			$arrIds = WBK_Db_Utils::getServices();
		} elseif( $autolock_mode == 'categories') {
			$arrIds = WBK_Db_Utils::getServicesWithSameCategory( $service_id );
	 	}
	 	$total_quantity = 0;
	 	foreach ( $arrIds as $service_id_this) {
	 		if( $service_id_this == $service_id ){ 			
	 			continue;
	 		}
	 		$service_this = new WBK_Service();
			if ( !$service_this->setId( $service_id_this ) ) {
				continue;
			}
			if ( !$service_this->load() ) {
	 			continue;
			}
	 		$service_schedule = new WBK_Service_Schedule();
	 		if ( !$service_schedule->setServiceId( $service_id_this ) ){
	        	continue;
	    	}
	    	if ( !$service_schedule->load() ){
		        continue;
		    }
		    $midnight = strtotime('today', $start );
		    $service_schedule->buildSchedule( $midnight );
		    $timeslots = $service_schedule->getTimeSlots();
		    foreach ( $timeslots as $timeslot ) { 
			
				    $this_start = $timeslot->getStart();
			    	$this_end = $timeslot->getStart() + $service_this->getDuration() * 60 + $service_this->getInterval() * 60;
			    	$intersect = false;
					if ( $this_start == $start ){
						$intersect = true;					
					}
					if ( $this_start > $start && $this_start < $end ){
						$intersect = true;					
					}
					if ( $this_end > $start && $this_end <= $end  ){
						$intersect = true;					
					}
					if ( $intersect == true ){
						if( is_array( $timeslot->getStatus() ) ){
							foreach ( $timeslot->getStatus() as $this_app_id ) {
								$total_quantity += intval( self::getQuantityByAppointmentId( $this_app_id ) );
							}
						} elseif ( $timeslot->getStatus() > 0 ) {
							$total_quantity += intval( self::getQuantityByAppointmentId( $timeslot->getStatus() ) );
						}
					}
		    }
	 	}
	 	return $total_quantity;
	}
	static function getQuantityFromConnectedServices2( $service_id, $time ){
		if( get_option( 'wbk_appointments_auto_lock', 'disabled' ) == 'disabled' ){
			return 0;
		}
		$service = self::initServiceById( $service_id );
		$end = $time + $service->getDuration() * 60 + $service->getInterval() *60;
		
		$autolock_mode = get_option( 'wbk_appointments_auto_lock_mode', 'all' );
		$arrIds = array();
		if( $autolock_mode == 'all' ){
			$arrIds = WBK_Db_Utils::getServices();
		} elseif( $autolock_mode == 'categories') {
			$arrIds = WBK_Db_Utils::getServicesWithSameCategory( $service_id );
	 	}
	 	$total_quantity = 0;
	 	
	 	$day = strtotime( date( 'Y-m-d', $time ).' 00:00:00' );
	 	foreach ( $arrIds as $service_id_this) {
			if( $service_id_this == $service_id ){ 			
	 			continue;
	 		}
	 		$service_schedule = new WBK_Service_Schedule();
	 		if ( !$service_schedule->setServiceId( $service_id_this ) ){
	        	continue;
	    	}
	    	$service_this = self::initServiceById( $service_id_this );
	    	$service_schedule->parital_load1();
	    	$service_schedule->loadAppointmentsDay( $day );
	    	$appointments = $service_schedule->getAppointment();
		    foreach( $appointments as $appointment ){
		    	$start_cur = $appointment->getTime();
		    	$end_cur = $start_cur + $service_this->getDuration() * 60 + $service_this->getInterval() * 60;
		    	if( WBK_Date_Time_Utils:: chekRangeIntersect( $time, $end, $start_cur, $end_cur ) == TRUE ){
		    		$total_quantity += $appointment->getQuantity();
		    	}
		    }
	 		 
	 	}	 	
	 	return $total_quantity;
	}
	static function getFeatureAppointmentsByService( $service_id ){
		global $wpdb;
		$time = time();
		$app_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id from wbk_appointments where service_id = %d AND time > %d order by time asc", $service_id, $time ) );
	    return $app_ids;
	}
	static function getFeatureAppointmentsByCategory( $category_id ){
		global $wpdb;
		$time = time();
		$result = array();
		$service_ids =   self:: getServicesInCategory( $category_id );
		foreach( $service_ids as $service_id ) {
			$app_ids = $wpdb->get_col( $wpdb->prepare( "SELECT id from wbk_appointments where service_id = %d AND time > %d order by time asc", $service_id, $time ) );
			$result = array_merge( $result, $app_ids );
		}
	    return $result;
	}
	public static function booked_slot_placeholder_processing( $appointment_id ){
		$text = get_option ( 'wbk_booked_text', '' );
		$appointment = new WBK_Appointment();
		if ( !$appointment->setId( $appointment_id ) ) {
			return '';
		};
		if ( !$appointment->load() ) {
			return '';
		};
		$customer_name = $appointment->getName();
		$text = str_replace( '#username', $customer_name, $text );
		// time
		$text = str_replace( '#time', '', $text );
		return $text;	
	}
	public static function message_placeholder_processing_multi_service( $message, $appointment, $total_amount = null, $current_category = 0, $multi_token = null, $multi_token_admin = null ){	
		$service = self::initServiceById( self::getServiceIdByAppointmentId( $appointment->getId() ) );
		return self::message_placeholder_processing( $message, $appointment, $service, $total_amount, $current_category, $multi_token, $multi_token_admin);
	}	
	public static function message_placeholder_processing( $message, $appointment, $service, $total_amount = null, $current_category = 0, $multi_token = null, $multi_token_admin = null ){	
		global $wbk_wording;
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		// begin landing for payment and cancelation
		$payment_link_url = get_option( 'wbk_email_landing', '' );
		$payment_link_text = get_option( 'wbk_email_landing_text',  '' );
		if( $payment_link_text == '' ){
			$payment_link_text = sanitize_text_field( $wbk_wording['email_landing_anchor'] );
		}
		$cancel_link_text = get_option( 'wbk_email_landing_text_cancel', '' );
	    if( $cancel_link_text == '' ){
	        $cancel_link_text = sanitize_text_field( $wbk_wording['email_landing_anchor2'] );
		}
		$gg_add_link_text = get_option( 'wbk_email_landing_text_gg_event_add', __( 'Click here to add this event to your Google Calendar.', 'wbk' ) );
	    if( $gg_add_link_text == '' ){
	        $gg_add_link_text = sanitize_text_field( $wbk_wording['wbk_email_landing_text_gg_event_add'] );
		}
		$payment_link = '';
		$cancel_link = '';
		$gg_add_link = '';		
		if( $payment_link_url != '' ){
			if( $multi_token == null ){
				$token = WBK_Db_Utils::getTokenByAppointmentId( $appointment->getId() );
			} else {
				$token = $multi_token;
			}			
			if( $token != false ){
				$payment_link = '<a target="_blank" target="_blank" href="' . $payment_link_url . '?order_payment=' . $token . '">' . trim( $payment_link_text ) . '</a>';
			    $cancel_link = '<a target="_blank" target="_blank" href="' . $payment_link_url . '?cancelation=' . $token . '">' . trim( $cancel_link_text ) . '</a>';
			    $gg_add_link = '<a target="_blank" target="_blank" href="' . $payment_link_url . '?ggeventadd=' . $token . '">' . trim( $gg_add_link_text ) . '</a>';
			 }
		}   
		// end landing for payment
		// begin admin management links
		$admin_cancel_link = '';
		$admin_approve_link = '';
		$admin_cancel_link_text = get_option( 'wbk_email_landing_text_cancel_admin', __( 'Click here to cancel this booking.', 'wbk' ) );
		$admin_approve_link_text =  get_option( 'wbk_email_landing_text_approve_admin', __( 'Click here to approve this booking.', 'wbk' ) );	
		if( get_option( 'wbk_allow_manage_by_link', 'no' ) == 'yes' ){
			if( $payment_link_url != '' ){	
				if( $multi_token_admin == null ){
					$token = WBK_Db_Utils::getAdminTokenByAppointmentId( $appointment->getId() );
				} else {
					$token = $multi_token_admin;
				}							
				if( $token != false ){
					$admin_cancel_link = '<a target="_blank" target="_blank" href="' . $payment_link_url . '?admin_cancel=' . $token . '">' . trim( $admin_cancel_link_text ) . '</a>';
				    $admin_approve_link = '<a target="_blank" target="_blank" href="' . $payment_link_url . '?admin_approve=' . $token . '">' . trim( $admin_approve_link_text ) . '</a>';
				}
			}
		}	


		// end admin management links
		// begin total amount
		if( is_null( $total_amount ) ){
			$total_price = '';
			$payment_methods = explode( ';', $service->getPayementMethods() );
			if( count( $payment_methods )  > 0 ){
				$total = $appointment->getQuantity() * $service->getPrice();
				$price_format = get_option( 'wbk_payment_price_format', '$#price' );
				$tax_rule = get_option( 'wbk_tax_for_messages', 'paypal' );
				if( $tax_rule == 'paypal' ){
					$tax = get_option( 'wbk_paypal_tax', 0 );			
				}
				if( $tax_rule == 'stripe' ){
					$tax = get_option( 'wbk_stripe_tax', 0 );			
				}
				if( $tax_rule == 'none' ){
					$tax = 0;			
				}
	 			if( is_numeric( $tax ) && $tax > 0 ){
					$tax_amount = ( ( $total ) / 100 ) * $tax;
				    	$total = $total + $tax_amount;
					} 
				$total_price =  str_replace( '#price', number_format( $total, get_option( 'wbk_price_fractional', '2' ) ), $price_format );
			}
		}
		// end total amount
		// beging extra data
		$extra_data = trim( $appointment->getExtra() );
		if( $extra_data != '' ){
			$extra = json_decode( $extra_data );
			foreach( $extra as $item ){
	    		if( count( $item ) <> 3 ){
	    			continue;    			
	    		}
	    		$custom_placeholder = '#field_' . $item[0];
	    		$message = str_replace( $custom_placeholder, $item[2], $message );
			}		
		}	 
		// end extra data			 
		if( $current_category == 0 ){
			$current_category_name = '';
		} else {
			$current_category_name = WBK_Db_Utils::getCategoryNameByCategoryId( $current_category );
			if( $current_category_name == false  ){
				$current_category_name = '';
			}
		}
		$message = str_replace( '#cancel_link', $cancel_link, $message );		        	        
		$message = str_replace( '#payment_link', $payment_link, $message );	
		$message = str_replace( '#add_event_link', $gg_add_link, $message );	
		$message = str_replace( '#admin_cancel_link', $admin_cancel_link, $message );		        	        
		$message = str_replace( '#admin_approve_link', $admin_approve_link, $message );
		if( is_null( $total_amount ) ){
			$message = str_replace( '#total_amount', $total_price, $message );	
		} else {
			$message = str_replace( '#total_amount', $total_amount, $message );	
		}		
		$category_names = WBK_Db_Utils::getCategoryNamesByService( $service->getId() );
		$message = str_replace( '#category_names', $category_names, $message );
		$message = str_replace( '#current_category_name', $current_category_name, $message );
		$message = str_replace( '#service_name', $service->getName(), $message );
		$message = str_replace( '#customer_name', $appointment->getName(), $message );
		$message = str_replace( '#appointment_day', date_i18n( $date_format, $appointment->getDay() ), $message );
		$message = str_replace( '#appointment_time', date_i18n( $time_format, $appointment->getTime() ), $message );
		$message = str_replace( '#appointment_local_time', date_i18n( $time_format, $appointment->getLocalTime() ), $message );
		$message = str_replace( '#appointment_local_date', date_i18n( $date_format, $appointment->getLocalTime() ), $message );
		$message = str_replace( '#customer_phone', $appointment->getPhone(), $message );
		$message = str_replace( '#customer_email', $appointment->getEmail(), $message );
		$message = str_replace( '#customer_comment', $appointment->getDescription(), $message );
		$message = str_replace( '#items_count', $appointment->getQuantity(), $message );
		$message = str_replace( '#appointment_id', $appointment->getId(), $message );
		$message = str_replace( '#customer_custom', $appointment->getFormatedExtra(), $message );			
		$time_range = date_i18n( $time_format, $appointment->getTime() ) . ' - ' .  date_i18n( $time_format, $appointment->getTime() + $service->getDuration()*60 );
		$message = str_replace( '#time_range', $time_range , $message );
		return $message;
	}
	public static function subject_placeholder_processing_multi_service( $message, $appointment, $total_amount = null, $current_category = 0  ){
		$service = self::initServiceById( self::getServiceIdByAppointmentId( $appointment->getId() ) );
		return self::subject_placeholder_processing( $message, $appointment, $service, $total_amount, $current_category );
	}
	public static function subject_placeholder_processing( $message, $appointment, $service, $total_amount = null, $current_category = 0  ){
		global $wbk_wording;
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
	  
		// begin total amount
		if( is_null( $total_amount ) ){
			$total_price = '';
			$payment_methods = explode( ';', $service->getPayementMethods() );
			if( count( $payment_methods )  > 0 ){
				$total = $appointment->getQuantity() * $service->getPrice();
				$price_format = get_option( 'wbk_payment_price_format', '$#price' );
				$tax_rule = get_option( 'wbk_tax_for_messages', 'paypal' );
				if( $tax_rule == 'paypal' ){
					$tax = get_option( 'wbk_paypal_tax', 0 );			
				}
				if( $tax_rule == 'stripe' ){
					$tax = get_option( 'wbk_stripe_tax', 0 );			
				}
				if( $tax_rule == 'none' ){
					$tax = 0;			
				}
	 			if( is_numeric( $tax ) && $tax > 0 ){
					$tax_amount = ( ( $total ) / 100 ) * $tax;
				    	$total = $total + $tax_amount;
					} 
				$total_price =  str_replace( '#price', number_format( $total,  get_option( 'wbk_price_fractional', '2' ) ), $price_format );
			}
		}
		// end total amount

		// beging extra data
		$extra_data = trim( $appointment->getExtra() );
		if( $extra_data != '' ){
			$extra = json_decode( $extra_data );
			foreach( $extra as $item ){
	    		if( count( $item ) <> 3 ){
	    			continue;    			
	    		}
	    		$custom_placeholder = '#field_' . $item[0];
	    		$message = str_replace( $custom_placeholder, $item[2], $message );
			}		
		}
		// end extra data			 
		if( $current_category == 0 ){
			$current_category_name = '';
		} else {
			$current_category_name = WBK_Db_Utils::getCategoryNameByCategoryId( $current_category );
			if( $current_category_name == false  ){
				$current_category_name = '';
			}
		}
		 
		if( is_null( $total_amount ) ){
			$message = str_replace( '#total_amount', $total_price, $message );	
		} else {
			$message = str_replace( '#total_amount', $total_amount, $message );	
		}		
		$category_names = WBK_Db_Utils::getCategoryNamesByService( $service->getId() );
		$message = str_replace( '#category_names', $category_names, $message );
		$message = str_replace( '#current_category_name', $current_category_name, $message );
		$message = str_replace( '#service_name', $service->getName(), $message );
		$message = str_replace( '#customer_name', $appointment->getName(), $message );
		$message = str_replace( '#appointment_day', date_i18n( $date_format, $appointment->getDay() ), $message );
		$message = str_replace( '#appointment_time', date_i18n( $time_format, $appointment->getTime() ), $message );
		$message = str_replace( '#appointment_local_time', date_i18n( $time_format, $appointment->getLocalTime() ), $message );
		$message = str_replace( '#appointment_local_date', date_i18n( $date_format, $appointment->getLocalTime() ), $message );
		$message = str_replace( '#customer_phone', $appointment->getPhone(), $message );
		$message = str_replace( '#customer_email', $appointment->getEmail(), $message );
		$message = str_replace( '#customer_comment', $appointment->getDescription(), $message );
		$message = str_replace( '#items_count', $appointment->getQuantity(), $message );
		$message = str_replace( '#appointment_id', $appointment->getId(), $message );		
		$message = str_replace( '#customer_custom', $appointment->getFormatedExtra(), $message );	
		$time_range = date_i18n( $time_format, $appointment->getTime() ) . ' - ' .  date_i18n( $time_format, $appointment->getTime() + $service->getDuration()*60 );
		$message = str_replace( '#time_range', $time_range , $message );
		return $message;
	}



	public static function landing_appointment_data_processing( $text, $appointment, $service ){
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time = $appointment->getTime();
		$end = $appointment->getTime() + $service->getDuration() * 60; 			
						
		$text = str_replace( '#name', $appointment->getName(), $text );
		$text = str_replace( '#service', $service->getName(), $text );
		$text = str_replace( '#date', date_i18n( $date_format, $time ), $text );
        $text = str_replace( '#time', date_i18n( $time_format, $time ), $text );
		$text = str_replace( '#start_end', date_i18n( $time_format, $time ). ' - '. date_i18n( $time_format, $end ) , $text );
		$text = str_replace( '#dt', date_i18n( $date_format, $time ) . ' ' .  date_i18n( $time_format, $time ), $text );
		$text = str_replace( '#id', $appointment->getId(), $text );


		return $text;
	}
	protected static function get_string_between( $string, $start, $end ){
	    $string = ' ' . $string;
	    $ini = strpos($string, $start);
	    if ($ini == 0) return '';
	    $ini += strlen($start);
	    $len = strpos($string, $end, $ini) - $ini;
	    return substr($string, $ini, $len);
	}
	static function prepareThankYouMessage( $appointment_ids, $service_id, $thanks_message, $skipped = null ){
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );	 	 
 		if( get_option( 'wbk_multi_booking', 'disabled' ) != 'disabled'  ){
 			 			
			$looped = self::get_string_between( $thanks_message, '[appointment_loop_start]', '[appointment_loop_end]' );
			$looped_html = '';
			$token_arr = array();
		 	foreach ( $appointment_ids as $appointment_id ){
				$appointment = new WBK_Appointment();
				if ( !$appointment->setId( $appointment_id ) ) {
					date_default_timezone_set( 'UTC' );	
					return;
				}
				if ( !$appointment->load() ) {
					date_default_timezone_set( 'UTC' );	
					return;
				}
				$service = new WBK_Service(); 
				if ( !$service->setId( $appointment->getService() ) ) {
					return $thanks_message;
				}
				if ( !$service->load() ) {
					return $thanks_message;
				}
				$looped_html .= self::message_placeholder_processing( $looped, $appointment, $service );
				$token_arr[] = self::getTokenByAppointmentId( $appointment_id );
		 	}
		 	
		 	if( count( $token_arr ) > 0 ){
		 		$multi_token = implode( '-', $token_arr );

		 	} else {
		 		$multi_token = null;
		 	}

			$total = count( $appointment_ids ) * $service->getPrice() * $appointment->getQuantity();
			$price_format = get_option( 'wbk_payment_price_format', '$#price' );			
			$tax_rule = get_option( 'wbk_tax_for_messages', 'paypal' );
			if( $tax_rule == 'paypal' ){
				$tax = get_option( 'wbk_paypal_tax', 0 );			
			}
			if( $tax_rule == 'stripe' ){
				$tax = get_option( 'wbk_stripe_tax', 0 );			
			}
			if( $tax_rule == 'none' ){
				$tax = 0;			
			}
 			if( is_numeric( $tax ) && $tax > 0 ){
				$tax_amount = ( ( $total ) / 100 ) * $tax;
			   	$total = $total + $tax_amount;
			} 
			$total_price =  str_replace( '#price', number_format( $total,  get_option( 'wbk_price_fractional', '2' ) ), $price_format );

		 	$search_tag =  '[appointment_loop_start]' . $looped . '[appointment_loop_end]';
		 	$thanks_message = str_replace( $search_tag, $looped_html, $thanks_message ); 
		 	$thanks_message = str_replace( '#selected_count', count( $appointment_ids ), $thanks_message );
		 	if( !is_null( $skipped ) ){
		 		$thanks_message = str_replace( '#failed_count', $skipped, $thanks_message );
		 	}

		 	$thanks_message = self::message_placeholder_processing(  $thanks_message, $appointment, $service, $total_price, null, $multi_token );
 
 		} elseif ( get_option( 'wbk_multi_booking', 'disabled' ) == 'disabled' ){
 			if( count( $appointment_ids ) == 0 ){
 				date_default_timezone_set( 'UTC' );	
 				return $thanks_message;
 			}
 			$appointment = new WBK_Appointment();
			if ( !$appointment->setId( $appointment_ids[0] ) ) {
				date_default_timezone_set( 'UTC' );	
				return $thanks_message;
			}
			if ( !$appointment->load() ) {
				date_default_timezone_set( 'UTC' );	
				return $thanks_message;
			} 
			$service = new WBK_Service(); 
			if ( !$service->setId( $service_id ) ) {
				date_default_timezone_set( 'UTC' );	
				return $thanks_message;
			}
			if ( !$service->load() ) {
				date_default_timezone_set( 'UTC' );	
				return $thanks_message;
			}
			$thanks_message = self::message_placeholder_processing( $thanks_message, $appointment, $service );
		}
		date_default_timezone_set( 'UTC' );	
		return $thanks_message; 	 
 	}
 	static function backend_customer_name_processing( $appointment_id, $customer_name ){
 		$template = get_option( 'wbk_customer_name_output', '#name' );
 		$result = str_replace( '#name',  $customer_name, $template );
		$words = explode( ' ',  $result );
 		foreach( $words as $word ){
 			$word_parts = explode( '_', $word );
 			if( count( $word_parts ) != 2 ){
 				continue;
 			}
 			if( $word_parts[0] == '#field' ){
 				$field_name = $word_parts[1];
 				$field_placeholder = '#field_' . $field_name;
 				$field_value = self::get_extra_value_by_appoiuntment_id( $appointment_id, $field_name );
 				$result = str_replace( $field_placeholder, $field_value, $result );
 			}
 		}
 		return $result;
 	}
 	static function get_extra_value_by_appoiuntment_id( $appointment_id, $field_name ){
 		return self::getExtraValueByAppointmentId( $appointment_id, $field_name );
 	}
 	static function addAppointmentDataToGGCelendar( $service_id, $appointment_id ){
 		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			 
			return FALSE;
		}
		if ( !$service->load() ) {
						 
 			return FALSE;
		}
		$appointment = new WBK_Appointment();
		if ( !$appointment->setId( $appointment_id ) ) {
					 
			return FALSE;
		}
		if ( !$appointment->load() ) {
					 
 			return FALSE;
		}
		$gg_calendars = $service->getGgCalendars();
		if( $gg_calendars == '' ){
						 
			return;
		}
		$title = get_option( 'wbk_gg_calendar_event_title', '#customer_name' );
		$description = get_option( 'wbk_gg_calendar_event_description', '#customer_name #customer_phone' );		
		$description = str_replace( '{n}', "\n\n",  $description);
		$title = self::subject_placeholder_processing_gg( $title, $appointment, $service );
		$description = self::message_placeholder_processing_gg( $description, $appointment, $service );
		$time_zone = get_option( 'wbk_timezone', 'UTC' );
		$start = date( 'Y-m-d', $appointment->getTime()  ) . 'T' . date(  'H:i:00', $appointment->getTime()  );
		$end = date( 'Y-m-d', $appointment->getTime() + $service->getDuration() * 60 + $service->getInterval() * 60 ) . 'T' . date(  'H:i:00', $appointment->getTime() + $service->getDuration() * 60 + $service->getInterval() * 60  );
		$gg_calendars = explode( ';', $gg_calendars );
		$event_data_arr = array();
		foreach ( $gg_calendars as $calendar_id ) {
			$google = new WBK_Google();
	  		$google->init( $calendar_id );
	  		if( $google->getCalendarMode() == 'One-way-import' ){
				continue;
			}  	  		 
	  		$connect_status = $google->connect();	  		 
			if( $connect_status[0]  == 1 ){
				 
				$event_data = $google->insertEvent( $title, $description, $start, $end, $time_zone );
				 				
				if( $event_data !=  FALSE ){
					$event_data_arr[] = $event_data;
				}
			}
		}
		if( count( $event_data_arr ) > 0 ){
			self::setGoogleEventsData( $appointment_id, json_encode( $event_data_arr )  );			 
		} 
 	}
	static function addAppointmentDataToCustomerGGCelendar( $service_id, $appointment_ids, $code ){
 		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			return FALSE;
		}
		if ( !$service->load() ) {
 			return FALSE;
		}
		$google = new WBK_Google();
	  	if( $google->init( null ) == FALSE ){
	  		return FALSE;
	  	}  		
	  	$i = 0; 
		if( $google->initCalendarByAuthcode( $code ) === TRUE ){
			foreach( $appointment_ids as $appointment_id ){
				$appointment = new WBK_Appointment();
				if ( !$appointment->setId( $appointment_id ) ) {
					continue;
				}
				if ( !$appointment->load() ) {
		 			continue;
				} 
				$title = get_option( 'wbk_gg_calendar_event_title_customer', '#service_name' );
				$description = get_option( 'wbk_gg_calendar_event_description_customer', 'Your appointment id is #appointment_id' );
				$title = self::subject_placeholder_processing_gg( $title, $appointment, $service );
				$description = self::message_placeholder_processing_gg( $description, $appointment, $service );
				$time_zone = get_option( 'wbk_timezone', 'UTC' );
				
				$start = date( 'Y-m-d', $appointment->getTime()  ) . 'T' . date(  'H:i:00', $appointment->getTime()  );
				$end = date( 'Y-m-d', $appointment->getTime() + $service->getDuration() * 60 + $service->getInterval() * 60 ) . 'T' . date(  'H:i:00', $appointment->getTime() + $service->getDuration() * 60 + $service->getInterval() * 60  );
							 
				$event_data = $google->insertEvent( $title, $description, $start, $end, $time_zone, 'primary' );	
				if( $event_data == FALSE ){
					continue;
				} 
				$i++;
			}
		} 
		if( $i == 0 ){
			return FALSE;				 
		}
		return TRUE;
 	}
 	static function updateAppointmentDataAtGGCelendar( $appointment_id ){
		global $wpdb;
		$appointment = new WBK_Appointment();
		if ( !$appointment->setId( $appointment_id ) ) {
			return FALSE;
		}
		if ( !$appointment->load() ) {
 			return FALSE;
		}
		$service_id = $appointment->getService();
		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			return FALSE;
		}
		if ( !$service->load() ) {
 			return FALSE;
		}
		$event_id_json = $wpdb->get_var( $wpdb->prepare( "SELECT gg_event_id FROM wbk_appointments WHERE id = %d", $appointment_id ) );
		if( $event_id_json == '' ){
			return;
		}
		$event_id_arr = json_decode( $event_id_json );
		$title = get_option( 'wbk_gg_calendar_event_title', '#customer_name' );
		$description = get_option( 'wbk_gg_calendar_event_description', '#customer_name #customer_phone' );
		$description = str_replace( '{n}', "\n\n",  $description);
		$title = self::subject_placeholder_processing_gg( $title, $appointment, $service );
		$description = self::message_placeholder_processing_gg( $description, $appointment, $service );
		$time_zone = get_option( 'wbk_timezone', 'UTC' );
		$start = date( 'Y-m-d', $appointment->getTime()  ) . 'T' . date(  'H:i:00', $appointment->getTime()  );
		$end = date( 'Y-m-d', $appointment->getTime() + $service->getDuration() * 60 + $service->getInterval() * 60 ) . 'T' . date(  'H:i:00', $appointment->getTime() + $service->getDuration() * 60 + $service->getInterval() * 60  );
		foreach( $event_id_arr as $event ){
			$google = new WBK_Google();
	  		$google->init( $event[0] );  		 
	  		$connect_status = $google->connect();	  
	  		if( $connect_status[0]  == 1 ){
	  			$google->updateEvent( $event[1], $title, $description, $start, $end, $time_zone );
	  		} 
		}		 
 	}
 	static function deleteAppointmentDataAtGGCelendar( $appointment_id ){
 		global $wpdb;
 		$event_id_json = $wpdb->get_var( $wpdb->prepare( "SELECT gg_event_id FROM wbk_appointments WHERE id = %d", $appointment_id ) );
		if( $event_id_json == '' ){
			return;
		}
		$event_id_arr = json_decode( $event_id_json );
		foreach( $event_id_arr as $event ){
			$google = new WBK_Google();
	  		$google->init( $event[0] );  		 
	  		$connect_status = $google->connect();	  
	  		if( $connect_status[0]  == 1 ){
	  			$google->deleteEvent( $event[1] );
	  		} 
		}		
	}
	public static function message_placeholder_processing_gg( $message, $appointment, $service ){
		global $wbk_wording;
		$date_format = WBK_Date_Time_Utils::getDateFormat();
		$time_format = WBK_Date_Time_Utils::getTimeFormat();
 		// begin total amount
		$total_price = '';
		$payment_methods = explode( ';', $service->getPayementMethods() );
		if( count( $payment_methods )  > 0 ){
			$total = $appointment->getQuantity() * $service->getPrice();
			$price_format = get_option( 'wbk_payment_price_format', '$#price' );
			$tax_rule = get_option( 'wbk_tax_for_messages', 'paypal' );
			if( $tax_rule == 'paypal' ){
				$tax = get_option( 'wbk_paypal_tax', 0 );			
			}
			if( $tax_rule == 'stripe' ){
				$tax = get_option( 'wbk_stripe_tax', 0 );			
			}
			if( $tax_rule == 'none' ){
				$tax = 0;			
			}
	 		if( is_numeric( $tax ) && $tax > 0 ){
				$tax_amount = ( ( $total ) / 100 ) * $tax;
			    	$total = $total + $tax_amount;
				} 
			$total_price =  str_replace( '#price', number_format( $total,  get_option( 'wbk_price_fractional', '2' ) ), $price_format );
		}
		// end total amount
		// beging extra data
		$extra_data = trim( $appointment->getExtra() );
		if( $extra_data != '' ){
			$extra = json_decode( $extra_data );
			foreach( $extra as $item ){
	    		if( count( $item ) <> 3 ){
	    			continue;    			
	    		}
	    		$custom_placeholder = '#field_' . $item[0];
	    		$message = str_replace( $custom_placeholder, $item[2], $message );
			}		
		}
		// end extra data	
 		$message = str_replace( '#total_amount', $total_price, $message );		        
		$message = str_replace( '#service_name', $service->getName(), $message );
		$message = str_replace( '#customer_name', $appointment->getName(), $message );
 		$message = str_replace( '#customer_phone', $appointment->getPhone(), $message );
		$message = str_replace( '#customer_email', $appointment->getEmail(), $message );
		$message = str_replace( '#customer_comment', $appointment->getDescription(), $message );
		$message = str_replace( '#items_count', $appointment->getQuantity(), $message );
		$message = str_replace( '#appointment_id', $appointment->getId(), $message );
		$message = str_replace( '#customer_custom', $appointment->getFormatedExtra(), $message );
		return $message; 					 
	}
	public static function subject_placeholder_processing_gg( $message, $appointment, $service ){
		return self::message_placeholder_processing( $message, $appointment, $service ); 	 	 				 
	}
	public static function wbk_sanitize( $value ){
		$value = str_replace('"', '', $value );
		$value = str_replace('<', '', $value );
		$value = str_replace('\'', '', $value );
		$value = str_replace('>', '', $value );
		$value = str_replace('/', '', $value );
		$value = str_replace('\\',  '', $value );
		$value = str_replace('and',  '', $value );
		$value = str_replace('union',  '', $value );
		$value = str_replace('delete',  '', $value );
		$value = str_replace('select',  '', $value );
		return $value;
	}
	public static function getAppointmentStatus( $appointment_id ){
		global $wpdb;
        $sql =  $wpdb->prepare( "SELECT status FROM wbk_appointments WHERE id = %d", $appointment_id);
        $status = $wpdb->get_var( $sql );
        return $status;
	}
	public static function setAppointmentStatus( $appointment_id, $status ){
		global $wpdb;
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'status' => $status ), 
						array( 'id' => $appointment_id), 
						array( '%s' ), 
						array( '%d' ) 
		);
		return $result;
	}
	public static function is_gg_event_added_to_customers_calendar( $appointment_id ){
		global $wpdb;
		return FALSE;
	}
	// get multiple appointments id by grouped token
	static function getAppointmentIdsByGroupToken( $token ){
		global $wpdb;	
		$arr_tokens = explode( '-', $token );
		$result = array();
		foreach( $arr_tokens as $token ){
			$appointment_id = $wpdb->get_var( $wpdb->prepare( " SELECT id FROM wbk_appointments WHERE token = %s ", $token ) );
			if ( $appointment_id == null ){
				continue;
			} else {
				$result[] = $appointment_id;
			}
		}
		return $result;
	}
	static function getAppointmentIdsByGroupAdminToken( $token ){
		global $wpdb;	
		$arr_tokens = explode( '-', $token );
		$result = array();
		foreach( $arr_tokens as $token ){
			$appointment_id = $wpdb->get_var( $wpdb->prepare( " SELECT id FROM wbk_appointments WHERE admin_token = %s ", $token ) );
			if ( $appointment_id == null ){
				continue;
			} else {
				$result[] = $appointment_id;
			}
		}
		return $result;
	}
	// set coupon to the appointment
	static function setCouponToAppointment( $appointment_id, $coupon ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'coupon' => $coupon ), 
						array( 'id' => $appointment_id) , 
						array( '%d' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}	
	}
	static function getCouponByAppointmentId( $appointment_id ){
		global $wpdb;
		$coupon = $wpdb->get_var( $wpdb->prepare( " SELECT coupon FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		return $coupon;	
	}
	// set payment_method to the appointment
	static function setPaymentMethodToAppointment( $appointment_id, $payment_method ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'payment_method' => $payment_method ), 
						array( 'id' => $appointment_id) , 
						array( '%s' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}	
	}
	static function getPaymentMethodByAppointmentId( $appointment_id ){
		global $wpdb;
		$payment_method = $wpdb->get_var( $wpdb->prepare( " SELECT payment_method FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		return $payment_method;	
	}
	static function increeaseCouponUsage( $appointment_id ){
		global $wpdb;
		$coupon = $wpdb->get_var( $wpdb->prepare( " SELECT coupon FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		if( $coupon == 0 ){
			return;
		}
		$used  = $wpdb->get_var( $wpdb->prepare( " SELECT used FROM wbk_coupons WHERE id = %d ", $coupon ) );

		if ( $used == null ){
			return false;
		} else {
			$used = intval( $used );
			$used++;
			$result = $wpdb->update( 
						'wbk_coupons', 
						array( 'used' => $used ), 
						array( 'id' => $coupon) , 
						array( '%d' ), 
						array( '%d' ) 
					);
			if( $result == false || $result == 0 ){
				return FALSE;
			} else {
				return TRUE;
			}	
		}
	}
	static function getCouponDiscount( $coupon_id){
		global $wpdb;
		$result = $wpdb->get_row( $wpdb->prepare( " SELECT * FROM wbk_coupons WHERE id = %d", $coupon_id ) , ARRAY_A );	
		if( $result == NULL ){
			return FALSE;
		}		 
		return array( $result['amount_fixed'], $result['amount_percentage'] );
	}
	static function initServiceById( $service_id ){
		$service = new WBK_Service();
		if ( !$service->setId( $service_id ) ) {
			return FALSE;
		}
		if ( !$service->load() ) {
 			return FALSE;
		}
		return $service;
	}
	static function doCacheForGoogleCalendars(){
		$calendar_ids = self::getBackwardGGCalendars();
		$start = date( 'c', time() );
		foreach( $calendar_ids as $calendar_id ){
			$google = new WBK_Google();			
	  		$google->init( $calendar_id );	  		 	 
	  		$connect_status = $google->connect();	  		 
			if( $connect_status[0]  == 1 ){				
				$google->doCache( $start ); 				 
			}
		}
	}
	// get calendars  
	static function getBackwardGGCalendars() {
	 	global $wpdb;
		$result = $wpdb->get_col( "SELECT id FROM wbk_gg_calendars where mode = 'Two-ways' OR mode = 'One-way-import'" );
		return $result;
	}
	// get count of appointment by email-time-service  
	static function getCountOfAppointmentsByEmailTimeService( $email, $time, $service_id ){
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( " SELECT COUNT(*) FROM wbk_appointments WHERE email=%s and time=%d and service_id=%d", $email, $time, $service_id ) );

		return $count;
	}
	// get count of appointment by email-service  
	static function getCountOfAppointmentsByEmailService( $email, $service_id ){
		global $wpdb;
		$count = $wpdb->get_var( $wpdb->prepare( " SELECT COUNT(*) FROM wbk_appointments WHERE email=%s and service_id=%d", $email, $service_id ) );
		return $count;
	} 
	// set creted_on to apppointment appointment
	static function setCreatedOnToAppointment( $appointment_id ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'created_on' => time() ), 
						array( 'id' => $appointment_id) , 
						array( '%d' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}	
	}
	// set creted_on to apppointment appointment
	static function setActualDurationToAppointment( $appointment_id, $duration ){
		global $wpdb;
		if( !is_numeric( $appointment_id ) ){
			return FALSE;
		}
		$result = $wpdb->update( 
						'wbk_appointments', 
						array( 'actual_duration' => $duration ), 
						array( 'id' => $appointment_id) , 
						array( '%d' ), 
						array( '%d' ) 
					);
		if( $result == false || $result == 0 ){
			return FALSE;
		} else {
			return TRUE;
		}	
	}
	static function getExtraValueByAppointmentId( $appointment_id, $field_id ){
		global $wpdb;
		$extra = $wpdb->get_var( $wpdb->prepare( " SELECT extra FROM wbk_appointments WHERE id = %d ", $appointment_id ) );
		
		$extra = json_decode( $extra );
    
		foreach( $extra as $item ){
    		if( count( $item ) <> 3 ){
    			continue;    			
    		}
    		if( $item[0] == $field_id ){
    			return $item[2];
    		}
    	}  
		return '';	
	}
	 
	static function setAmountForApppointment( $apppointment_id ){	
	    global $wpdb;	 	 
		$appointment = new WBK_Appointment();
		if ( !$appointment->setId( $apppointment_id ) ) {
			return FALSE;
		}
		if ( !$appointment->load() ) {
 			return FALSE;
		}
		$service_id = self::getServiceIdByAppointmentId( $apppointment_id );
		$service = self::initServiceById( $service_id );
		if( $service == FALSE ){
			return;
		}
		$price_per_appointment = $appointment->getQuantity() * $service->getPrice();
		$price_format = get_option( 'wbk_payment_price_format', '$#price' );	
		$amount = str_replace( '#price', number_format( $price_per_appointment,  get_option( 'wbk_price_fractional', '2' ) ), $price_format );		
			
		$result = $wpdb->update( 
					'wbk_appointments', 
					array( 'moment_price' => $amount ), 
					array( 'id' => $apppointment_id ), 
					array( '%s' ), 
					array( '%d' ) 
				);			 
			

	}	
	static function getLangByAppointmentId( $app_id ){
		global $wpdb;
 		$lang = $wpdb->get_var( $wpdb->prepare( 'select lang from wbk_appointments where id = %d', $app_id ) );
 		return $lang;
	}
	static function	setLangToAppointmentId( $app_id ){	
		global $wpdb;
	 	if(  !defined( 'ICL_LANGUAGE_CODE' ) ){	 		 
	 		return;
	 	}	  
		$wpdb->update( 
			'wbk_appointments', 
			array( 'lang' => ICL_LANGUAGE_CODE ), 
			array( 'id' => $app_id ), 
			array( '%s' ), 
			array( '%d' ) 
		);
	}
	static function	switchLanguageByAppointmentId( $app_id ){
	 	if(  !defined( 'ICL_LANGUAGE_CODE' ) ){
			return;
		}		
		$lang = self::getLangByAppointmentId( $app_id );
		 
		if( $lang == '' || $lang === FALSE ){
			return;
		}
		global $sitepress;
		$sitepress->switch_lang( $lang, true );
	}
	static function filterNotPaidAppointments( $appointment_ids ){		 	 
		$verified_ids = array();
        foreach( $appointment_ids as $appointment_id ){
            $status = self::getAppointmentStatus( $appointment_id );
            error_log( 'status: ' . $status );
            if( !is_null( $status) ) {
                if( $status == 'pending' || $status == 'approved' ){
                    $verified_ids[] = $appointment_id;   
                }
            }
        } 
        return $verified_ids;
	}
	static function	getPymentItemNamesByAppoiuntmentIds( $apppointment_ids ){
		date_default_timezone_set( get_option( 'wbk_timezone', 'UTC' ) );
		$item_name = get_option( 'wbk_payment_item_name', '' );
        if( $item_name == '' ){
        	$item_name = sanitize_text_field( $wbk_wording['payment_item_name'] );
        }
        $time_format = WBK_Date_Time_Utils::getTimeFormat();
        $date_format = WBK_Date_Time_Utils::getDateFormat();
    	$item_names = array();
        foreach ( $apppointment_ids as $appointment_id ) {
        	$appointment = new WBK_Appointment();
			if ( !$appointment->setId( $appointment_id ) ) {
				continue;
			}
			if ( !$appointment->load() ) {
	 			continue;
			}
			$service = self::initServiceById( $appointment->getService() );
			if( $service == FALSE ){
				continue;
			}
			$item_name = str_replace( '#service', $service->getName(), $item_name );
	        $item_name = str_replace( '#date', date_i18n( $date_format, $appointment->getTime() ), $item_name );
	        $item_name = str_replace( '#time', date_i18n( $time_format, $appointment->getTime() ), $item_name );
	        $item_name = str_replace( '#tr', date_i18n( $time_format, $appointment->getTime() ) . ' - ' .  date_i18n( $time_format, $appointment->getTime() + $service->getDuration() * 60 ) , $item_name );
	        $item_name = str_replace( '#id',  $appointment->getId(), $item_name );
	        $item_name = str_replace( '#name',  $appointment->getName(), $item_name );
	        $item_name = str_replace( '#email',  $appointment->getEmail(), $item_name );
	        $item_name = str_replace( '#quantity',  $appointment->getQuantity(), $item_name );

	       	$item_names[] = $item_name;        	 	
        }
        date_default_timezone_set('UTC');
        return trim( implode( ', ', $item_names ) );
	}
	static function	getAmountNoTaxByAppoiuntmentIds( $apppointment_ids ){
		$amount = 0;
		foreach ( $apppointment_ids as $appointment_id ) {
        	$appointment = new WBK_Appointment();
			if ( !$appointment->setId( $appointment_id ) ) {
				continue;
			}
			if ( !$appointment->load() ) {
	 			continue;
			}
			$service = self::initServiceById( $appointment->getService() );
			if( $service == FALSE ){
				continue;
			}
			$amount += $appointment->getQuantity() * $service->getPrice();
		}
		return $amount;

	}

}
?>