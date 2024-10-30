<?php
/**
 * @var $data
 */

$settings = $data['settings'];
?>

<div id="haet-cleverreach-settings-general" class="wrap haet-cleverreach-settings">

    <h2><img src="https://cloud-files.crsend.com/images/cleverreach-logo.png"/> <?php
	    esc_html_e( 'General Settings', 'cleverreach' ); ?></h2>

    <form action="options.php" method="post">
		<?php
		if ( isset( $_GET["cleverreach-athenticate"] ) && isset( $_GET["code"] ) ) : ?>
            <p class="message dashicons-before dashicons-lightbulb">
				<?php
				esc_html_e( 'Connection initialized. Please save your settings to test connection and load your lists.',
					'cleverreach' ); ?>
            </p>
		<?php
        elseif ( $data['is_successful'] ): ?>
            <p class="success dashicons-before dashicons-yes">
				<?php
				echo wp_kses( $data['api_message'], array(
				        'a' => array( 'href' => array(), 'target' => array() ),
                        'br' => array(),
                        'strong' => array() ) ); ?>
            </p>
		<?php
        elseif ( $data['access_token'] === null ): ?>
            <p class="error dashicons-before dashicons-no-alt">
				<?php
				echo wp_kses( $data['api_message'], array(
				        'a' => array( 'href' => array(), 'target' => array() ),
                        'br' => array(),
                        'strong' => array() ) ); ?>
            </p>
		<?php
		else:
            foreach($data['form_messages'] as $message){?>
            <p class="message dashicons-before dashicons-lightbulb">
				<?php
				echo wp_kses( $message, 'strong' ); ?>
            </p>
		<?php }
		endif; ?>

		<?php
		settings_fields( 'cleverreach_newsletter_option_group' ); ?>
		<?php
		do_settings_sections( 'toplevel_page_cleverreach' ); ?>

		<?php
		submit_button(); ?>

    </form>
	<?php
	if ( $data['refresh_lists'] && ! $data['list_result']['success'] ): ?>
        <p class="error dashicons-before dashicons-no-alt">
			<?php
			echo esc_html( $data['list_result']['message'] ); ?>
        </p>
	<?php
	endif; ?>

	<?php
	if ( array_key_exists( 'cleverreach_id', $settings ) ) { ?>
        <h3>CleverReach ID: <?php
			echo esc_html( $settings['cleverreach_id'] ); ?></h3>
		<?php
	}
	if ( ! empty( $settings['group_lists'] ) && is_array( $settings['group_lists'] ) ): ?>
        <h3><?php
	        esc_html_e( 'Your CleverReach lists', 'cleverreach' ); ?></h3>
        <table class="haet_cleverreach_lists">
            <tbody>
            <tr>
                <th class="name">
					<?php
					esc_html_e( 'List Name', 'cleverreach' ); ?>
                </th>
                <th class="count">
					<?php
					esc_html_e( '# Subscribers', 'cleverreach' ); ?>
                </th>
            </tr>
			<?php
			foreach ( $settings['group_lists'] as $list ): ?>
                <tr>
                    <td class="name">
						<?php
						echo esc_html( $list['name'] ); ?>
                    </td>
                    <td class="count">
						<?php
						echo esc_html( $list['count'] ); ?>
                    </td>
                </tr>
			<?php
			endforeach; ?>
            </tbody>
        </table>
	<?php
	endif; ?>
	<?php
	if ( $data['access_token'] ): ?>
        <form method="post">
            <input type="hidden" name="haet_cleverreach_refresh" value="1"/>
            <p>
                <input type="submit" value="<?php
                esc_html_e( 'Reload Lists from CleverReach', 'cleverreach' ); ?>" class="button"/>
            </p>
        </form>
	<?php
	endif; ?>
</div>

<div class="haet-cleverreach-sidebar">
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-features.php'; ?>
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-haet.php'; ?>
</div>