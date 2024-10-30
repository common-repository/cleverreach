<?php

use CleverReach\Newsletter\Utility\View;

/**
 * @var $data
 */

?>

<div id="haet-cleverreach-settings-form" class="wrap haet-cleverreach-settings">
    <h2><img src="https://cloud-files.crsend.com/images/cleverreach-logo.png"/> <?php
	    esc_html_e( 'Form Builder', 'cleverreach' ); ?></h2>
	<?php
	if ( $data['access_token'] === null ) { ?>
        <p class="error dashicons-before dashicons-no-alt">
			<?php
			echo wp_kses( $data['message'], array( 'a' => array( 'href' => array() ), 'strong' => array() ) ); ?>
        </p>
	<?php
	} elseif ( ! $data['is_successful'] ) { ?>
        <p class="message dashicons-before dashicons-lightbulb">
			<?php
			echo wp_kses( $data['message'], array( 'a' => array( 'href' => array() ), 'strong' => array() ) ); ?>
        </p>
	<?php
	} ?>
	<?php
	if ( isset( $data['attributes'] ) || isset( $data['settings']['form_attributes_available'] ) || isset( $data['settings']['form_attributes_used'] ) ): ?>
        <form id="cleverreach_newsletter_builder_form" action="options.php" method="post">

            <h3><?php
	            esc_html_e( 'Drag & Drop fields to create your form', 'cleverreach' ); ?></h3>
            <div class="clearfix">
                <div class="sortable-wrapper cleverreach-formfields-used">
                    <h4><?php
	                    esc_html_e( 'Your Form', 'cleverreach' ); ?></h4>
                    <ul id="haet_cleverreach_formfields_used" class="connected-sortable">

                    </ul>
                </div>
                <div class="sortable-wrapper cleverreach-formfields-available">
                    <h4><?php
	                    esc_html_e( 'Available Fields', 'cleverreach' ); ?></h4>
                    <p class="description"><?php
	                    esc_html_e( 'Just drag the fields to the form above.', 'cleverreach' ); ?></p>
                    <ul id="haet_cleverreach_formfields_available" class="connected-sortable">
						<?php
						if ( isset( $data['attributes'] ) && is_array( $data['attributes'] ) ): // Data refreshed
							?>
                            <li id="cleverreach-attribute-description" data-key="cleverreach_description"
                                data-type="description" class="attribute clearfix type-description">

                                <span class="attribute-name">
                                    <span class="dashicons dashicons-editor-alignleft"></span>
                                    <?php
                                    esc_html_e( 'Description Text', 'cleverreach' ); ?>
                                </span>
                                <div class="field-description">
                                    <label><?php
	                                    esc_html_e( 'Text', 'cleverreach' ); ?></label>
                                    <textarea><?php
	                                    esc_html_e( 'Signup for our Newsletter!', 'cleverreach' ); ?></textarea>
                                </div>
                            </li>
                            <li id="cleverreach-attribute-email" data-key="cleverreach_email" data-type="email"
                                class="attribute clearfix type-email">
                                <span class="attribute-name">
                                    <span class="dashicons dashicons-email"></span>
                                    <?php
                                    esc_html_e( 'Email Address', 'cleverreach' ); ?>
                                </span>
                                <div class="field-label">
                                    <label><?php
	                                    esc_html_e( 'Label', 'cleverreach' ); ?></label>
                                    <input type="text" value="<?php
                                    esc_html_e( 'Email', 'cleverreach' ); ?>">
                                </div>
                                <div class="field-required">
                                    <input type="checkbox" name="cleverreach_email-required"
                                           id="cleverreach_email-required" value="1" checked disabled>
                                    <label for="cleverreach_email-required"><?php
	                                    esc_html_e( 'required', 'cleverreach' ); ?></label>
                                </div>
                            </li>

							<?php
							foreach ( $data['attributes'] as $attribute ):
								$icon = '<span class="dashicons dashicons-marker"></span>';
								if ( $attribute['type'] === 'gender' ) {
									$icon = '<span class="dashicons dashicons-universal-access"></span>';
								}
								if ( $attribute['type'] === 'text' ) {
									$icon = '<span class="dashicons dashicons-feedback"></span>';
								}

								?>
                                <li id="cleverreach-attribute-<?php
								echo esc_attr( $attribute['name'] ); ?>" data-key="<?php
								echo esc_attr( $attribute['name'] ); ?>" data-type="<?php
								echo esc_attr( $attribute['type'] ); ?>" class="attribute clearfix type-<?php
								echo esc_attr( $attribute['type'] ); ?>">
                                    <span class="attribute-name">
                                        <?php
                                        echo wp_kses( $icon, array( 'span' => array( 'class' => array() ) ) ) . ' ' . ( strpos( $attribute['name'],
		                                        'GLOBAL_' ) !== false ? '<span class="dashicons dashicons-admin-site" title="' . esc_html( __( 'Global Attribute',
			                                        'cleverreach' ) ) . '"></span>' : '' ) . ' ' . esc_attr( $attribute['description'] ); ?>
                                    </span>
                                    <div class="field-label">
                                        <label><?php
	                                        esc_html_e( 'Label', 'cleverreach' ); ?></label>
                                        <input type="text" value="<?php
										echo esc_attr( $attribute['description'] ); ?>">
                                    </div>
									<?php
									if ( $attribute['type'] === 'gender' ): ?>
                                        <div class="field-options">
                                            <label><?php
	                                            esc_html_e( 'Available Options', 'cleverreach' ); ?></label>
                                            <textarea><?php
												echo esc_html( __( 'Mrs.', 'cleverreach' ) ) . "\n" .
												     esc_html( __( 'Mr.', 'cleverreach' ) ) . "\n" .
                                                     esc_html( __( 'Family', 'cleverreach' ) ) . "\n" .
                                                     esc_html( __( 'Company', 'cleverreach' ) );
												?></textarea>
                                        </div>
									<?php
									endif; ?>
                                    <div class="field-required">
                                        <input type="checkbox" name="<?php
										echo esc_attr( $attribute['name'] ); ?>-required" id="<?php
										echo esc_attr( $attribute['name'] ); ?>-required" value="1">
                                        <label for="<?php
										echo esc_attr( $attribute['name'] ); ?>-required"><?php
	                                        esc_html_e( 'required', 'cleverreach' ); ?></label>
                                    </div>
                                </li>
							<?php
							endforeach; ?>
                            <li id="cleverreach-attribute-policy_confirm" data-key="cleverreach_policy_confirm"
                                data-type="policy_confirm" class="attribute clearfix type-policy_confirm">
                                <span class="attribute-name">
                                    <span class="dashicons dashicons-yes"></span>
                                    <?php
                                    esc_html_e( 'Privacy policy checkbox', 'cleverreach' ); ?>
                                </span>
                                <div class="field-label">
                                    <label><?php
	                                    esc_html_e( 'Label', 'cleverreach' ); ?></label>
                                    <input type="text" value="<?php
                                    esc_html_e( 'Yes, I agree to your privacy policy...', 'cleverreach' ); ?>">
                                </div>
                                <div class="field-required">
                                    <input type="checkbox" name="cleverreach_policy_confirm-required"
                                           id="cleverreach_policy_confirm-required" value="1" checked disabled>
                                    <label for="cleverreach_policy_confirm-required"><?php
	                                    esc_html_e( 'required', 'cleverreach' ); ?></label>
                                </div>
                            </li>
                            <li id="cleverreach-attribute-submit_button" data-key="cleverreach_submit_button"
                                data-type="submit" class="attribute clearfix type-submit">
                                <span class="attribute-name">
                                    <span class="dashicons dashicons-share-alt2"></span>
                                    <?php
                                    esc_html_e( 'Submit Button', 'cleverreach' ); ?>
                                </span>
                                <div class="field-label">
                                    <label><?php
	                                    esc_html_e( 'Label', 'cleverreach' ); ?></label>
                                    <input type="text" value="<?php
                                    esc_html_e( 'Subscribe', 'cleverreach' ); ?>">
                                </div>
                            </li>
						<?php
						endif; ?>
                    </ul>
                </div>
            </div>
			<?php
			settings_fields( 'cleverreach_newsletter_option_group' ); ?>
			<?php
			do_settings_sections( 'cleverreach_page_cleverreach-forms' ); ?>
			<?php
			submit_button(); ?>
        </form>
        <br>
        <hr><br>
        <p>
			<?php
			esc_html_e( 'Delete this form and reload list attributes from CleverReach?', 'cleverreach' ); ?>
        </p>
	<?php
	else: ?>

	<?php
	endif; ?>
    <p>
		<?php
		esc_html_e( 'Please select a form below to load the list attributes from CleverReach. To use these functions, a form and a recipient list need to be set up in CleverReach.', 'cleverreach' ); ?>
    </p>
    <form method="post">
        <table class="form-table">
            <tbody>
            <tr>
                <th scope="row"><?php
	                esc_html_e( 'CleverReach form', 'cleverreach' ); ?></th>
                <td>
					<?php
					if ( isset( $data['settings']['group_lists'] ) && is_array( $data['settings']['group_lists'] ) ): ?>
                        <select name="haet_cleverreach_get_fields">
							<?php
							$lists          = $data['settings']['group_lists'];
							$selected_value = $data['selected_value'];
							echo View::file( '/admin/form/options-form.php' )->render( array(
									'group_lists'    => $data['settings']['group_lists'],
									'selected_value' => $selected_value
								)
							);
							?>
                        </select>
					<?php
					endif; ?>
                </td>
            </tr>
            <tr>
                <th scope="row"></th>
                <td>
                    <input type="submit" value="<?php
                    esc_html_e( 'Load Form Attributes', 'cleverreach' ); ?>" class="button"/>
                </td>
            </tr>
            </tbody>
        </table>
    </form>

</div>
<div class="haet-cleverreach-sidebar">
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-features.php'; ?>
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-form-usage.php'; ?>
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-woocommerce.php'; ?>
	<?php
	require $data['plugin_dir_path'] . '/resources/views/admin/sidebar-haet.php'; ?>
</div>