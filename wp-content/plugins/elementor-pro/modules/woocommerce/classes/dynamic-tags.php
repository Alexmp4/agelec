<?php
namespace ElementorPro\Modules\Woocommerce\Classes;

use Elementor\Controls_Manager;
use ElementorPro\Modules\DynamicFields\Tags\Tag_Base;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

abstract class Woocommerce_Tag_Base extends Tag_Base {
	public function get_group() {
		return 'woocommerce';
	}
}

class Woocommerce_Price extends Woocommerce_Tag_Base {
	public function get_name() {
		return 'woocommerce-price';
	}

	public function get_label() {
		return __( 'Woocommerce Price', 'elementor-pro' );
	}

	protected function _register_controls() {
		$this->add_control( 'format', [
			'label' => __( 'Format', 'elementor-pro' ),
			'type' => Controls_Manager::SELECT,
			'options' => [
				'both'      => __( 'Both', 'elementor-pro' ),
				'original' => __( 'Original', 'elementor-pro' ),
				'sale'      => __( 'Sale', 'elementor-pro' ),
			],
			'default' => 'both',
		] );
	}

	public function get_value() {
		$product = wc_get_product();
		if ( ! $product ) {
			return '';
		}

		$format = $this->get_settings( 'format' );
		$value    = '';
		switch ( $format ) {
			case 'both':
				$value = $product->get_price_html();
				break;
			case 'original':
				$value = wc_price( $product->get_regular_price() ) . $product->get_price_suffix();
				break;
			case 'sale' && $product->is_on_sale():
				$value = wc_price( $product->get_sale_price() ) . $product->get_price_suffix();
				break;
		}

		return $value;
	}
}

class Woocommerce_SKU extends Woocommerce_Tag_Base {
	public function get_name() {
		return 'woocommerce-sku';
	}

	public function get_label() {
		return __( 'Woocommerce SKU', 'elementor-pro' );
	}

	public function get_value() {
		$product = wc_get_product();
		if ( ! $product ) {
			return '';
		}

		$value = '';

		if ( $product->get_sku() ) {
			$value = esc_html( $product->get_sku() );
		}

		return $value;
	}
}

class Woocommerce_Stock_Text extends Woocommerce_Tag_Base {
	public function get_name() {
		return 'woocommerce-stock-text';
	}

	public function get_label() {
		return __( 'Woocommerce Stock Text', 'elementor-pro' );
	}

	public function get_value() {
		$product = wc_get_product();
		if ( ! $product ) {
			return '';
		}

		$value = wc_get_stock_html( $product );

		return $value;
	}
}

class Woocommerce_Stock_Number extends Woocommerce_Tag_Base {
	public function get_name() {
		return 'woocommerce-stock-number';
	}

	public function get_label() {
		return __( 'Woocommerce Stock Number', 'elementor-pro' );
	}

	public function get_value() {
		$product = wc_get_product();
		if ( ! $product ) {
			return '';
		}

		$value = $product->get_stock_quantity();

		if ( ! $value ) {
			$value = '';
		}

		return $value;
	}
}

class Woocommerce_Sale extends Woocommerce_Tag_Base {
	public function get_name() {
		return 'woocommerce-sale';
	}

	public function get_label() {
		return __( 'Woocommerce Sale', 'elementor-pro' );
	}

	protected function _register_controls() {
		$this->add_control( 'text', [
			'label' => __( 'Text', 'elementor-pro' ),
			'type' => Controls_Manager::TEXT,
			'default' => __( 'Sale!', 'elementor-pro' ),
		] );
	}

	public function get_value() {
		$product = wc_get_product();
		if ( ! $product ) {
			return '';
		}

		$value = '';

		if ( $product->is_on_sale() ) {
			$value = $this->get_settings( 'text' );
		}

		return $value;
	}
}

class Woocommerce_Gallery extends Woocommerce_Tag_Base {
	public function get_name() {
		return 'woocommerce-gallery';
	}

	public function get_label() {
		return __( 'Woocommerce Gallery', 'elementor-pro' );
	}

	public static function get_type() {
		return 'gallery';
	}

	public function get_value() {
		$product = wc_get_product();
		if ( ! $product ) {
			return '';
		}
		$value = [];

		$attachment_ids = $product->get_gallery_image_ids();

		foreach ( $attachment_ids as $attachment_id ) {
			$value[] = [
				'id' => $attachment_id,
			];
		}

		return $value;
	}
}
