<?php

namespace ElementorPro\Modules\DynamicTags\ACF;

use Elementor\Modules\DynamicTags;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends DynamicTags\Module {

	const ACF_GROUP = 'acf';

	/**
	 * @param array $types
	 *
	 * @return array
	 */
	public static function get_control_options( $types ) {
		// ACF >= 5.0.0
		if ( function_exists( 'acf_get_field_groups' ) ) {
			$acf_groups = acf_get_field_groups();
		} else {
			$acf_groups = apply_filters( 'acf/get_field_groups', [] );
		}

		$groups = [];

		foreach ( $acf_groups as $acf_group ) {
			// ACF >= 5.0.0
			if ( function_exists( 'acf_get_fields' ) ) {
				$fields = acf_get_fields( $acf_group['ID'] );
			} else {
				$fields = apply_filters( 'acf/field_group/get_fields', [], $acf_group['id'] );
			}

			$options = [];

			foreach ( $fields as $field ) {
				if ( ! in_array( $field['type'], $types, true ) ) {
					continue;
				}

				// Use group ID for unique keys
				$key = $field['key'] . ':' . $field['name'];
				$options[ $key ] = $field['label'];
			}

			if ( empty( $options ) ) {
				continue;
			}

			$groups[] = [
				'label' => $acf_group['title'],
				'options' => $options,
			];
		}

		return $groups;
	}

	public function get_tag_classes_names() {
		return [
			'ACF_Text',
			'ACF_Image',
			'ACF_URL',
			'ACF_Gallery',
		];
	}

	public function get_groups() {
		return [
			self::ACF_GROUP => [
				'title' => __( 'ACF', 'elementor-pro' ),
			],
		];
	}
}
