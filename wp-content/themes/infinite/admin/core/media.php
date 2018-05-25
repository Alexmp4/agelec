<?php
	/*	
	*	Goodlayers Media File
	*	---------------------------------------------------------------------
	*	This file contains media function in the theme
	*	---------------------------------------------------------------------
	*/

	// get image from image id/url
	if( !function_exists('gdlr_core_get_image') ){
		function gdlr_core_get_image( $image, $size = 'full', $settings = array() ){
			if( empty($image) ) return;
		
			if( is_numeric($image) ){
				$alt_text = get_post_meta($image , '_wp_attachment_image_alt', true);	
				$image_src = wp_get_attachment_image_src($image, $size);	
				if( empty($image_src) ) return;

				return '<img src="' . esc_url($image_src[0]) . '" alt="' . esc_attr($alt_text) . '" width="' . esc_attr($image_src[1]) .'" height="' . esc_attr($image_src[2]) . '" />';
			}else{
				return '<img src="' . esc_url($image) . '" alt="" />';
			}

			return;
		}
	}	
	
	// get audio from url 
	if( !function_exists('gdlr_core_get_audio') ){
		function gdlr_core_get_audio( $audio ){

			if( empty($audio) ) return '';

			$ret  = '<div class="gdlr-core-audio">';
			if( preg_match('#^\[audio\s.+\[/audio\]#', $audio, $match) ){
				$ret .= do_shortcode($audio);
			}else{
				$ret .= wp_audio_shortcode(array('src'=>$audio));
			}
			$ret .= '</div>';

			return $ret;
		}
	}

	// get video from url
	if( !function_exists('gdlr_core_get_video') ){
		function gdlr_core_get_video( $video, $size = 'full', $atts = array() ){
			
			$size = array( 'width'=>640, 'height'=>360 );
			
			// video shortcode
			if( preg_match('#^\[video\s.+\[/video\]#', $video, $match) ){ 
				return do_shortcode($match[0]);
				
			// embed shortcode
			}else if( preg_match('#^\[embed.+\[/embed\]#', $video, $match) ){ 
				global $wp_embed; 
				return $wp_embed->run_shortcode($match[0]);
				
			// youtube link
			}else if( strpos($video, 'youtube') !== false || strpos($video, 'youtu.be') !== false ){
				if( strpos($video, 'youtube') !== false ){
					preg_match('#[?&]v=([^&]+)(&.+)?#', $video, $id);
				}else{
					preg_match('#youtu.be\/([^?&]+)#', $video, $id);
				}
				$id[2] = empty($id[2])? '': $id[2];
				$url = '//www.youtube.com/embed/' . $id[1] . '?wmode=transparent' . $id[2];
				if( !empty($atts['background']) ){
					$url = add_query_arg(array(
						'autoplay' => 1,
						'controls' => 0,
						'showinfo' => 0,
						'rel' => 0,
						'enablejsapi' => 1,
						'loop' => 1,
						'playlist' => $id[1]
					), $url);
				}
				
				return '<i ' . 'frame src="' . esc_url($url) . '" width="' . esc_attr($size['width']) . '" height="' . esc_attr($size['height']) . '" data-player-type="youtube" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>';

			// vimeo link
			}else if( strpos($video, 'vimeo') !== false ){
				preg_match('#https?:\/\/vimeo.com\/(\d+)#', $video, $id);
				$url = '//player.vimeo.com/video/' . $id[1] . '?title=0&byline=0&portrait=0';
				if( !empty($atts['background']) ){
					$url = add_query_arg(array(
						'autopause' => 0,
						'autoplay' => 1,
						'loop' => 1,
						'api' => 1,
						'background' => 1
					), $url);
				}
				
				return '<i' . 'frame src="' . esc_url($url) . '" width="' . esc_attr($size['width']) . '" height="' . esc_attr($size['height']) . '" data-player-type="vimeo" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen ></iframe>';
			
			// another link
			}else if(preg_match('#^https?://\S+#', $video, $match)){ 	
				$path_parts = pathinfo($match[0]);
				if( !empty($path_parts['extension']) ){
					return wp_video_shortcode( array( 'width' => $size['width'], 'height' => $size['height'], 'src' => $match[0]) );
				}else{
					global $wp_embed;
					return $wp_embed->run_shortcode('[embed width="' . $size['width'] . '" height="' . $size['height'] . '" ]' . $match[0] . '[/embed]');
				}				
			}
			
		}
	}	
