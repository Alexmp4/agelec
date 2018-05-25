<?php
namespace ElementorPro\Modules\ThemeBuilder\Classes;

use Elementor\TemplateLibrary\Source_Local;
use ElementorPro\Modules\ThemeBuilder\Documents;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Templates_Types_Manager {
	private $docs_types = [];

	public function __construct() {
		add_action( 'elementor_pro/init', [ $this, 'register_documents' ] );
	}

	public function get_types_config() {
		$config = [];

		foreach ( $this->docs_types as $type => $class_name ) {
			$config[ $type ] = call_user_func( [ $class_name, 'get_properties' ] );
		}

		return $config;
	}

	public function register_documents() {
		$this->docs_types = [
			'section' => Documents\Section::get_class_full_name(),
			'header' => Documents\Header::get_class_full_name(),
			'footer' => Documents\Footer::get_class_full_name(),
			'single' => Documents\Single::get_class_full_name(),
			'archive' => Documents\Archive::get_class_full_name(),
		];

		foreach ( $this->docs_types as $type => $class_name ) {
			Plugin::elementor()->documents->register_document_type( $type, $class_name );
			Source_Local::add_template_type( $type );
		}
	}
}
