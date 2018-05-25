<?php
// Webba Booking Google integration class BLANK
class WBK_Google{
	protected 
	$client;

	protected
	$calendar_id;

	protected
	$gg_calendar_id;

	public function init( $calendar_id ){
		return FALSE;
	}	 
	public function getAuthUrl(){		
		return '';
	}
	public function connect(){
 		return array( 2, '' );
	}
	public function renderCalendarBlock(){
 		return '';
	}	
	public function processAuthCode( $authCode ){
   		return 0;    	
	}
	protected function getAccessToken(){
		return '';
	}
 	protected function getGGCalendarId(){
		return 0;
	}
	protected function saveAccessToken( $access_token ){
		 return;
	}	 
	public function getCalendarName(){
		return '';
	}
	public function getCalendarMode(){
		return '';
	}
	public function clearToken(){
		return;
	}
	public function insertEvent( $title, $description, $start, $end, $time_zone, $calendar_id = '', $use_current_time_zone = false ){	
		return FALSE;
	}
	public function updateEvent( $event_id, $title, $description, $start, $end, $time_zone ){ 
		return FALSE;
	}
	public function deleteEvent( $event_id ){ 
		return FALSE;
	}
	public function initCalendarByAuthcode( $code ){		 
   		return FALSE;    	 
	}
	public function getEventsTimeRanges( $start, $end ){
		return FALSE;
	}
	public function doCache( $start ){
		return FALSE;
	}
	protected function getCacheTime(){
		return '';
	}
	protected function getCacheContent(){
		return '';
	}
	protected function saveCache( $cache_content ){
		return '';
	}	 
	protected function try_to_get_from_cache( $start, $end ){
	 return FALSE;
	}
}
?>