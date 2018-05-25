<?php
/**
 * The template for displaying 404 pages (not found)
 */

	get_header();

	echo '<div class="infinite-not-found-wrap" id="infinite-full-no-header-wrap" >';
	echo '<div class="infinite-not-found-background" ></div>';
	echo '<div class="infinite-not-found-container infinite-container">';
	echo '<div class="infinite-header-transparent-substitute" ></div>';
	
	echo '<div class="infinite-not-found-content infinite-item-pdlr">';
	echo '<h1 class="infinite-not-found-head" >' . esc_html__('404', 'infinite') . '</h1>';
	echo '<h3 class="infinite-not-found-title infinite-content-font" >' . esc_html__('Page Not Found', 'infinite') . '</h3>';
	echo '<div class="infinite-not-found-caption" >' . esc_html__('Sorry, we couldn\'t find the page you\'re looking for.', 'infinite') . '</div>';

	echo '<form role="search" method="get" class="search-form" action="' . esc_url(home_url('/')) . '">';
	echo '<input type="text" class="search-field infinite-title-font" placeholder="' . esc_html__('Type Keywords...', 'infinite') . '" value="" name="s">';
	echo '<div class="infinite-top-search-submit"><i class="fa fa-search" ></i></div>';
	echo '<input type="submit" class="search-submit" value="Search">';
	echo '</form>';
	echo '<div class="infinite-not-found-back-to-home" ><a href="' . esc_url(home_url('/')) . '" >' . esc_html__('Or Back To Homepage', 'infinite') . '</a></div>';
	echo '</div>'; // infinite-not-found-content

	echo '</div>'; // infinite-not-found-container
	echo '</div>'; // infinite-not-found-wrap

	get_footer(); 
