<?php

namespace CleverReach\Newsletter\Proxy;

use CleverReach\Newsletter\Exceptions\BadAuthInfoException;
use CleverReach\Newsletter\Exceptions\HttpAuthenticationException;
use CleverReach\Newsletter\Exceptions\HttpCommunicationException;
use CleverReach\Newsletter\Exceptions\HttpRequestException;
use CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException;
use CleverReach\Newsletter\Model\Auth_Info;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Services\Infrastructure\Http_Client_Service;
use CleverReach\Newsletter\Utility\HttpResponse;
use CleverReach\Newsletter\Utility\Logger;

abstract class CleverReach_BaseProxy {
	const API_VERSION = 'v3';
	const BASE_URL = 'https://rest.cleverreach.com/';
	const CLIENT_ID = 'uadDU0wHla';
	const CLIENT_SECRET = 'QrUrHfpkKkfgFCcFKREHJ9RXMMYjtjtj';
	const HTTP_STATUS_CODE_UNAUTHORIZED = 401;
	const HTTP_STATUS_CODE_FORBIDDEN = 403;

	/**
	 * @var Http_Client_Service
	 */
	protected $http_client;

	/**
	 * @var Auth_Service
	 */
	private $auth_service;

	/**
	 * CleverReach_BaseProxy constructor.
	 */
	public function __construct() {
		$this->http_client  = new Http_Client_Service();
		$this->auth_service = new Auth_Service();
	}

	/**
	 * Execute get method request
	 *
	 * @param $endpoint
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException|\CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 */
	public function get( $endpoint ) {
		$response = $this->call( 'GET', $endpoint );

		return json_decode( $response->get_body(), true );
	}

	/**
	 * Execute post method request
	 *
	 * @param $endpoint
	 * @param array $body
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException|\CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 */
	public function post( $endpoint, $body = array() ) {
		$response = $this->call( 'POST', $endpoint, $body );

		return json_decode( $response->get_body(), true );
	}

	/**
	 * Execute put method request
	 *
	 * @param $endpoint
	 * @param array $body
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException|\CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 */
	public function put( $endpoint, $body = array() ) {
		$response = $this->call( 'PUT', $endpoint, $body );

		return json_decode( $response->get_body(), true );
	}

	/**
	 * Execute delete method request
	 *
	 * @param $endpoint
	 * @param array $body
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException|\CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 */
	public function delete( $endpoint, $body = array() ) {
		$response = $this->call( 'DELETE', $endpoint, $body );

		return json_decode( $response->get_body(), true );
	}

	/**
	 * Get headers
	 *
	 * @return string[]
	 * @throws \CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException
	 */
	protected function get_headers() {
		$headers          = $this->get_base_headers();
		$headers['token'] = 'Authorization: Bearer ' . $this->get_valid_access_token();

		return $headers;
	}

	/**
	 * Execute HTTP Request
	 *
	 * @param $method
	 * @param $endpoint
	 * @param array $body
	 *
	 * @return \CleverReach\Newsletter\Utility\HttpResponse
	 * @throws \CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException
	 */
	protected function call( $method, $endpoint, $body = array() ) {
		$headers = $this->get_headers();
		$url     = $this->get_base_url() . $endpoint;
		$payload = $this->format_payload( $method, $body );

		$response = $this->http_client->request( $method, $url, $headers, $payload );
		$this->validate_response( $response );

		return $response;
	}

	/**
	 * Get base url
	 *
	 * @return string
	 */
	protected function get_base_url() {
		return self::BASE_URL . self::API_VERSION . '/';
	}

	/**
	 * Format payload
	 *
	 * @param $method
	 * @param array $body
	 *
	 * @return false|string
	 */
	private function format_payload( $method, array $body ) {
		return in_array( strtoupper( $method ), array( 'POST', 'PUT' ) ) ? json_encode( $body ) : '';
	}

	/**
	 * Check if status code outside the range [200, 300)
	 *
	 * @param int $httpCode HTTP status code
	 *
	 * @return bool
	 */
	protected function is_error_code( $httpCode ) {
		return ( $httpCode !== null ) && ( $httpCode < 200 || $httpCode >= 300 );
	}

	/**
	 * Extract error message and status code from response
	 *
	 * @param HttpResponse $response Object.
	 *
	 * @return array
	 *   [message, statusCode]
	 */
	protected function extract_error_message( $response ) {
		$httpCode = $response->get_status();
		$body     = $response->get_body();
		$message  = var_export( $body, true );

		$error = json_decode( $body, true );
		if ( is_array( $error ) ) {
			if ( isset( $error['error']['message'] ) ) {
				$message = $error['error']['message'];
			}

			if ( isset( $error['error']['code'] ) ) {
				$httpCode = $error['error']['code'];
			}
		}

		return array( $message, $httpCode );
	}

	/**
	 * Check if status code is 401 or 403
	 *
	 * @param int $httpCode
	 *
	 * @return bool
	 */
	protected function is_unauthorized_or_forbidden( $httpCode ) {
		return ( $httpCode === self::HTTP_STATUS_CODE_UNAUTHORIZED ) || ( $httpCode === self::HTTP_STATUS_CODE_FORBIDDEN );
	}

	/**
	 * Logs provided message as error and throws exception.
	 *
	 * @param string $message Message to be logged and put to exception.
	 * @param int $code Status code.
	 * @param null $previousException
	 *
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 */
	protected function log_and_throw_http_request_exception( $message, $code = 0, $previousException = null ) {
		Logger::error( $message );

		throw new HttpRequestException( $message, $code, $previousException );
	}

	/**
	 * Refreshes access token.
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException
	 */
	protected function refresh_access_token() {
		$refreshToken = $this->auth_service->get_refresh_token();
		if ( empty( $refreshToken ) ) {
			throw new BadAuthInfoException( 'Refresh token not found! User must re-authenticate.' );
		}

		$payload  = '&grant_type=refresh_token&refresh_token=' . $refreshToken;
		$identity = base64_encode( self::CLIENT_ID . ':' . self::CLIENT_SECRET );
		$header   = array( 'Authorization: Basic ' . $identity );

		$response = $this->http_client->request( 'POST', $this->get_token_url(), $header, $payload );
		if ( ! $response->is_successful() ) {
			throw new RefreshTokenExpiredException( 'Refresh token expired! User must re-authenticate.' );
		}

		$result = json_decode( $response->get_body(), true );
		if ( empty( $result['access_token'] ) || empty( $result['expires_in'] ) ) {
			throw new HttpCommunicationException( 'CleverReach API invalid response.' );
		}

		return $result;
	}

	/**
	 * Gets CleverReach REST API token url.
	 *
	 * @return string
	 */
	protected function get_token_url() {
		return static::BASE_URL . 'oauth/token.php';
	}

	/**
	 * Retrieves valid access token.
	 *
	 * @return mixed|string
	 * @throws \CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException
	 */
	private function get_valid_access_token() {
		// Try to get access token from config and validate expiration
		$token = $this->auth_service->get_access_token();

		if ( $this->auth_service->is_access_token_expired() ) {
			try {
				$result = $this->refresh_access_token();
				$token  = $result['access_token'];
			} catch ( RefreshTokenExpiredException $e ) {
				$this->auth_service->set_refresh_token( null );
				throw $e;
			}

			if ( isset( $result['access_token'], $result['expires_in'], $result['refresh_token'] ) ) {
				$result['expires_in'] = time() + $result['expires_in'];
				$this->auth_service->set_auth_info( Auth_Info::from_array( $result ) );
			}
		}

		if ( empty( $token ) ) {
			throw new BadAuthInfoException( 'Access token missing' );
		}

		return $token;
	}

	/**
	 * Validate response
	 *
	 * @param HttpResponse $response
	 *
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 */
	private function validate_response( $response ) {
		$httpCode = $response->get_status();
		if ( $this->is_error_code( $httpCode ) ) {
			list( $message, $httpCode ) = $this->extract_error_message( $response );

			if ( $this->is_unauthorized_or_forbidden( $httpCode ) ) {
				Logger::info( $message );
				throw new HttpAuthenticationException( $message, $httpCode );
			}

			$this->log_and_throw_http_request_exception( $message, $httpCode );
		}
	}

	/**
	 * Return base HTTP header
	 *
	 * @return string[]
	 */
	private function get_base_headers() {
		return array(
			'accept'  => 'Accept: application/json',
			'content' => 'Content-Type: application/json',
		);
	}
}