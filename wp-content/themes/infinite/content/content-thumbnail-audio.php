<?php
/**
 * The template part for displaying the audio post format thumbnail
 */

global $pages;

if( !preg_match('#^https?://\S+#', $pages[0], $match) ){
	preg_match('#^\[audio\s.+\[/audio\]#', $pages[0], $match);
}

if( !empty($match[0]) ){
	echo '<div class="infinite-single-article-thumbnail infinite-media-audio" >';
	echo gdlr_core_get_audio($match[0]);
	echo '</div>';

	$pages[0] = substr($pages[0], strlen($match[0]));
}