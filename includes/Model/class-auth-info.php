<?php

namespace CleverReach\Newsletter\Model;

class Auth_Info {
	/**
	 * @var string|null
	 */
	private $access_token;

	/**
	 * @var string|null
	 */
	private $refresh_token;

	/**
	 * @var string|null
	 */
	private $access_token_duration;

	/**
	 * @return string|null
	 */
	public function get_access_token() {
		return $this->access_token;
	}

	/**
	 * @param string|null $access_token
	 */
	public function set_access_token( $access_token ) {
		$this->access_token = $access_token;
	}

	/**
	 * @return string|null
	 */
	public function get_refresh_token() {
		return $this->refresh_token;
	}

	/**
	 * @param string|null $refresh_token
	 */
	public function set_refresh_token( $refresh_token ) {
		$this->refresh_token = $refresh_token;
	}

	/**
	 * @return string|null
	 */
	public function get_access_token_duration() {
		return $this->access_token_duration;
	}

	/**
	 * @param string|null $access_token_duration
	 */
	public function set_access_token_duration( $access_token_duration ) {
		$this->access_token_duration = $access_token_duration;
	}

	/**
	 * @return array
	 */
	public function to_array() {
		return [
			'access_token'  => $this->access_token,
			'refresh_token' => $this->refresh_token,
			'expires_in'    => $this->access_token_duration
		];
	}

	/**
	 * @param $data
	 *
	 * @return Auth_Info
	 */
	public static function from_array( $data ) {
		$auth_info                        = new self();
		$auth_info->access_token          = is_array( $data ) && array_key_exists( 'access_token',
			$data ) ? $data['access_token'] : null;
		$auth_info->refresh_token         = is_array( $data ) && array_key_exists( 'refresh_token',
			$data ) ? $data['refresh_token'] : null;
		$auth_info->access_token_duration = is_array( $data ) && array_key_exists( 'expires_in',
			$data ) ? $data['expires_in'] : null;

		return $auth_info;
	}

}