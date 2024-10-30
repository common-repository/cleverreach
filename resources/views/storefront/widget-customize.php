<?php
/**
 * @var $data
 */

?>

<p>
    <label for="<?php
	echo esc_attr( $data['field_id'] ); ?>"><?php
	    esc_html_e( 'Title:' ); ?></label>
    <input class="widefat" id="<?php
	echo esc_attr( $data['field_id'] ); ?>" name="<?php
	echo esc_attr( $data['field_name'] ); ?>" type="text" value="<?php
	echo esc_attr( $data['title'] ); ?>"/>
</p>
