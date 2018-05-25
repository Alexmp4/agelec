<?php
/**
 * The template part for displaying the video post format thumbnail
 */

global $pages;

if( preg_match('#^<a.+<img.+/></a>|^<img.+/>#', $pages[0], $match) ){ 
	$post_format_image = $match[0];
}else if( preg_match('#^https?://\S+#', $pages[0], $match) ){
	$post_format_image = gdlr_core_get_image($match[0]);
}

if( !empty($post_format_image) ){
	echo '<div class="infinite-single-article-thumbnail infinite-media-image" >';
	echo gdlr_core_escape_content($post_format_image);
	echo '</div>';

	$pages[0] = substr($pages[0], strlen($match[0]));
}