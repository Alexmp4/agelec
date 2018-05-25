<?php
namespace ElementorPro\Modules\Social\Widgets;

use Elementor\Controls_Manager;
use Elementor\Widget_Base;
use ElementorPro\Modules\Social\Classes\Facebook_SDK_Manager;
use ElementorPro\Modules\Social\Module;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Facebook_Button extends Widget_Base {

	public function get_name() {
		return 'facebook-button';
	}

	public function get_title() {
		return __( 'Facebook Button', 'elementor-pro' );
	}

	public function get_icon() {
		return 'eicon-facebook-like-box';
	}

	public function get_categories() {
		return [ 'pro-elements' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_content',
			[
				'label' => __( 'Button', 'elementor-pro' ),
			]
		);

		Facebook_SDK_Manager::add_app_id_control( $this );

		$this->add_control(
			'type',
			[
				'label' => __( 'Type', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'like',
				'options' => [
					'like' => __( 'Like', 'elementor-pro' ),
					'recommend' => __( 'Recommend', 'elementor-pro' ),
					'follow' => __( 'Follow', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'follow_url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'placeholder' => __( 'https://your-link.com', 'elementor-pro' ),
				'default' => 'https://www.facebook.com/elemntor/',
				'label_block' => true,
				'condition' => [
					'type' => 'follow',
				],
				'description' => __( 'Paste the URL of the Facebook page or profile.', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'layout',
			[
				'label' => __( 'Layout', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'standard',
				'options' => [
					'standard' => __( 'Standard', 'elementor-pro' ),
					'button' => __( 'Button', 'elementor-pro' ),
					'button_count' => __( 'Button Count', 'elementor-pro' ),
					'box_count' => __( 'Box Count', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'size',
			[
				'label' => __( 'Size', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'small',
				'options' => [
					'small' => __( 'Small', 'elementor-pro' ),
					'large' => __( 'Large', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'color_scheme',
			[
				'label' => __( 'Color Scheme', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'default' => 'light',
				'options' => [
					'light' => __( 'Light', 'elementor-pro' ),
					'dark' => __( 'Dark', 'elementor-pro' ),
				],
			]
		);

		$this->add_control(
			'show_share',
			[
				'label' => __( 'Share Button', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
				'condition' => [
					'type!' => 'follow',
				],
			]
		);

		$this->add_control(
			'show_faces',
			[
				'label' => __( 'Faces', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'default' => '',
			]
		);

		$this->add_control(
			'url_type',
			[
				'label' => __( 'Target URL', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT,
				'options' => [
					Module::URL_TYPE_CURRENT_PAGE => __( 'Current Page', 'elementor-pro' ),
					Module::URL_TYPE_CUSTOM => __( 'Custom', 'elementor-pro' ),
				],
				'default' => Module::URL_TYPE_CURRENT_PAGE,
				'separator' => 'before',
				'condition' => [
					'type' => [ 'like', 'recommend' ],
				],
			]
		);

		$this->add_control(
			'url',
			[
				'label' => __( 'URL', 'elementor-pro' ),
				'placeholder' => __( 'https://your-link.com', 'elementor-pro' ),
				'label_block' => true,
				'condition' => [
					'type' => [ 'like', 'recommend' ],
					'url_type' => Module::URL_TYPE_CUSTOM,
				],
			]
		);

		$this->end_controls_section();
	}

	public function render() {
		$settings = $this->get_settings();

		// Validate URL
		switch ( $settings['type'] ) {
			case 'follow':
				$facebook_url_pattern = '/^(https?:\/\/)?(www\.)?facebook.com\/[a-zA-Z0-9(\.)]*\/?$/';

				if ( ! preg_match( $facebook_url_pattern, $settings['follow_url'] ) ) {
					if ( Plugin::elementor()->editor->is_edit_mode() ) {
						echo $this->get_title() . ': ' . esc_html__( 'Please enter a valid Facebook User/Page URL', 'elementor-pro' ); // XSS ok.
					}

					return;
				}
				break;
			case 'like':
			case 'recommend':
				if ( Module::URL_TYPE_CUSTOM === $settings['url_type'] && ! filter_var( $settings['url'], FILTER_VALIDATE_URL ) ) {
					if ( Plugin::elementor()->editor->is_edit_mode() ) {
						echo $this->get_title() . ': ' . esc_html__( 'Please enter a valid URL', 'elementor-pro' ); // XSS ok.
					}

					return;
				}
				break;
		}

		$attributes = [
			'data-layout' => $settings['layout'],
			'data-colorscheme' => $settings['color_scheme'],
			'data-size' => $settings['size'],
			'data-show-faces' => $settings['show_faces'] ? 'true' : 'false',
			// The style prevent's the `widget.handleEmptyWidget` to set it as an empty widget
			'style' => 'min-height: 1px',
		];

		switch ( $settings['type'] ) {
			case 'follow':
				$attributes['data-href'] = $settings['follow_url'];
				$attributes['class'] = 'elementor-facebook-widget fb-follow';
				break;
			case 'like':
			case 'recommend':
				if ( Module::URL_TYPE_CURRENT_PAGE === $settings['url_type'] ) {
					$permalink = Facebook_SDK_Manager::get_permalink();
				} else {
					$permalink = esc_url( $settings['url'] );
				}

				$attributes['class'] = 'elementor-facebook-widget fb-like';
				$attributes['data-href'] = $permalink;
				$attributes['data-share'] = $settings['show_share'] ? 'true' : 'false';
				$attributes['data-action'] = $settings['type'];
				break;
		}

		$this->add_render_attribute( 'embed_div', $attributes );

		echo '<div ' . $this->get_render_attribute_string( 'embed_div' ) . '></div>'; // XSS ok.
	}

	public function render_plain_content() {}
}
