<?php
/**
 * @var array $data
 */

$validation     = $data['validation'];
$is_widget      = $data['is_widget'];
$settings       = $data['settings'];
$attributes     = json_decode( $settings['form_attributes_used'] );
$label_position = isset( $settings['label_position_in_from'] ) ? $settings['label_position_in_from'] : 'left';
?>
<div class="haet-cleverreach">
	<?php
	if ( $validation && strlen( $validation->message ) > 0 ) {
		echo '<p class="' . ( $validation->valid ? 'message-success' : 'message-error' ) . '">' . $validation->message . '</p>';
	}

	if ( is_array( $attributes ) && $settings['selected_form_id'] !== '' && $settings['selected_group_list_id'] !== '' ) {
		//echo '<pre>'.print_r($attributes,true).'</pre>';
		?>
        <form method="post" class="haet-cleverreach-form">
            <input type="hidden" name="haet-cleverreach-is-widget" value="<?php
			echo( $is_widget ? '1' : '0' ); ?>">
            <input type="hidden" name="haet-cleverreach-form-id" value="<?php
			echo esc_attr( $settings['selected_form_id'] ); ?>">
            <input type="hidden" name="haet-cleverreach-list-id" value="<?php
			echo esc_attr( $settings['selected_group_list_id'] ); ?>">
			<?php
			foreach ( $attributes as $attribute ) {
				$field_has_error = isset( $validation, $validation_fields[ $attribute->field ] ) && ! $validation_fields[ $attribute->field ]->valid;

				if ( $attribute->type === 'policy_confirm' ): ?>
                    <div class="haet-cleverreach-field-wrap cleverreach-checkbox type-<?php
					echo esc_attr( $attribute->type ); ?> <?php
					echo esc_attr( $field_has_error ? 'field-error' : '' ); ?>">
                        <input type="checkbox" id="haet-cleverreach-<?php
						echo esc_attr( $attribute->field ); ?>" name="haet-cleverreach-<?php
						echo esc_attr( $attribute->field ); ?>" value="1" required>
                        <label for="haet-cleverreach-<?php
						echo esc_attr( $attribute->field ); ?>"><?php
							echo $attribute->label; ?></label>

						<?php
						if ( $field_has_error ): ?>
                            <p class="cleverreach-error-message"><?php
								echo esc_attr( $validation->fields[ $attribute->field ]->error ); ?></p>
						<?php
						endif; ?>
                    </div>
				<?php
				else: ?>
                    <div class="haet-cleverreach-field-wrap label-<?php
					echo esc_attr( $label_position); ?> type-<?php
					echo esc_attr( $attribute->type ); ?> <?php
					echo esc_attr( $field_has_error ? 'field-error' : '' ); ?>">
						<?php
						if ( $attribute->type === 'description' ): ?>
                            <p><?php
								echo nl2br( $attribute->label ); ?></p>
						<?php
                        elseif ( $attribute->type === 'submit' ): ?>
                            <button type="submit" class="button" id="haet-cleverreach-submit">
								<?php
								echo esc_html( $attribute->label ); ?>
                            </button>
						<?php
						else: ?>
							<?php
							if ( $label_position !== 'inside' ): ?>
                                <label for="haet-cleverreach-<?php
								echo esc_attr( $attribute->field ); ?>">
									<?php
									echo esc_html( $attribute->label ); ?>
                                </label>
							<?php
							endif; ?>
							<?php
							if ( $attribute->type === 'text' || $attribute->type === 'email' || $attribute->type === 'date' || $attribute->type === 'number'): ?>
                                <input type="<?php
								echo esc_attr( $attribute->type ); ?>"
                                       id="haet-cleverreach-<?php
								       echo esc_attr( $attribute->field ); ?>"
                                       name="haet-cleverreach-<?php
								       echo esc_attr( $attribute->field ); ?>"
                                       value="<?php
								       echo ( $field_has_error ? esc_attr( $validation->fields[ $attribute->field ]->value ) : '' ); ?>"
									<?php
									echo( ( $label_position === 'inside' ) ? ' placeholder="' . esc_attr( $attribute->label ) . '" ' : '' ); ?>
									<?php
									echo( $attribute->required ? ' required ' : '' ); ?>
                                >
							<?php
                            elseif ( $attribute->type === 'gender' ):
								$field_options = explode( "\n", $attribute->options );
								?>
                                <select id="haet-cleverreach-<?php
								echo esc_attr( $attribute->field ); ?>" name="haet-cleverreach-<?php
								echo esc_attr( $attribute->field ); ?>" <?php
								echo( $attribute->required ? ' required ' : '' ); ?>>
									<?php
									if ( $label_position === 'inside' ): ?>
                                        <option value=""><?php
											echo esc_html( $attribute->label ); ?></option>
									<?php
									endif; ?>
									<?php
									foreach ( $field_options as $option ): ?>
                                        <option><?php
											echo esc_html( $option ); ?></option>
									<?php
									endforeach; ?>
                                </select>
							<?php
							endif; ?>

							<?php
							if ( $field_has_error ): ?>
                                <p class="cleverreach-error-message">
									<?php
									echo esc_html( $validation->fields[ $attribute->field ]->error ); ?>
                                </p>
							<?php
							endif; ?>
						<?php
						endif; ?>
                    </div>
				<?php
				endif;
			} ?>
        </form>
		<?php
	}
	?>
</div>
