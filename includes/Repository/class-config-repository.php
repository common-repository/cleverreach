<?php

namespace CleverReach\Newsletter\Repository;

class Config_Repository {
	const SETTINGS_OPTION_NAME = 'cleverreach_newsletter_settings';

	/**
	 * Retrieves settings
	 *
	 * @return false|mixed|void
	 */
	public function get_settings() {
		return get_option( self::SETTINGS_OPTION_NAME );
	}

	/**
	 * Saves settings
	 *
	 * @param array $settings
	 */
	public function save_settings( $settings ) {
		update_option( self::SETTINGS_OPTION_NAME, $settings );
	}

	/**
	 * Deletes settings
	 */
	public function delete_settings() {
		delete_option( self::SETTINGS_OPTION_NAME );
	}
}