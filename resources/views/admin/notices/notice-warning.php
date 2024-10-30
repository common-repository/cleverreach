<?php
/**
 * @var $data
 */

?>
<div class="notice notice-warning">
    <p><?php
		echo wp_kses( $data['message'], 'strong' ); ?></p>
</div>