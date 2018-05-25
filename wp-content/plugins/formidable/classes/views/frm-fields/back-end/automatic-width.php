<tr>
	<td class="frm_150_width"><label><?php esc_html_e( 'Field Size', 'formidable' ) ?></label></td>
	<td>
		<label for="size_<?php echo esc_attr( $field['id'] ) ?>">
			<input type="checkbox" name="field_options[size_<?php echo esc_attr( $field['id'] ) ?>]" id="size_<?php echo esc_attr( $field['id'] ) ?>" value="1" <?php echo FrmField::is_option_true( $field, 'size' ) ? 'checked="checked"' : ''; ?> />
			<?php esc_html_e( 'automatic width', 'formidable' ) ?>
		</label>
	</td>
</tr>
