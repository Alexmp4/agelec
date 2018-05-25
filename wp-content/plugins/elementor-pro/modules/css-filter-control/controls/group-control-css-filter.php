<?php
namespace ElementorPro\Modules\CssFilterControl\Controls;

use Elementor\Controls_Manager;
use Elementor\Group_Control_Base;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Group_Control_Css_Filter extends Group_Control_Base {

	public static function get_type() {
		return 'css-filter';
	}

	protected static $fields;

	protected function init_fields() {
		$controls = [];

		$controls['filter_type'] = [
			'type' => Controls_Manager::HIDDEN,
			'default' => 'custom',
		];

		$controls['blur'] = [
			'label' => _x( 'Blur', 'Filter Control', 'elementor-pro' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'template',
			'required' => 'true',
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 10,
					'step' => 0.1,
				],
			],
			'default' => [
				'size' => 0,
			],
			'selectors' => [
				'{{SELECTOR}}' => 'filter: brightness( {{brightness.SIZE}}% ) contrast( {{contrast.SIZE}}% ) saturate( {{saturate.SIZE}}% ) blur( {{blur.SIZE}}px )',
			],
			'condition' => [
				'filter_type' => 'custom',
			],
		];

		$controls['brightness'] = [
			'label' => _x( 'Brightness', 'Filter Control', 'elementor-pro' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'template',
			'required' => 'true',
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'separator' => 'none',
			'condition' => [
				'filter_type' => 'custom',
			],
		];

		$controls['contrast'] = [
			'label' => _x( 'Contrast', 'Filter Control', 'elementor-pro' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'template',
			'required' => 'true',
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'separator' => 'none',
			'condition' => [
				'filter_type' => 'custom',
			],
		];

		$controls['saturate'] = [
			'label' => _x( 'Saturation', 'Filter Control', 'elementor-pro' ),
			'type' => Controls_Manager::SLIDER,
			'render_type' => 'template',
			'required' => 'true',
			'default' => [
				'size' => 100,
			],
			'range' => [
				'px' => [
					'min' => 0,
					'max' => 200,
				],
			],
			'separator' => 'none',
			'condition' => [
				'filter_type' => 'custom',
			],
		];

		return $controls;
	}

	/**
	 * Prepare fields.
	 *
	 * Process css_filter control fields before adding them to `add_control()`.
	 *
	 * @since 2.0.0
	 * @access protected
	 *
	 * @param array $fields CSS Filter control fields.
	 *
	 * @return array Processed fields.
	 */
	protected function prepare_fields( $fields ) {
		array_walk( $fields, function ( &$field, $field_name ) {
			if ( in_array( $field_name, [ 'css_filter', 'popover_toggle' ] ) ) {
				return;
			}

			$field['condition'] = [
				'css_filter' => 'custom',
			];
		} );

		return parent::prepare_fields( $fields );
	}

	/**
	 * @access protected
	 */
	protected function get_default_options() {
		return [
			'popover' => [
				'starter_name' => 'css_filter',
				'starter_title' => _x( 'CSS Filters', 'Filter Control', 'elementor-pro' ),
			],
		];
	}
}
