<?php
/**
 * The template part for displaying the video post format thumbnail
 */

global $pages;

if( preg_match('#^\[gallery[^\]]+]#', $pages[0], $match) ){ 	
	$post_format_gallery = do_shortcode($match[0]);		
	$pages[0] = substr($pages[0], strlen($match[0]));
}

if( !empty($post_format_gallery) ){
	echo '<div class="infinite-single-article-thumbnail infinite-media-gallery" >';
	echo gdlr_core_escape_content($post_format_gallery);
	echo '</div>';

	$pages[0] = substr($pages[0], strlen($match[0]));
}