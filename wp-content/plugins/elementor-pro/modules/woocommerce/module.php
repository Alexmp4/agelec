<?php
namespace ElementorPro\Modules\Woocommerce;

use Elementor\TemplateLibrary\Source_Local;
use Elementor\Utils;
use ElementorPro\Base\Module_Base;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public static function is_active() {
		return function_exists( 'WC' );
	}

	public function get_name() {
		return 'woocommerce';
	}

	public function get_widgets() {
		return [
			'Products',
			'Add_To_Cart',
			'Elements',
			'Single_Elements',
			'Categories',
		];
	}

	public function add_product_post_class( $classes ) {
		$classes[] = 'product';

		return $classes;
	}

	public function add_products_post_class_filter() {
		add_filter( 'post_class', [ $this, 'add_product_post_class' ] );
	}

	public function remove_products_post_class_filter() {
		remove_filter( 'post_class', [ $this, 'add_product_post_class' ] );
	}

	public function register_tags() {
		// Allow WooCommerce tags only in a product or a layouts
		if ( ! Utils::is_ajax() && ! is_singular( [ Source_Local::CPT, 'product' ] ) ) {
			return;
		}

		require_once __DIR__ . '/classes/dynamic-tags.php';

		$tags = [
			'ElementorPro\Modules\Woocommerce\Classes\Woocommerce_Price',
			'ElementorPro\Modules\Woocommerce\Classes\Woocommerce_SKU',
			'ElementorPro\Modules\Woocommerce\Classes\Woocommerce_Stock_Text',
			'ElementorPro\Modules\Woocommerce\Classes\Woocommerce_Stock_Number',
			'ElementorPro\Modules\Woocommerce\Classes\Woocommerce_Sale',
			'ElementorPro\Modules\Woocommerce\Classes\Woocommerce_Gallery',
		];

		$module = Plugin::instance()->modules_manager->get_modules( 'dynamic-fields' );

		$module->register_group( 'woocommerce', 'Woocommerce' );

		foreach ( $tags as $tag ) {
			$module->register_tag( new $tag( [
				'id' => $tag,
				'settings' => [],
			] ) );
		}
	}

	public function register_wc_hooks() {
		wc()->frontend_includes();
	}

	public function __construct() {
		parent::__construct();

		add_action( 'elementor_pro/dynamic_fields/register_tags', [ $this, 'register_tags' ] );

		// On Editor - register Woocommerce frontend hooks - before the Editor init
		add_action( 'admin_action_elementor', [ $this, 'register_wc_hooks' ], 9 );
	}
}
