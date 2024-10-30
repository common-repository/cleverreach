<?php
/**
 * @var $data
 */

if ( is_array( $data['group_lists'] ) && count( $data['group_lists'] ) ):
	foreach ( $data['group_lists'] as $list ): ?>
        <optgroup label="<?php
		echo esc_html( __( 'List:', 'cleverreach' ) . ' ' . $list['name'] ); ?>">
			<?php
			if ( isset( $list['forms'] ) && is_array( $list['forms'] ) && count( $list['forms'] ) ):
				foreach ( $list['forms'] as $form ): ?>
                    <option value="<?php
					echo esc_attr( $list['id'] . '-' . $form['id'] ) ?>" <?php
					echo esc_attr( isset( $data['selected_value'] ) && $data['selected_value'] === $list['id'] . '-' . $form['id'] ? 'selected' : '' ); ?>>
						<?php
						echo esc_html( $form['name'] ); ?>
                    </option>
				<?php
				endforeach;
			endif; ?>
        </optgroup>
	<?php
	endforeach; ?>
<?php
endif;