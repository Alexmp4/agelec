<?php
/**
 * The main template file
 */ 


	get_header();

	echo '<div class="infinite-content-container infinite-container">';
	echo '<div class="infinite-sidebar-style-none" >'; // for max width

	get_template_part('content/archive', 'default');

	echo '</div>'; // infinite-content-area
	echo '</div>'; // infinite-content-container

	get_footer(); 
