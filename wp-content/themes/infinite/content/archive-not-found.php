<?php
/**
 * The template for displaying archive not found
 */

	echo '<div class="infinite-not-found-wrap" id="infinite-full-no-header-wrap" >';
	echo '<div class="infinite-not-found-background" ></div>';
	echo '<div class="infinite-not-found-container infinite-container">';
	echo '<div class="infinite-header-transparent-substitute" ></div>';
	
	echo '<div class="infinite-not-found-content infinite-item-pdlr">';
	echo '<h1 class="infinite-not-found-head" >' . esc_html__('Not Found', 'infinite') . '</h1>';
	echo '<div class="infinite-not-found-caption" >' . esc_html__('Nothing matched your search criteria. Please try again with different keywords.', 'infinite') . '</div>';

	echo '<form role="search" method="get" class="search-form" action="' . esc_url(home_url('/')) . '">';
	echo '<input type="text" class="search-field infinite-title-font" placeholder="' . esc_html__('Type Keywords...', 'infinite') . '" value="" name="s">';
	echo '<div class="infinite-top-search-submit"><i class="fa fa-search" ></i></div>';
	echo '<input type="submit" class="search-submit" value="Search">';
	echo '</form>';
	echo '<div class="infinite-not-found-back-to-home" ><a href="' . esc_url(home_url('/')) . '" >' . esc_html__('Or Back To Homepage', 'infinite') . '</a></div>';
	echo '</div>'; // infinite-not-found-content

	echo '</div>'; // infinite-not-found-container
	echo '</div>'; // infinite-not-found-wrap