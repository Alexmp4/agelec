<?php
	/* a template for displaying the header social network */

	$social_list = array(
		'delicious' => 'fa fa-delicious', 
		'email' => 'fa fa-envelope', 
		'deviantart' => 'fa fa-deviantart', 
		'digg' => 'fa fa-digg', 
		'facebook' => 'fa fa-facebook', 
		'flickr' => 'fa fa-flickr', 
		'google-plus' => 'fa fa-google-plus', 
		'lastfm' => 'fa fa-lastfm',
		'linkedin' => 'fa fa-linkedin', 
		'pinterest' => 'fa fa-pinterest-p', 
		'rss' => 'fa fa-rss', 
		'skype' => 'fa fa-skype', 
		'stumbleupon' => 'fa fa-stumbleupon', 
		'tumblr' => 'fa fa-tumblr', 
		'twitter' => 'fa fa-twitter',
		'vimeo' => 'fa fa-vimeo', 
		'youtube' => 'fa fa-youtube',
		'instagram' => 'fa fa-instagram',
		'snapchat' => 'fa fa-snapchat-ghost',
	);

	foreach( $social_list as $social_key => $social_icon ){
		$social_link = infinite_get_option('general', 'top-bar-social-' . $social_key);

		if( $social_key == 'email' && !empty($social_link) ){
			$social_link = 'mailto:' . $social_link;
		}

		if( !empty($social_link) ){
			echo '<a href="' . esc_attr($social_link) . '" target="_blank" class="infinite-top-bar-social-icon" title="' . esc_attr($social_key) . '" >';
			echo '<i class="' . esc_attr($social_icon) . '" ></i>';
			echo '</a>';
		}
	}