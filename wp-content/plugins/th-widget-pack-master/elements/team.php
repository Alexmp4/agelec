<?php
namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

class Themo_Widget_Team extends Widget_Base {

	public function get_name() {
		return 'themo-team';
	}

	public function get_title() {
		return __( 'Team Member', 'th-widget-pack' );
	}

	public function get_icon() {
		return 'eicon-person';
	}

	public function get_categories() {
		return [ 'themo-elements' ];
	}

	protected function _register_controls() {
		$this->start_controls_section(
			'section_about',
			[
				'label' => __( 'About', 'th-widget-pack' ),
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __( 'Image', 'th-widget-pack' ),
				'type' => Controls_Manager::MEDIA,
                'default' => [
                    'url' => Utils::get_placeholder_image_src(),
                ],
			]
		);

        $this->add_control(
            'post_image_size',
            [
                'label' => __( 'Image Size', 'th-widget-pack' ),
                'type' => Controls_Manager::SELECT,
                'default' => 'th_img_sm_standard',
                'options' => [
                    'th_img_sm_standard' => __( 'Standard', 'th-widget-pack' ),
                    'th_img_sm_landscape' => __( 'Landscape', 'th-widget-pack' ),
                    'th_img_sm_portrait' => __( 'Portrait', 'th-widget-pack' ),
                    'th_img_sm_square' => __( 'Square', 'th-widget-pack' ),
                    'th_img_lg' => __( 'Large', 'th-widget-pack' ),
                ],
                /*'selectors' => [
                    '{{WRAPPER}} {{CURRENT_ITEM}} .slick-slide-inner' => 'background-size: {{VALUE}}',
                ]*/
            ]
        );

		$this->add_control(
			'name',
			[
				'label' => __( 'Name', 'th-widget-pack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Justin Case', 'th-widget-pack' ),
				'placeholder' => __( 'Justin Case', 'th-widget-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'job',
			[
				'label' => __( 'Job Title', 'th-widget-pack' ),
				'type' => Controls_Manager::TEXT,
				'default' => __( 'Job position', 'th-widget-pack' ),
				'placeholder' => __( 'Job position', 'th-widget-pack' ),
				'label_block' => true,
			]
		);

		$this->add_control(
			'content',
			[
				'label' => __( 'Content', 'th-widget-pack' ),
				'type' => Controls_Manager::TEXTAREA,
				'label_block' => true,
				'default' => __( 'Nulla vitae elit libero, a pharetra augue. Sed posuere consectetur est at lobortis.', 'th-widget-pack' ),

			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_link',
			[
				'label' => __( 'Link', 'th-widget-pack' ),
			]
		);

		$this->add_control(
			'url',
			[
				'label' => __( 'Link URL', 'th-widget-pack' ),
				'type' => Controls_Manager::URL,
				'placeholder' => 'http://your-link.com',
				'default' => [
					'url' => '',
				],
				'separator' => 'before',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_social',
			[
				'label' => __( 'Social Icons', 'th-widget-pack' ),
			]
		);

		$this->add_control(
			'social',
			[
				'label' => __( 'Social Icons', 'th-widget-pack' ),
				'type' => Controls_Manager::REPEATER,
				'default' => [
					[
						'url' => 'http://your-link.com'
					]
				],
				'fields' => [
					[
						'name' => 'icon',
						'label' => __( 'Icon', 'th-widget-pack' ),
						'type' => Controls_Manager::ICON,
                        'label_block' => true,
                        'default' => 'fa fa-facebook',
						'options' => themo_icons(),
						'include' => themo_fa_icons()
					],
					[
						'name' => 'url',
						'label' => __( 'Link URL', 'th-widget-pack' ),
						'type' => Controls_Manager::URL,
						'placeholder' => 'http://your-link.com',
						'default' => [
							'url' => '',
						],
						'separator' => 'before',
						'label_block' => true,
					],
				],
				'title_field' => '<i class="{{ icon }}"></i> {{{ url.url }}}',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style_background',
			[
				'label' => __( 'Content', 'th-widget-pack' ),
				'tab' => Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'background_color',
			[
				'label' => __( 'Background Color', 'th-widget-pack' ),
				'type' => Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .th-team-member-content' => 'background-color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'name_color',
			[
				'label' => __( 'Name Color', 'th-widget-pack' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} h4' => 'color: {{VALUE}};',
				],
			]
		);

		$this->add_control(
			'job_color',
			[
				'label' => __( 'Job Title Color', 'th-widget-pack' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} h5' => 'color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
            'content_color',
            [
                'label' => __( 'Content Color', 'th-widget-pack' ),
                'type' => Controls_Manager::COLOR,
                'scheme' => [
                    'type' => Scheme_Color::get_type(),
                    'value' => Scheme_Color::COLOR_3,
                ],
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .th-team-member-text' => 'color: {{VALUE}};',
                ],
            ]
        );

		$this->add_control(
			'icon_color',
			[
				'label' => __( 'Icon Color', 'th-widget-pack' ),
				'type' => Controls_Manager::COLOR,
				'scheme' => [
					'type' => Scheme_Color::get_type(),
					'value' => Scheme_Color::COLOR_3,
				],
				'default' => '',
				'selectors' => [
					'{{WRAPPER}} i' => 'color: {{VALUE}};',
				],
			]
		);

		$this->end_controls_section();
	}

	protected function render() {
		$settings = $this->get_settings();

		if ( ! empty( $settings['url']['url'] ) ) {
			$this->add_render_attribute( 'link', 'href', esc_url( $settings['url']['url'] ) );

			if ( ! empty( $settings['url']['is_external'] ) ) {
				$this->add_render_attribute( 'link', 'target', '_blank' );
			}
		}

        if ( empty( $settings['image']['url'] ) ) {
            $image = false;
        }
        if ( isset($settings['post_image_size']) &&  $settings['post_image_size'] > "") {
            $image_size = esc_attr( $settings['post_image_size'] );
            if ( $settings['image']['id'] ) $image = wp_get_attachment_image( $settings['image']['id'], $image_size, false, array( 'class' => '' ) );
        }

        if ( isset( $settings['post_image_size'] ) &&  $settings['post_image_size'] > "" && isset( $settings['image']['id'] ) && $settings['image']['id'] > "" ) {
            $image_size = esc_attr( $settings['post_image_size'] );
            if ( $settings['image']['id'] ) $image = wp_get_attachment_image( $settings['image']['id'], $image_size, false, array( 'class' => 'th-img-stretch' ) );
        }elseif ( ! empty( $settings['image']['url'] ) ) {
            $this->add_render_attribute( 'image', 'src', esc_url( $settings['image']['url'] ) );
            $this->add_render_attribute( 'image', 'alt', esc_attr( Control_Media::get_image_alt( $settings['image'] ) ) );
            $this->add_render_attribute( 'image', 'title', esc_attr( Control_Media::get_image_title( $settings['image'] ) ) );
            $this->add_render_attribute( 'image', 'class', 'th-img-stretch' );
            $image = '<img ' . $this->get_render_attribute_string( 'image' ) . '>';
        }

		//if ( $settings['image']['id'] ) $image = wp_get_attachment_image( $settings['image']['id'], 'th_img_md_square', false, array( 'class' => 'th-team-member-image' ) );
		?>

		<div class="th-team-member">
            <?php if ( ! empty( $settings['url']['url'] ) ) : ?>
                <a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
                    <?php echo wp_kses_post( $image ); ?>
                </a>
            <?php else : ?>
                <?php echo wp_kses_post( $image ); ?>
            <?php endif; ?>

			<div class="th-team-member-content">
                <?php
                if ( empty( $settings['name'] ) ) {
                    return;
                } else { ?>
                    <?php if ( ! empty( $settings['url']['url'] ) ) : ?>
                        <a <?php echo $this->get_render_attribute_string( 'link' ); ?>>
                            <h4 class="th-team-member-name"><?php echo esc_html( $settings['name'] ) ?></h4>
                        </a>
                    <?php else : ?>
                        <h4 class="th-team-member-name"><?php echo esc_html( $settings['name'] ) ?></h4>
                    <?php endif; ?>
               <?php }  ?>
				<?php if ( ! empty( $settings['job'] ) ) : ?>
					<h5 class="th-team-member-title"><?php echo esc_html( $settings['job']) ?></h5>
				<?php endif;?>
				<?php if ( ! empty( $settings['content'] ) ) : ?>
					<div class="th-team-member-text"><?php echo wp_kses_post( $settings['content'] ); ?></div>
				<?php endif; ?>
				<div class="th-team-member-social">
					<?php foreach( $settings['social'] as $social ) {
						if ( ! empty( $social['url']['url'] ) ) {
							$target = $social['url']['is_external'] ? ' target="_blank"' : '';
							echo '<a href="' . esc_url( $social['url']['url'] ) . '"' . wp_kses_post( $target ) . '>';
						}
						if ( $social['icon'] ) : ?>
							<i class="<?php echo esc_attr( $social['icon'] ); ?>"></i>
						<?php endif;
						if ( ! empty( $social['url']['url'] ) ) {
							echo '</a>';
						}
					} ?>
				</div>
			</div>
		</div>

		<?php
	}

	protected function _content_template() {}

	/*
	 * <div class="th-team-member">
			<div class="th-team-member-content">
				<# if ( settings.url && settings.url.url ) { #>
					<a href="{{ settings.url.url }}">
				<# } #>
					<# if ( settings.image && '' !== settings.image.url ) { #>
						<img src="{{{ settings.image.url }}}" class="th-team-member-image" />
					<# } #>
					<# if ( '' !== settings.name ) { #>
						<h4>{{{ settings.name }}}</h4>
					<# } #>
				<# if ( settings.url && settings.url.url ) { #>
					</a>
				<# } #>
				<# if ( '' !== settings.job ) { #>
					<h5>{{{ settings.job }}}</h5>
				<# } #>
				<# if ( '' !== settings.content ) { #>
					<div class="th-team-member-bio">
						{{{ settings.content }}}
					</div>
				<# } #>
				<div class="th-team-member-social">
					<#
					if ( settings.social ) {
						_.each( settings.social, function( item ) { #>
							<# if ( item.url && item.url.url ) { #>
								<a href="{{ item.url.url }}">
							<# } #>
								<# if ( item.icon ) { #>
									<i class="{{ item.icon }}"></i>
								<# } #>
							<# if ( item.link && item.link.url ) { #>
								</a>
							<# } #>
						<#
						} );
					} #>
				</div>
			</div>
		</div>
	 *
	 * */
}

Plugin::instance()->widgets_manager->register_widget_type( new Themo_Widget_Team() );
