<?php
namespace ElementorPro\Modules\Sticky;

use Elementor\Controls_Manager;
use Elementor\Controls_Stack;
use ElementorPro\Base\Module_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		$this->add_actions();
	}

	public function get_name() {
		return 'sticky';
	}

	public function register_controls( Controls_Stack $element ) {
		$element->start_controls_section(
			'section_scrolling_effect',
			[
				'label' => __( 'Scrolling Effect', 'elementor-pro' ),
				'tab' => Controls_Manager::TAB_ADVANCED,
				'condition' => [
					'stretch_section' => ''
				],
			]
		);

		$element->add_control(
			'sticky',
			[
				'label' => __( 'Sticky', 'elementor-pro' ),
				'type' => Controls_Manager::SWITCHER,
				'condition' => [
					'stretch_section' => ''
				],
				'render_type' => 'none',
				'return_value' => 'top',
				'frontend_available' => true,
			]
		);

		$element->add_control(
			'sticky_on',
			[
				'label' => __( 'Sticky On', 'elementor-pro' ),
				'type' => Controls_Manager::SELECT2,
				'multiple' => true,
				'label_block' => 'true',
				'default' => [ 'desktop', 'tablet', 'mobile' ],
				'options' => [
					'desktop' => __( 'Desktop', 'elementor-pro' ),
					'tablet' => __( 'Tablet', 'elementor-pro' ),
					'mobile' => __( 'Mobile', 'elementor-pro' ),
				],
				'condition' => [
					'stretch_section' => '',
					'sticky!' => ''
				],
				'render_type' => 'none',
				'frontend_available' => true,
			]
		);

		$element->end_controls_section();
	}

	private function add_actions() {
		add_action( 'elementor/element/section/section_advanced/after_section_end', [ $this, 'register_controls' ] );
	}
}
