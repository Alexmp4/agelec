<?php
	/**
	 * The template part for displaying single posts style 1
	 */

	// print header title
	if( get_post_type() == 'post' ){
		get_template_part('header/header', 'title-blog');
	}

	$post_option = infinite_get_post_option(get_the_ID());
	$post_option = empty($post_option)? array(): $post_option;
	$post_option['show-content'] = empty($post_option['show-content'])? 'enable': $post_option['show-content']; 

	if( empty($post_option['sidebar']) || $post_option['sidebar'] == 'default' ){
		$sidebar_type = infinite_get_option('general', 'blog-sidebar', 'none');
		$sidebar_left = infinite_get_option('general', 'blog-sidebar-left');
		$sidebar_right = infinite_get_option('general', 'blog-sidebar-right');
	}else{
		$sidebar_type = empty($post_option['sidebar'])? 'none': $post_option['sidebar'];
		$sidebar_left = empty($post_option['sidebar-left'])? '': $post_option['sidebar-left'];
		$sidebar_right = empty($post_option['sidebar-right'])? '': $post_option['sidebar-right'];
	}

	if( $sidebar_type != 'none' || $post_option['show-content'] == 'enable' ){
		echo '<div class="infinite-content-container infinite-container">';
		echo '<div class="' . infinite_get_sidebar_wrap_class($sidebar_type) . '" >';

		// sidebar content
		echo '<div class="' . infinite_get_sidebar_class(array('sidebar-type'=>$sidebar_type, 'section'=>'center')) . '" >';
		echo '<div class="infinite-content-wrap infinite-item-pdlr clearfix" >';

		// single content
		if( $post_option['show-content'] == 'enable' ){
			echo '<div class="infinite-content-area" >';
			if( in_array(get_post_format(), array('aside', 'quote', 'link')) ){
				get_template_part('content/content', get_post_format());
			}else{
				get_template_part('content/content', 'single');
			}
			echo '</div>';
		}
	}

	if( !post_password_required() ){
		if( $sidebar_type != 'none' ){
			echo '<div class="infinite-page-builder-wrap infinite-item-rvpdlr" >';
			do_action('gdlr_core_print_page_builder');
			echo '</div>';

		// sidebar == 'none'
		}else{
			ob_start();
			do_action('gdlr_core_print_page_builder');
			$pb_content = ob_get_contents();
			ob_end_clean();

			if( !empty($pb_content) ){
				if( $post_option['show-content'] == 'enable' ){
					echo '</div>'; // infinite-content-area
					echo '</div>'; // infinite_get_sidebar_class
					echo '</div>'; // infinite_get_sidebar_wrap_class
					echo '</div>'; // infinite_content_container
				}
				echo gdlr_core_escape_content($pb_content);
				echo '<div class="infinite-bottom-page-builder-container infinite-container" >'; // infinite-content-area
				echo '<div class="infinite-bottom-page-builder-sidebar-wrap infinite-sidebar-style-none" >'; // infinite_get_sidebar_class
				echo '<div class="infinite-bottom-page-builder-sidebar-class" >'; // infinite_get_sidebar_wrap_class
				echo '<div class="infinite-bottom-page-builder-content infinite-item-pdlr" >'; // infinite_content_container
			}
		}
	}

	// social share
	if( infinite_get_option('general', 'blog-social-share', 'enable') == 'enable' ){
		if( class_exists('gdlr_core_pb_element_social_share') ){
			$share_count = (infinite_get_option('general', 'blog-social-share-count', 'enable') == 'enable')? 'counter': 'none';

			echo '<div class="infinite-single-social-share infinite-item-rvpdlr" >';
			echo gdlr_core_pb_element_social_share::get_content(array(
				'social-head' => $share_count,
				'layout'=>'left-text', 
				'text-align'=>'center',
				'facebook'=>infinite_get_option('general', 'blog-social-facebook', 'enable'),
				'linkedin'=>infinite_get_option('general', 'blog-social-linkedin', 'enable'),
				'google-plus'=>infinite_get_option('general', 'blog-social-google-plus', 'enable'),
				'pinterest'=>infinite_get_option('general', 'blog-social-pinterest', 'enable'),
				'stumbleupon'=>infinite_get_option('general', 'blog-social-stumbleupon', 'enable'),
				'twitter'=>infinite_get_option('general', 'blog-social-twitter', 'enable'),
				'email'=>infinite_get_option('general', 'blog-social-email', 'enable'),
				'padding-bottom'=>'0px'
			));
			echo '</div>';
		}
	}

	// author section
	$author_desc = get_the_author_meta('description');
	if( !empty($author_desc) && infinite_get_option('general', 'blog-author', 'enable') == 'enable' ){
		echo '<div class="clear"></div>';
		echo '<div class="infinite-single-author clearfix" >';
		echo '<div class="infinite-single-author-wrap" >';
		echo '<div class="infinite-single-author-avartar infinite-media-image">' . get_avatar(get_the_author_meta('ID'), 90) . '</div>';
		
		echo '<div class="infinite-single-author-content-wrap" >';
		echo '<div class="infinite-single-author-caption infinite-info-font" >' . esc_html__('About the author', 'infinite') . '</div>';
		echo '<h4 class="infinite-single-author-title">';
		the_author_posts_link();
		echo '</h4>';

		echo '<div class="infinite-single-author-description" >' . gdlr_core_escape_content(gdlr_core_text_filter($author_desc)) . '</div>';
		echo '</div>'; // infinite-single-author-content-wrap
		echo '</div>'; // infinite-single-author-wrap
		echo '</div>'; // infinite-single-author
	}

	// prev - next post navigation
	if( infinite_get_option('general', 'blog-navigation', 'enable') == 'enable' ){
		$prev_post = get_previous_post_link(
			'<span class="infinite-single-nav infinite-single-nav-left">%link</span>',
			'<i class="arrow_left" ></i><span class="infinite-text" >' . esc_html__( 'Prev', 'infinite' ) . '</span>'
		);
		$next_post = get_next_post_link(
			'<span class="infinite-single-nav infinite-single-nav-right">%link</span>',
			'<span class="infinite-text" >' . esc_html__( 'Next', 'infinite' ) . '</span><i class="arrow_right" ></i>'
		);
		if( !empty($prev_post) || !empty($next_post) ){
			echo '<div class="infinite-single-nav-area clearfix" >' . $prev_post . $next_post . '</div>';
		}
	}

	// comments template
	if( comments_open() || get_comments_number() ){
		comments_template();
	}

	echo '</div>'; // infinite-content-area
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

?>