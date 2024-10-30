<?php
/**
 * @var $data
 */

?>
<select name="cleverreach_newsletter_settings[label_position_in_from]">
	<?php
	foreach ( $data['available_options'] as $val => $label ): ?>
        <option value="<?php
		echo esc_attr( $val ); ?>" <?php
		echo esc_attr( $data['options']['label_position_in_from'] === $val ? 'selected' : '' ) ?>><?php
			echo esc_html( $label ); ?></option>
	<?php
	endforeach; ?>
</select>