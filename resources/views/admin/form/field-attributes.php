<?php
/**
 * @var $data
 */

$options = $data['options'];
?>

<input name='cleverreach_newsletter_settings[<?php
echo esc_attr( $data['message'] ); ?>]' type='hidden' value='<?php
echo( isset( $options[ $data['message'] ] ) ? esc_attr( $options[ $data['message'] ] ) : '' ); ?>'/
