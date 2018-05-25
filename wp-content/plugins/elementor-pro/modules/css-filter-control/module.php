<?php
namespace ElementorPro\Modules\CssFilterControl;

use ElementorPro\Base\Module_Base;
use ElementorPro\Modules\CssFilterControl\Controls\Group_Control_Css_Filter;
use ElementorPro\Plugin;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Module extends Module_Base {

	public function __construct() {
		parent::__construct();

		add_action( 'elementor/controls/controls_registered', [ $this, 'register_controls' ] );
	}

	public function get_name() {
		return 'css-filter-control';
	}

	public function register_controls() {
		Plugin::elementor()->controls_manager->add_group_control( Group_Control_Css_Filter::get_type(), new Group_Control_Css_Filter() );
	}
}
