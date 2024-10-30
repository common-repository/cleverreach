<?php
/**
 * @var $data
 */

$options = $data['options'];
?>
<input name='cleverreach_newsletter_settings[<?php
echo esc_attr( $data['message'] ); ?>]' class="widefat" type='text' value='<?php
echo esc_attr( $options[ $data['message'] ] ); ?>'/>

