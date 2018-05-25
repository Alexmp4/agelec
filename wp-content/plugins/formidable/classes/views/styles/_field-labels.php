<div class="field-group field-group-background clearfix frm-first-row">
	<label><?php esc_html_e( 'Color', 'formidable' ) ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'label_color' ) ); ?>" id="frm_label_color" class="hex" value="<?php echo esc_attr( $style->post_content['label_color'] ); ?>" />
</div>
<div class="field-group clearfix frm-first-row">
	<label><?php esc_html_e( 'Weight', 'formidable' ) ?></label>
	<select name="<?php echo esc_attr( $frm_style->get_field_name( 'weight' ) ); ?>" id="frm_weight">
		<?php foreach ( FrmStyle::get_bold_options() as $value => $name ) { ?>
		<option value="<?php echo esc_attr( $value ) ?>" <?php selected( $style->post_content['weight'], $value ) ?>><?php echo esc_html( $name ) ?></option>
		<?php } ?>
	</select>
</div>
<div class="field-group clearfix frm-first-row">
	<label><?php esc_html_e( 'Size', 'formidable' ) ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'font_size' ) ); ?>" id="frm_font_size" value="<?php echo esc_attr( $style->post_content['font_size'] ); ?>"  size="3" />
</div>

<div class="field-group clearfix frm_clear">
	<label><?php esc_html_e( 'Position', 'formidable' ) ?></label>
	<select name="<?php echo esc_attr( $frm_style->get_field_name( 'position' ) ); ?>" id="frm_position">
	    <?php foreach ( FrmStylesHelper::get_css_label_positions() as $pos => $pos_label ) { ?>
			<option value="<?php echo esc_attr( $pos ); ?>" <?php selected( $style->post_content['position'], $pos ); ?>><?php echo esc_html( $pos_label ); ?></option>
	    <?php } ?>
	</select>
</div>

<div class="field-group clearfix">
	<label for="frm_align"><?php esc_html_e( 'Align', 'formidable' ); ?></label>
	<select name="<?php echo esc_attr( $frm_style->get_field_name( 'align' ) ); ?>" id="frm_align">
		<option value="left" <?php selected( $style->post_content['align'], 'left' ); ?>>
			<?php esc_html_e( 'left', 'formidable' ); ?>
		</option>
		<option value="right" <?php selected( $style->post_content['align'], 'right' ); ?>>
			<?php esc_html_e( 'right', 'formidable' ); ?>
		</option>
	</select>
</div>

<div class="field-group clearfix">
	<label><?php esc_html_e( 'Width', 'formidable' ) ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'width' ) ); ?>" id="frm_width" value="<?php echo esc_attr( $style->post_content['width'] ); ?>" />
</div>

<div class="field-group clearfix frm_clear">
	<label><?php esc_html_e( 'Padding', 'formidable' ) ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'label_padding' ) ); ?>" id="frm_label_padding" value="<?php echo esc_attr( $style->post_content['label_padding'] ); ?>" />
</div>

<div class="clear"></div>
<h3><?php esc_html_e( 'Required Indicator', 'formidable' ) ?></h3>
<div class="field-group field-group-border clearfix after-h3">
	<label class="background"><?php esc_html_e( 'Color', 'formidable' ) ?></label>
	<input type="text" name="<?php echo esc_attr( $frm_style->get_field_name( 'required_color' ) ); ?>" id="frm_required_color" class="hex" value="<?php echo esc_attr( $style->post_content['required_color'] ); ?>" />
</div>
<div class="field-group clearfix after-h3">
	<label><?php esc_html_e( 'Weight', 'formidable' ) ?></label>
	<select name="<?php echo esc_attr( $frm_style->get_field_name( 'required_weight' ) ); ?>" id="frm_required_weight">
		<?php foreach ( FrmStyle::get_bold_options() as $value => $name ) { ?>
		<option value="<?php echo esc_attr( $value ) ?>" <?php selected( $style->post_content['required_weight'], $value ) ?>><?php echo esc_html( $name ) ?></option>
		<?php } ?>
	</select>
</div>
<div class="clear"></div>
