<?php
/**
 * @var $data
 */

?>
    <input name='cleverreach_newsletter_settings[show_comments_section_checkbox]' type='radio' value='1' <?php
	echo esc_attr( (int) $data['options']['show_comments_section_checkbox'] === 1 ? 'checked' : '' ); ?> /> <?php
esc_html_e( 'Yes' ); ?> &nbsp; &nbsp;
    <input name='cleverreach_newsletter_settings[show_comments_section_checkbox]' type='radio' value='0' <?php
	echo esc_attr( (int) $data['options']['show_comments_section_checkbox'] !== 1 ? 'checked' : '' ); ?> /> <?php
esc_html_e( 'No' ); ?>