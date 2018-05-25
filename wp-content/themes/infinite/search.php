<?php
/**
 * The main template file
 */

	get_header();

	if( have_posts() ){

		$sidebar_type = infinite_get_option('general', 'archive-blog-sidebar', 'none');
		$sidebar_left = infinite_get_option('general', 'archive-blog-sidebar-left');
		$sidebar_right = infinite_get_option('general', 'archive-blog-sidebar-right');

		echo '<div class="infinite-content-container infinite-container">';
		echo '<div class="' . infinite_get_sidebar_wrap_class($sidebar_type) . '" >';

		// sidebar content
		echo '<div class="' . infinite_get_sidebar_class(array('sidebar-type'=>$sidebar_type, 'section'=>'center')) . '" >';
		
		if( class_exists('gdlr_core_pb_element_blog')  ){

			get_template_part('content/archive', 'blog');

		}else{

			get_template_part('content/archive', 'default');
			
		}

		echo '</div>'; // infinite-get-sidebar-class

		// sidebar left
		if( $sidebar_type == 'left' || $sidebar_type == 'both' ){
			echo infinite_get_sidebar($sidebar_type, 'left', $sidebar_left);
		}

		// sidebar right
		if( $sidebar_type == 'right' || $sidebar_type == 'both' ){
			echo infinite_get_sidebar($sidebar_type, 'right', $sidebar_right);
		}

		echo '</div>'; // infinite-get-sidebar-wrap-class
	 	echo '</div>'; // infinite-content-container
	 	
	 }

	get_footer(); 
?>