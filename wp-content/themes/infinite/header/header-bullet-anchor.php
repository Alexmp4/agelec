<?php
	/* a template for displaying the header social network */

	$post_option = infinite_get_post_option(get_the_ID());
	if( !empty($post_option['bullet-anchor']) ){

		echo '<div class="infinite-bullet-anchor" id="infinite-bullet-anchor" >';
		echo '<a class="infinite-bullet-anchor-link current-menu-item" href="' . get_permalink() . '" ></a>';
		foreach( $post_option['bullet-anchor'] as $anchor ){
			echo '<a class="infinite-bullet-anchor-link" href="' . esc_url($anchor['title']) . '" ></a>';
		}
		echo '</div>';
	}