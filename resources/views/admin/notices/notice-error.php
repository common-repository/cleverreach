<?php
/**
 * @var $data
 */

?>
<div class="notice notice-error">
    <p><?php
		echo wp_kses( $data['message'], array( 'a' => array( 'href' => array() ), 'strong' => array() ) ); ?></p>
</div>
