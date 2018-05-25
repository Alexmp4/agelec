<?php
namespace ElementorPro\Modules\ThemeBuilder\Widgets;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Scheme_Color;
use Elementor\Scheme_Typography;
use Elementor\Widget_Base;
use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Content extends Widget_Base {

	public function get_name() {
		// `theme` prefix is to avoid conflicts with a dynamic-tag with same name.
		return 'theme-post-content';
	}

	public function get_title() {
		return __( 'Post Content', 'elementor-pro' );
	}

	public function get_icon() {
		return 'eicon-post-content';
	}

	public function get_categories() {
		return [ 'theme-elements' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_style',
			[
				'label' => __( 'Style', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_responsive_control(
			'align',
			[
				'label' => __( 'Alignment', 'elementor-pro' ),
				'type' => Controls_Manager::CHOOSE,
				'options' => [
					'left' => [
						'title' => __( 'Left', 'elementor-pro' ),
						'icon' => 'fa fa-align-left',
					],
					'center' => [
						'title' => __( 'Center', 'elementor-pro' ),
						'icon' => 'fa fa-align-center',
					],
					'right' => [
						'title' => __( 'Right', 'elementor-pro' ),
						'icon' => 'fa fa-align-right',
					],
					'justify' => [
						'title' => __( 'Justified', 'elementor-pro' ),
						'icon' => 'fa fa-align-justify',
					],
				],
				'selectors' => [
					'{{WRAPPER}} .elementor-widget-container' => 'text-align: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'text_color',
			[
				'label' => __( 'Text Color', 'elementor-pro' ),
				'type' => Controls_Manager::COLOR,
				'default' => '',
				'selectors' => [
					'{{WRAPPER}}' => 'color: {{VALUE}};',
				],
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
			]
		);

		$this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'typography',
				'scheme' => Scheme_Typography::TYPOGRAPHY_3,
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		static $did_posts = [];

		$post = get_post();

		// Avoid recursion
		if ( isset( $did_posts[ $post->ID ] ) ) {
			return;
		}

		$did_posts[ $post->ID ] = true;
		// End avoid recursion

		$document = Module::instance()->get_document( $post->ID );

		if ( $document ) {
			$preview_id = $document->get_settings( 'preview_id' );

			if ( empty( $preview_id ) ) {
				return;
			}

			$post = get_post( $preview_id );
		}

		if ( Plugin::elementor()->preview->is_preview_mode( $post->ID ) ) {
			$content = Plugin::elementor()->preview->builder_wrapper( '' ); // XSS ok
		} else {
			$editor = Plugin::elementor()->editor;

			// Set edit mode as false, so don't render settings and etc. use the $is_edit_mode to indicate if we need the CSS inline
			$is_edit_mode = $editor->is_edit_mode();
			$editor->set_edit_mode( false );

			// Print manually (and don't use `the_content()`) because it's within another `the_content` filter, and the Elementor filter has been removed to avoid recursion.
			$content = Plugin::elementor()->frontend->get_builder_content( $post->ID, true );

			// Restore edit mode state
			Plugin::elementor()->editor->set_edit_mode( $is_edit_mode );

			if ( empty( $content ) ) {
				Plugin::elementor()->frontend->remove_content_filter();
				/** This filter is documented in wp-includes/post-template.php */
				$content = apply_filters( 'the_content', $post->post_content );
				Plugin::elementor()->frontend->add_content_filter();
			}
		}

		echo $content; // XSS ok.
	}

	public function render_plain_content() {}
}
