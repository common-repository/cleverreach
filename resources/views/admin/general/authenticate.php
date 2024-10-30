<?php
/**
 * @var $data
 */

if ( isset( $data['options']['api_key'] ) && ! isset( $data['options']['token'] ) ): ?>
    <p><strong><?php
		    esc_html_e( 'You can switch to the new faster REST API at any time, just connect your account below.',
				'cleverreach' ); ?></strong></p>
<?php
endif; ?>

    <p class="description">
		<?php
		if ( isset( $data['options']['token'] ) ): ?>
			<?php
			esc_html_e( 'Your CleverReach account is connected.', 'cleverreach' ); ?>
            <a href="<?php
			echo esc_url( $data['auth_url'] . '?client_id=' . $data['client_id'] . '&grant=write&response_type=code&redirect_uri=' . $data['redirect_url'] ); ?>"
               class="button"><?php
	            esc_html_e( 'Reconnect', 'cleverreach' ); ?></a>
		<?php
		else: ?>
            <a href="<?php
			echo esc_url( $data['auth_url'] . '?client_id=' . $data['client_id'] . '&grant=write&response_type=code&redirect_uri=' . $data['redirect_url'] ); ?>"
               class="button"><?php
	            esc_html_e( 'Connect to CleverReach', 'cleverreach' ); ?></a>
		<?php
		endif; ?>
		<?php
		echo wp_kses( $data['message'], array( 'div' => array( 'class' => array() ), 'p' => array() ) ); // . ( isset( $result ) ? '<pre>'.print_r($result,true).'</pre>' : '' );
		?>
    </p>
    <input name='cleverreach_newsletter_settings[token]' id="cleverreach_newsletter_settings" type='hidden' value='<?php
	echo esc_attr( $data['options']['token'] ?? '' ); ?>' class="widefat"/>
<?php
$redirect = 'admin.php?page=cleverreach'; ?>
    <input type="hidden" name="_wp_http_referer" value="<?php
	echo esc_attr( $redirect ); ?>">