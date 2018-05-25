<?php

namespace ElementorPro\Modules\DynamicTags;

use Elementor\Modules\DynamicTags\Module as TagsModule;
use ElementorPro\Modules\DynamicTags\ACF;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends TagsModule {

	const AUTHOR_GROUP = 'author';

	const POST_GROUP = 'post';

	const COMMENTS_GROUP = 'comments';

	const SITE_GROUP = 'site';

	const ARCHIVE_GROUP = 'archive';

	public function __construct() {
		parent::__construct();

		if ( class_exists( '\acf' ) ) {
			$this->add_component( 'acf', new ACF\Module() );
		}
	}

	public function get_name() {
		return 'tags';
	}

	public function get_tag_classes_names() {
		return [
			'Archive_Description',
			'Archive_Meta',
			'Archive_Title',
			'Archive_URL',
			'Author_Info',
			'Author_Meta',
			'Author_Name',
			'Author_Profile_Picture',
			'Author_URL',
			'Comments_Number',
			'Comments_URL',
			'Post_Custom_Field',
			'Post_Date',
			'Post_Excerpt',
			'Post_Featured_Image',
			'Post_Gallery',
			'Post_ID',
			'Post_Terms',
			'Post_Time',
			'Post_Title',
			'Post_URL',
			'Site_Logo',
			'Site_Tagline',
			'Site_Title',
			'Site_URL',
			'Current_Date_Time',
		];
	}

	public function get_groups() {
		return [
			self::POST_GROUP => [
				'title' => __( 'Post', 'elementor-pro' ),
			],
			self::ARCHIVE_GROUP => [
				'title' => __( 'Archive', 'elementor-pro' ),
			],
			self::SITE_GROUP => [
				'title' => __( 'Site', 'elementor-pro' ),
			],
			self::COMMENTS_GROUP => [
				'title' => __( 'Comments', 'elementor-pro' ),
			],
			self::AUTHOR_GROUP => [
				'title' => __( 'Author', 'elementor-pro' ),
			],
		];
	}
}
