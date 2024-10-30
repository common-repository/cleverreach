<?php
/**
 * @var $data
 */

$options = $data['options'];
?>

<input name='cleverreach_newsletter_settings[show_comments_section_checkbox_default]' type='radio' value='1' <?php
echo( (int) $options['show_comments_section_checkbox_default'] === 1 ? 'checked' : '' ); ?> /> <?php
esc_html_e( 'Yes' ); ?> &nbsp; &nbsp;
<input name='cleverreach_newsletter_settings[show_comments_section_checkbox_default]' type='radio' value='0' <?php
echo esc_attr( (int) $options['show_comments_section_checkbox_default'] !== 1 ? 'checked' : '' ); ?> /> <?php
esc_html_e( 'No' ); ?>
<p class="description">
	<?php
	esc_html_e( 'Please make sure this option is allowed in your country.', 'cleverreach' ); ?>
</p>
