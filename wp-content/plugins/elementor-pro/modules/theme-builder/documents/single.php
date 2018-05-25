<?php
namespace ElementorPro\Modules\ThemeBuilder\Documents;

use Elementor\DB;
use ElementorPro\Classes\Utils;
use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Modules\ThemeBuilder\Widgets\Post_Content;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Single extends Theme_Page_Document {

	public static function get_properties() {
		$properties = parent::get_properties();

		$properties['location'] = 'single';
		$properties['condition_type'] = 'singular';

		return $properties;
	}

	public function get_name() {
		return 'single';
	}

	public static function get_title() {
		return __( 'Single', 'elementor-pro' );
	}

	protected function _register_controls() {
		parent::_register_controls();

		$latest_posts = get_posts( 'posts_per_page=1' );

		if ( ! empty( $latest_posts ) ) {
			$this->update_control(
				'preview_type',
				[
					'default' => 'single/post',
				]
			);

			$this->update_control(
				'preview_id',
				[
					'default' => $latest_posts[0]->ID,
				]
			);
		}
	}

	public static function get_preview_as_options() {
		$post_types = Utils::get_post_types();
		$post_types['attachment'] = get_post_type_object( 'attachment' )->label;
		$post_types_options = [];

		foreach ( $post_types as $post_type => $label ) {
			$post_types_options[ 'single/' . $post_type ] = get_post_type_object( $post_type )->labels->singular_name;
		}

		return [
			'single' => [
				'label' => __( 'Single', 'elementor-pro' ),
				'options' => $post_types_options,
			],
			'page/404' => __( '404', 'elementor-pro' ),
		];
	}

	public function get_elements_data( $status = DB::STATUS_PUBLISH ) {
		$data = parent::get_elements_data();

		if ( Plugin::elementor()->preview->is_preview_mode() && self::get_property( 'location' ) === Module::instance()->get_locations_manager()->get_current_location() ) {
			$has_the_content = false;

			Plugin::elementor()->db->iterate_data( $data, function ( $element ) use ( $has_the_content ) {
				if ( isset( $element['widgetType'] ) && Post_Content::get_type() === $element['widgetType'] ) {
					$has_the_content = true;
				}
			} );

			if ( ! $has_the_content ) {
				add_action( 'wp_footer', [ $this, 'preview_error_handler' ] );
			}
		}

		return $data;
	}

	public function preview_error_handler() {
		wp_localize_script( 'elementor-frontend', 'elementorPreviewErrorArgs', [
			'headerMessage' => __( 'The Post Content Widget was not found in your template.', 'elementor-pro' ),
			'message' => sprintf(
				/* translators: %s: Template name. */
				__( 'You must include the Post Content Widget in your template (%s), in order for Elementor to work on this page.', 'elementor-pro' ),
				'<strong>' . self::get_title() . '</strong>'
			),
			'strings' => [
				'confirm' => __( 'Edit Template', 'elementor-pro' ),
			],
			'confirmURL' => $this->get_edit_url(),
		] );
	}
}
