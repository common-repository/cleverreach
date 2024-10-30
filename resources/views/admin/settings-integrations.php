<?php
/**
 * @var $data
 */

?>

<div id="haet-cleverreach-settings-integrations" class="wrap haet-cleverreach-settings">

    <h2><img src="https://cloud-files.crsend.com/images/cleverreach-logo.png"/> <?php
	    esc_html_e( 'Integration Settings', 'cleverreach' ); ?></h2>
	<?php
	if ( $data['access_token'] === null ) { ?>
        <p class="error dashicons-before dashicons-no-alt">
			<?php
			echo wp_kses( $data['message'], array(
				'a' => array( 'href' => array() ),
				'strong' => array() ) ); ?>
        </p>
	<?php
	} elseif ( ! $data['is_successful'] ) { ?>
        <p class="message dashicons-before dashicons-lightbulb">
			<?php
			echo wp_kses( $data['message'], array(
				'a' => array( 'href' => array() ),
				'strong' => array() ) ); ?>
        </p>
	<?php
	} ?>
    <form action="options.php" method="post">
		<?php
		settings_fields( 'cleverreach_newsletter_option_group' ); ?>
		<?php
		do_settings_sections( 'cleverreach_page_cleverreach-integrations' ); ?>

		<?php
		submit_button(); ?>

    </form>
</div>
<div class="haet-cleverreach-sidebar">
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-features.php'; ?>
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-woocommerce.php'; ?>
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-haet.php'; ?>
</div>