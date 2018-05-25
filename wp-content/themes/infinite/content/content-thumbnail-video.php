<?php
/**
 * The template part for displaying the video post format thumbnail
 */

global $pages;

if( !preg_match('#^https?://\S+#', $pages[0], $match) ){
	if( !preg_match('#^\[video\s.+\[/video\]#', $pages[0], $match) ){
		preg_match('#^\[embed.+\[/embed\]#', $pages[0], $match);
	}
}

if( !empty($match[0]) ){
	echo '<div class="infinite-single-article-thumbnail infinite-media-video" >';
	echo gdlr_core_get_video($match[0], 'full');
	echo '</div>';

	$pages[0] = substr($pages[0], strlen($match[0]));
}