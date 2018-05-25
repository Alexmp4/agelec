<?php
namespace ElementorPro\Modules\ThemeBuilder\Classes;

use Elementor\Post_CSS_File;
use ElementorPro\Classes\Utils;
use ElementorPro\Modules\ThemeBuilder\Documents\Theme_Document;
use ElementorPro\Modules\ThemeBuilder\Module;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

class Locations_Manager {

	protected $core_locations = [];
	protected $locations = [];
	protected $did_locations = [];
	protected $current_location;

	public function __construct() {
		$this->set_core_locations();

		add_filter( 'the_content', [ $this, 'builder_wrapper' ], 9999999 ); // 9999999 = after preview->builder_wrapper
		add_filter( 'template_include', [ $this, 'template_include' ], 11 ); // 11 = after WooCommerce.
		add_action( 'template_redirect', [ $this, 'register_locations' ] );

		add_filter( 'elementor/admin/create_new_post/meta', [ $this, 'filter_add_location_meta_on_create_new_post' ] );

		if ( ! Module::is_preview() ) {
			add_action( 'wp_enqueue_scripts', [ $this, 'enqueue_styles' ] );
		}
	}

	public function register_locations() {
		// Run Once.
		if ( ! did_action( 'elementor/theme/register_locations' ) ) {
			/**
			 * Register theme locations.
			 *
			 * Fires after template files where included but before locations
			 * have been registered.
			 *
			 * This is where Elementor theme locations are registered by
			 * external themes.
			 *
			 * @since 2.0.0
			 *
			 * @param Locations_Manager $this An instance of locations manager.
			 */
			do_action( 'elementor/theme/register_locations', $this );
		}
	}

	public function enqueue_styles() {
		$locations = $this->get_locations();

		if ( empty( $locations ) ) {
			return;
		}

		Plugin::elementor()->frontend->enqueue_styles();
		$current_post_id = get_the_ID();

		foreach ( $locations as $location => $settings ) {
			$documents = Module::instance()->get_conditions_manager()->get_documents_for_location( $location );
			foreach ( $documents as $document ) {
				$post_id = $document->get_post()->ID;

				// Don't enqueue current post here (let the  preview/frontend components to handle it)
				if ( $current_post_id !== $post_id ) {
					$css_file = new Post_CSS_File( $post_id );
					$css_file->enqueue();
				}
			}
		}
	}

	public function template_include( $template ) {
		$location = '';

		// Compatibility
		if ( class_exists( '\WC_Template_Loader' ) ) {
			$woocommerce_template = \WC_Template_Loader::template_loader( '' );
			if ( $woocommerce_template ) {
				return $woocommerce_template;
			}
		}

		if ( is_singular() ) {
			$document = Plugin::elementor()->documents->get_doc_for_frontend( get_the_ID() );
			if ( $document && $document::get_property( 'support_wp_page_templates' ) ) {
				$wp_page_template = $document->get_meta( '_wp_page_template' );
				if ( $wp_page_template && 'default' !== $wp_page_template ) {
					return $template;
				}
			}
		} else {
			$document = false;
		}

		if ( $document && $document instanceof Theme_Document ) {
			// For editor preview iframe.
			$location = $document->get_location();
		} elseif ( is_archive() || is_home() || is_search() ) {
			$location = 'archive';
		} elseif ( is_singular() || is_404() ) {
			$location = 'single';
		}

		if ( $location ) {
			$location_settings = $this->get_locations( $location );
			$location_documents = Module::instance()->get_conditions_manager()->get_documents_for_location( $location );
			if ( empty( $location_documents ) ) {
				return $template;
			}

			if ( 'single' === $location || 'archive' === $location ) {
				$theme_document = $location_documents[0];

				if ( Module::is_preview() && $theme_document->get_autosave_id() ) {
					$theme_document = $theme_document->get_autosave();
				}

				$document_page_template = $theme_document->get_settings( 'page_template' );
				if ( $document_page_template ) {
					$page_template = $document_page_template;
				}
			}
		}

		/**
		 * @var \Elementor\Modules\PageTemplates\Module $page_templates_module
		 */
		$page_templates_module = Plugin::elementor()->modules_manager->get_modules( 'page-templates' );

		// If is a `content` document or the theme is not support the document location (top header/ sidebar and etc.).
		$location_exist = ! empty( $location_settings );
		$is_header_footer = 'header' === $location || 'footer' === $location;
		$need_override_location = ! empty( $location_settings['overwrite'] ) && ! $is_header_footer;

		if ( $location && empty( $page_template ) && ( ! $location_exist || $need_override_location ) ) {
			$page_template = $page_templates_module::TEMPLATE_HEADER_FOOTER;
		}

		if ( ! empty( $page_template ) ) {
			$template_path = $page_templates_module->get_template_path( $page_template );

			if ( $template_path ) {
				$page_templates_module->set_print_callback( function() use ( $location ) {
					Module::instance()->get_locations_manager()->do_location( $location );
				} );

				$template = $template_path;
			}
		}

		return $template;
	}

	public function do_location( $location ) {
		$documents = Module::instance()->get_conditions_manager()->get_documents_for_location( $location );

		if ( is_singular() ) {
			Utils::set_global_authordata();
		}

		if ( empty( $documents ) ) {
			return false;
		}

		/**
		 * Before location content printed.
		 *
		 * Fires before Elementor location was printed.
		 *
		 * The dynamic portion of the hook name, `$location`, refers to the location name.
		 *
		 * @since 2.0.0
		 *
		 * @param Locations_Manager $this An instance of locations manager.
		 */
		do_action( "elementor/theme/before_do_{$location}", $this );

		foreach ( $documents as $document ) {
			$this->current_location = $location;
			$this->print_content( $document );
			$this->did_locations[] = $this->current_location;
			$this->current_location = null;
		}

		/**
		 * After location content printed.
		 *
		 * Fires after Elementor location was printed.
		 *
		 * The dynamic portion of the hook name, `$location`, refers to the location name.
		 *
		 * @since 2.0.0
		 *
		 * @param Locations_Manager $this An instance of locations manager.
		 */
		do_action( "elementor/theme/after_do_{$location}", $this );

		return true;
	}

	public function did_location( $location ) {
		return in_array( $location, $this->did_locations, true );
	}

	public function get_current_location() {
		return $this->current_location;
	}

	public function builder_wrapper( $content ) {
		$post_id = get_the_ID();

		if ( $post_id ) {
			$document = Module::instance()->get_document( $post_id );
			if ( $document ) {
				$document_location = $document->get_location();
				$location_settings = $this->get_locations( $document_location );
				// If is a `content` document or the theme is not support the document location (header/footer and etc.).
				if ( $location_settings && ! $location_settings['edit_in_content'] ) {
					$content = '<div class="elementor-theme-builder-content-area">' . __( 'Content Area', 'elementor-pro' ) . '</div>';
				}
			}
		}

		return $content;
	}

	/**
	 * @param Theme_Document $document
	 */
	private function print_content( $document ) {
		$current_post_id = get_the_ID();
		$plugin = Plugin::elementor();
		$current_location = $this->get_current_location();
		$location_settings = $this->get_locations( $current_location );
		$current_document = Module::instance()->get_document( $current_post_id );

		$is_panel_preview = $plugin->preview->is_preview_mode( $document->get_main_id() );
		$is_current_theme_template_match_edited_post = $current_post_id === $document->get_post()->ID;
		$is_current_location_match_document_location = $current_document && $current_document->get_location() === $current_location;

		/**
		 * $print can be content/builder/placeholder
		 * `content` if it's not the edited post, or the edited post in frontend
		 * `builder` if it's the edited post in the right place
		 * `placeholder` for `the_content` area - if current theme template is not content type ( like header/footer/sidebar )
		 */

		$print = 'content';
		$content = '';

		if ( $is_panel_preview && $is_current_theme_template_match_edited_post && $is_current_location_match_document_location ) {
			$print = 'builder';
		}

		if ( $current_document && ! $is_current_theme_template_match_edited_post && ! $is_current_location_match_document_location && $location_settings && $location_settings['edit_in_content'] ) {
			$print = 'placeholder';
		}

		if ( 'content' === $print ) {
			$content = $document->get_content();
		} elseif ( 'builder' === $print ) {
			$content = $plugin->preview->builder_wrapper( '' );
		} elseif ( 'placeholder' === $print ) {
			$content = '<div class="elementor-theme-builder-content-area">' . __( 'Content Area', 'elementor-pro' ) . '</div>';
		}

		echo $content; // XSS ok.
	}

	public function get_locations( $location = null ) {
		if ( is_null( $location ) ) {
			return $this->locations;
		}

		if ( isset( $this->locations[ $location ] ) ) {
			$location_config = $this->locations[ $location ];
		} else {
			$location_config = [];
		}

		return $location_config;
	}

	public function get_doc_location( $post_id ) {
		/** @var Theme_Document $document */
		$document = Plugin::elementor()->documents->get( $post_id );
		return $document->get_location();
	}

	public function get_core_locations() {
		return $this->core_locations;
	}

	public function register_all_core_location() {
		foreach ( $this->core_locations as $location => $settings ) {
			$this->register_location( $location, $settings );
		}
	}

	public function register_location( $location, $args = [] ) {
		$args = wp_parse_args( $args, [
			'label' => $location,
			'multiple' => false,
			'edit_in_content' => true,
			'hook' => 'elementor/theme/' . $location,
		] );

		$this->locations[ $location ] = $args;

		add_action( $args['hook'], function() use ( $location, $args ) {
			$did_location = Module::instance()->get_locations_manager()->do_location( $location );

			if ( $did_location && ! empty( $args['remove_hooks'] ) ) {
				foreach ( $args['remove_hooks'] as $item ) {
					remove_action( $args['hook'], $item );
				}
			}
		}, 5 );
	}

	public function register_core_location( $location, $args = [] ) {
		if ( ! isset( $this->core_locations[ $location ] ) ) {
			/* translators: %s: Location name. */
			wp_die( esc_html( sprintf( __( 'Location \'%s\' is not a core location.', 'elementor-pro' ), $location ) ) );
		}

		$args = array_replace_recursive( $this->core_locations[ $location ], $args );

		$this->register_location( $location, $args );
	}

	public function get_locations_without_core() {
		$locations = $this->get_locations();

		foreach ( $this->core_locations as $location => $settings ) {
			unset( $locations[ $location ] );
		}

		return $locations;
	}

	public function location_exits( $location = '', $check_match = false ) {
		$location_exits = ! ! $this->get_locations( $location );

		if ( $location_exits && $check_match ) {
			$location_exits = ! ! Module::instance()->get_conditions_manager()->get_documents_for_location( $location );
		}

		return $location_exits;
	}

	public function filter_add_location_meta_on_create_new_post( $meta ) {
		if ( ! empty( $_GET['meta_location'] ) ) {
			$meta[ Theme_Document::LOCATION_META_KEY ] = $_GET['meta_location'];
		}

		return $meta;
	}

	private function set_core_locations() {
		$this->core_locations = [
			'header' => [
				'is_core' => true,
				'label' => __( 'Header', 'elementor-pro' ),
				'edit_in_content' => false,
			],
			'footer' => [
				'is_core' => true,
				'label' => __( 'Footer', 'elementor-pro' ),
				'edit_in_content' => false,
			],
			'archive' => [
				'is_core' => true,
				'overwrite' => true,
				'label' => __( 'Archive', 'elementor-pro' ),
				'edit_in_content' => true,
			],
			'single' => [
				'is_core' => true,
				'label' => __( 'Single', 'elementor-pro' ),
				'edit_in_content' => true,
			],
		];
	}
}
