<?php
	if (isset($_GET['cookie_name'])) {
	    session_start();
		$cookie_name = rawurldecode($_GET['cookie_name']);
	    if(isset($_SESSION[$cookie_name])) {
    		unset($_SESSION[$cookie_name]);
    		echo "destroyed";
    	}
    	else {
    		echo "no session";
    	}
	}
	else {
		echo "no param value";
	}
?>