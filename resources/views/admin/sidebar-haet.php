<div class="haet-cleverreach-info-widget">
    <h3><?php
	    esc_html_e( 'Looking for Support?', 'cleverreach' ); ?></h3>

    <p><?php
		printf( wp_kses( __( 'Please check out our <a href="%s">helpcenter</a> or our <a href="%s">seminars.</a>',
			'cleverreach' ), array( 'a' => array ( 'href' => array() ) ) ),
			esc_url( __( 'https://support.cleverreach.de/hc/en-us/', 'cleverreach' ) ),
			"https://www.cleverreach.com/de/seminare/" ) ?></p>
</div>