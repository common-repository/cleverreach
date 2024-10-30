<div class="haet-cleverreach-info-widget">
    <h3><?php
	    esc_html_e( 'How to use this form', 'cleverreach' ); ?></h3>
    <p><?php
	    esc_html_e( 'There are different ways to use this form:', 'cleverreach' ); ?></p>
    <ul>
        <li>
			<?php
			esc_html_e( 'Add the CleverReach widget to your sidebar', 'cleverreach' ); ?>
        </li>
        <li>
			<?php
			echo wp_kses( __('Use the <code>[cleverreach_signup]</code> shortcode anywhere in your pages or posts',
				'cleverreach' ), 'code'); ?>
        </li>
        <li>
			<?php
			esc_html_e( 'Drop this line of PHP code in your theme where you want to show the form.', 'cleverreach' ); ?><br>
            <code>&lt;?php if( function_exists( 'print_cleverreach_form' ) ) print_cleverreach_form(); ?&gt;</code>

        </li>
    </ul>
</div>