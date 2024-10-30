<?php

/**
 * @var $data
 */
$settings = $data['settings'];

?>
<p class="cleverreach-checkbox cleverreach-checkbox-comments">
    <input type="checkbox" name="cleverreach_checkbox_comments" id="cleverreach_checkbox_comments" <?php
	echo esc_attr( isset( $settings['show_comments_section_checkbox_default'] ) && $settings['show_comments_section_checkbox_default'] == 1 ? 'checked' : '' ); ?>
           value="1"/>
    <label for="cleverreach_checkbox_comments"><?php
		echo esc_html( $settings['caption_for_comments_section_checkbox'] ); ?></label>
</p>
