<?php
namespace ElementorPro\Modules\ThemeBuilder\Conditions;

use ElementorPro\Classes\Utils;
use ElementorPro\Modules\ThemeBuilder\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Singular extends Condition_Base {

	public static function get_type() {
		return 'singular';
	}

	public function get_name() {
		return 'singular';
	}

	public function get_label() {
		return __( 'Singular', 'elementor-pro' );
	}

	public function get_all_label() {
		return __( 'All Singular', 'elementor-pro' );
	}

	public function get_sub_conditions() {
		$sub_conditions = [
			'front_page',
		];

		$conditions_manager = Module::instance()->get_conditions_manager();
		$post_types = Utils::get_post_types();
		$post_types['attachment'] = get_post_type_object( 'attachment' )->label;

		foreach ( $post_types as $post_type => $label ) {
			$condition = new Post( [
				'post_type' => $post_type,
			] );
			$conditions_manager->register_condition_instance( $condition );
			$sub_conditions[] = $condition->get_name();
		}

		$sub_conditions[] = 'not_found404';

		return $sub_conditions;
	}

	public function check( $args ) {
		return is_singular() || is_404();
	}
}
