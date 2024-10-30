<?php

namespace CleverReach\Newsletter\Services\Business;

use CleverReach\Newsletter\Model\Auth_Info;
use CleverReach\Newsletter\Repository\Auth_Info_Repository;

class Auth_Service {
	/**
	 * @var Auth_Info_Repository
	 */
	private $auth_info_repository;

	/**
	 * Auth_Service constructor.
	 */
	public function __construct() {
		$this->auth_info_repository = new Auth_Info_Repository();
	}

	/**
	 * Returns if access token has expired
	 *
	 * @return bool
	 */
	public function is_access_token_expired() {
		$auth_info = $this->auth_info_repository->get_auth_info();

		$duration = $auth_info->get_access_token_duration();

		if ( $duration ) {
			return time() >= $duration;
		}

		return false;
	}

	/**
	 * Sets auth info
	 *
	 * @param Auth_Info $authInfo
	 */
	public function set_auth_info( Auth_Info $authInfo ) {
		$this->auth_info_repository->save_auth_info( $authInfo );
	}

	/**
	 * Sets refresh token
	 *
	 * @param $value
	 */
	public function set_refresh_token( $value ) {
		$auth_info = $this->auth_info_repository->get_auth_info();
		$auth_info->set_refresh_token( $value );
		$this->auth_info_repository->save_auth_info( $auth_info );
	}

	/**
	 * Retrieves access token
	 *
	 * @return string|null
	 */
	public function get_access_token() {
		$auth_info = $this->auth_info_repository->get_auth_info();

		return $auth_info->get_access_token();
	}

	/**
	 * Retrieve refresh token
	 *
	 * @return string|null
	 */
	public function get_refresh_token() {
		$auth_info = $this->auth_info_repository->get_auth_info();

		return $auth_info->get_refresh_token();
	}

}