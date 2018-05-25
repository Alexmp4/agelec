<?php
namespace ElementorPro\Modules\ThemeBuilder\Skins;

use ElementorPro\Modules\Posts\Skins\Skin_Classic;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Posts_Archive_Skin_Classic extends Skin_Classic {

	protected function _register_controls_actions() {
		add_action( 'elementor/element/archive-posts/section_layout/before_section_end', [ $this, 'register_controls' ] );
		add_action( 'elementor/element/archive-posts/section_layout/after_section_end', [ $this, 'register_style_sections' ] );
	}

	public function get_id() {
		return 'archive_classic';
	}

	public function get_title() {
		return __( 'Classic', 'elementor-pro' );
	}

	public function render() {
		$this->parent->query_posts();

		$wp_query = $this->parent->get_query();

		if ( ! $wp_query->found_posts ) {
			$this->render_loop_header();

			echo '<div class="elementor-posts-nothing-found">' . esc_html( $this->parent->get_settings( 'nothing_found_message' ) ) . '</div>';

			$this->render_loop_footer();

			return;
		}

		parent::render();
	}

	public function get_container_class() {
		// Use parent class and parent css.
		return 'elementor-posts--skin-classic';
	}

	/* Remove `posts_per_page` control */
	protected function register_post_count_control(){}
}
