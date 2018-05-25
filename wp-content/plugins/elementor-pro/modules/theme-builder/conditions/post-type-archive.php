<?php
namespace ElementorPro\Modules\ThemeBuilder\Conditions;

use ElementorPro\Modules\ThemeBuilder\Module;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Post_Type_Archive extends Condition_Base {

	private $post_type;
	private $post_taxonomies;

	public static function get_type() {
		return 'archive';
	}

	public function __construct( $data ) {
		$this->post_type = get_post_type_object( $data['post_type'] );
		$taxonomies = get_object_taxonomies( $data['post_type'], 'objects' );
		$this->post_taxonomies = wp_filter_object_list( $taxonomies, [
			'public' => true,
			'show_in_nav_menus' => true,
		] );

		parent::__construct();
	}

	public function get_name() {
		return $this->post_type->name . '_archive';
	}

	public function get_label() {
		return $this->post_type->label . ' ' . __( 'Archive', 'elementor-pro' );
	}

	public function get_all_label() {
		return $this->post_type->label . ' ' . __( 'Archive', 'elementor-pro' );
	}

	public function get_sub_conditions() {
		$sub_conditions = [];

		$conditions_manager = Module::instance()->get_conditions_manager();

		foreach ( $this->post_taxonomies as $slug => $object ) {
			$condition = new Taxonomy( [
				'object' => $object,
			] );
			$conditions_manager->register_condition_instance( $condition );
			$sub_conditions[] = $condition->get_name();
		}

		return $sub_conditions;
	}

	public function check( $args ) {
		return is_post_type_archive( $this->post_type->name ) || ( 'post' === $this->post_type->name && is_home() );
	}
}
