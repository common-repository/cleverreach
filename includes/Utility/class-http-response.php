<?php

namespace CleverReach\Newsletter\Utility;

/**
 * Class HttpResponse
 *
 * @package CleverReach\Newsletter\Utility
 */
class HttpResponse {
	/**
	 * HTTP status.
	 *
	 * @var int
	 */
	private $status;
	/**
	 * Response body.
	 *
	 * @var string
	 */
	private $body;
	/**
	 * HTTP headers.
	 *
	 * @var array
	 */
	private $headers;

	/**
	 * HttpResponse constructor.
	 *
	 * @param int $status HTTP status
	 * @param array $headers HTTPS headers
	 * @param string $body Response body
	 */
	public function __construct( $status, $headers, $body ) {
		$this->status  = $status;
		$this->headers = $headers;
		$this->body    = $body;
	}

	/**
	 * Returns response status.
	 *
	 * @return int HTTPS status.
	 */
	public function get_status() {
		return $this->status;
	}

	/**
	 * Returns response body.
	 *
	 * @return string Response body.
	 */
	public function get_body() {
		return $this->body;
	}

	/**
	 * Returns json decoded response body.
	 *
	 * @return array Response body decoded as json decode.
	 */
	public function decode_body_to_array() {
		$result = json_decode( $this->body, true );

		return ! empty( $result ) ? $result : array();
	}

	/**
	 * Return. response headers.
	 *
	 * @return array Array of HTTP headers.
	 */
	public function get_headers() {
		return $this->headers;
	}

	/**
	 * Verifies HTTP status code.
	 *
	 * @return bool Returns TRUE if in success range [200, 300); otherwise, FALSE.
	 */
	public function is_successful() {
		return $this->status !== null && $this->get_status() >= 200 && $this->get_status() < 300;
	}
}