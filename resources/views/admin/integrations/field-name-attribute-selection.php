<?php
/**
 * @var $data
 */

$attributes_result = $data['attributes'];
$settings          = $data['settings'];
$language          = null;

?>
<?php
if ( isset( $language ) ){
?>
<img src="<?php
echo esc_url( $language['country_flag_url'] ); ?>" height="12" alt="<?php
echo esc_attr( $language['language_code'] ); ?>" width="18"/>
<select name="cleverreach_newsletter_settings[selected_name_attribute<?php
echo esc_attr( $language['language_code'] ); ?>]">
	<?php
	} else{
	?>
    <select name="cleverreach_newsletter_settings[selected_name_attribute]">
		<?php
		}

		if ( is_array( $attributes_result['global_attributes'] ) && count( $attributes_result['global_attributes'] ) > 0 ): ?>
            <optgroup label="<?php
            esc_html_e( 'Global attributes', 'cleverreach' ); ?>">
				<?php
				foreach ( $attributes_result['global_attributes'] as $attribute ): ?>
					<?php
					if ( $attribute['type'] === 'text' ): ?>
                        <option value="<?php
						echo esc_attr( $attribute['name'] ); ?>" <?php
						echo esc_attr( isset( $settings['selected_name_attribute'] ) && $settings['selected_name_attribute'] === $attribute['name'] ? 'selected' : '' ); ?>>
							<?php
							echo esc_html( $attribute['description'] ); ?>
                        </option>
					<?php
					endif;
				endforeach;
				?>
            </optgroup>
		<?php
		endif; ?>
		<?php
		if ( is_array( $attributes_result['list_attributes'] ) && count( $attributes_result['list_attributes'] ) > 0 ): ?>
            <optgroup label="<?php
            esc_html_e( 'List attributes', 'cleverreach' ); ?>">
				<?php
				foreach ( $attributes_result['list_attributes'] as $attribute ): ?>
					<?php
					if ( $attribute['type'] === 'text' ): ?>
                        <option value="<?php
						echo esc_attr( $attribute['name'] ); ?>" <?php
						echo esc_attr( isset( $settings['selected_name_attribute'] ) && $settings['selected_name_attribute'] === $attribute['name'] ? 'selected' : '' ); ?>>
							<?php
							echo esc_html( $attribute['description'] ); ?>
                        </option>
					<?php
					endif;
				endforeach;
				?>
            </optgroup>

		<?php
		endif; ?>
    </select>
    <p class="notice list-change-notice">
		<?php
		esc_html_e( 'Please save your form changes first in order to refresh available attributes.',
			'cleverreach' ); ?>
    </p>
    <p class="description">
		<?php
		esc_html_e( 'Select a CleverReach attribute to store the name of the comment author.',
			'cleverreach' ); ?>
    </p>
