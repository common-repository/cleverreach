<?php

namespace CleverReach\Newsletter\Repository;

use CleverReach\Newsletter\Model\Auth_Info;

class Auth_Info_Repository {
	const AUTH_INFO_OPTION_NAME = 'cleverreach_newsletter_auth_info';

	/**
	 * Retrieve auth info from database
	 *
	 * @return Auth_Info
	 */
	public function get_auth_info() {
		return Auth_Info::from_array( get_option( self::AUTH_INFO_OPTION_NAME ) );
	}

	/**
	 * Save auth info
	 *
	 * @param Auth_Info $authInfo
	 */
	public function save_auth_info( Auth_Info $authInfo ) {
		update_option( self::AUTH_INFO_OPTION_NAME, $authInfo->to_array() );
	}

	/**
	 * Delete auth info from database
	 */
	public function delete_auth_info() {
		delete_option( self::AUTH_INFO_OPTION_NAME );
	}
}