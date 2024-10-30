<?php

use CleverReach\Newsletter\Utility\View;

/**
 * @var $data
 */

if ( defined( 'ICL_LANGUAGE_CODE' ) ) {  //WPML is active
	$languages = apply_filters( 'wpml_active_languages', null, 'orderby=id&order=desc' );
	if ( ! empty( $languages ) ) {
		foreach ( $languages as $language ) { ?>
            <img src="<?php
			echo esc_url( $language['country_flag_url'] ); ?>" height="12" alt="<?php
			echo esc_attr( $language['language_code'] ); ?>" width="18"/>
            <select name="cleverreach_newsletter_settings[selected_group_and_form_<?php
			echo esc_attr( $language['language_code'] ); ?>]">
				<?php
				echo View::file( '/admin/form/options-form.php' )->render( array(
					'group_lists'    => $data['options']['group_lists'],
					'selected_value' => $data['options'][ 'selected_group_and_form_' . $language['language_code'] ]
				) );
				?>
            </select><br>
			<?php
		}
	}
} else { ?>
    <select name="cleverreach_newsletter_settings[selected_group_and_form]">
		<?php
		echo View::file( '/admin/form/options-form.php' )->render( array(
			'group_lists'    => $data['options']['group_lists'],
			'selected_value' => array_key_exists( 'selected_group_and_form',
				$data['options'] ) ? $data['options']['selected_group_and_form'] : false
		) );
		?>
    </select>
	<?php
}