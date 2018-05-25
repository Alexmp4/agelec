<?php
namespace ElementorPro\Modules\ThemeBuilder\Widgets;

use Elementor\Controls_Manager;
use ElementorPro\Modules\Posts\Widgets\Posts_Base;
use ElementorPro\Modules\ThemeBuilder\Skins;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Class Posts
 */
class Archive_Posts extends Posts_Base {

	public function get_name() {
		return 'archive-posts';
	}

	public function get_title() {
		return __( 'Archive Posts', 'elementor-pro' );
	}

	public function get_icon() {
		return 'eicon-archive-posts';
	}

	public function get_categories() {
		return [ 'theme-elements' ];
	}

	protected function _register_skins() {
		$this->add_skin( new Skins\Posts_Archive_Skin_Classic( $this ) );
		$this->add_skin( new Skins\Posts_Archive_Skin_Cards( $this ) );
	}

	protected function _register_controls() {
		parent::_register_controls();

		$this->register_pagination_section_controls();

		$this->register_advanced_section_controls();

		$this->update_control(
			'pagination_type',
			[
				'default' => 'numbers',
			]
		);
	}

	public function register_advanced_section_controls() {
		$this->start_controls_section(
			'section_advanced',
			[
				'label' => __( 'Advanced ', 'elementor-pro' ),
			]
		);

		$this->add_control(
			'nothing_found_message',
			[
				'label' => __( 'Nothing Found Message', 'elementor-pro' ),
				'type' => Controls_Manager::TEXTAREA,
				'default' => __( 'It seems we can\'t find what you\'re looking for.', 'elementor-pro' ),
			]
		);

		$this->end_controls_section();
	}

	public function query_posts() {
		global $wp_query;

		$query_vars = $wp_query->query_vars;

		/**
		 * Posts archive query vars.
		 *
		 * Filters the post query variables when the theme loads the posts archive page.
		 *
		 * @since 2.0.0
		 *
		 * @param array $query_vars The query variables for the `WP_Query`.
		 */
		$query_vars = apply_filters( 'elementor/theme/posts_archive/query_posts/query_vars', $query_vars );

		if ( $query_vars !== $wp_query->query_vars ) {
			$this->query = new \WP_Query( $query_vars );
		} else {
			$this->query = $wp_query;
		}
	}
}
