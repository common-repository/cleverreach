<?php

namespace CleverReach\Newsletter\Proxy;

use CleverReach\Newsletter\Exceptions\BadAuthInfoException;
use CleverReach\Newsletter\Exceptions\HttpCommunicationException;
use CleverReach\Newsletter\Model\Auth_Info;
use CleverReach\Newsletter\Utility\Logger;
use Exception;

class CleverReach_ResourceProxy extends CleverReach_BaseProxy {

	/**
	 * Retrieve all lists from cleverreach API
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException|\CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 */
	public function get_lists() {
		$lists = $this->get( 'groups' );

		foreach ( $lists as &$list ) {
			$list['forms'] = $this->get( "groups/{$list['id']}/forms" );
			$list_stats    = $this->get( "groups/{$list['id']}/stats" );
			$list['count'] = $list_stats['active_count'];
		}

		return $lists;
	}

	/**
	 * Retrieve list attributes from cleverreach API
	 *
	 * @param $list_id
	 *
	 * @return array
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException|\CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 */
	public function get_list_attributes( $list_id ) {
		$global_attributes = $this->get( 'attributes' );
		$list_attributes   = $this->get( 'attributes?' . http_build_query( array( 'group_id' => $list_id ) ) );

		foreach ( $global_attributes as &$attribute ) {
			$attribute['name'] = 'GLOBAL_' . $attribute['name'];
		}

		unset( $attribute );

		foreach ( $list_attributes as &$attribute ) {
			$attribute['name'] = 'LIST_' . $attribute['name'];
		}

		return array(
			'global_attributes' => $global_attributes,
			'list_attributes'   => $list_attributes
		);
	}

	/**
	 * Retrieves global attributes
	 *
	 * @return mixed|null
	 * @throws \CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException
	 */
	public function get_global_attributes() {
		return $this->get( 'attributes' );
	}

	/**
	 * Subscribe user
	 *
	 * @return array
	 */
	public function subscribe_user(
		$settings,
		$submission,
		$form_id,
		$list_id,
		$source = null,
		$send_activation_mail = true
	) {
		$return            = array();
		$return['success'] = false;
		$email             = $submission['cleverreach_email']->value;

		if ( ! $list_id || ! $email || ! $form_id ) {
			$return['success'] = false;

			return $return;
		}

		try {
			if ( $source === null ) {
				$source = get_bloginfo( 'name' );
			}

			$user_data  = $this->set_user_data( $submission );
			$attributes = $this->remove_attributes_prefix( $user_data );

			$user = array(
				"email"             => $email,
				"registered"        => time(),
				"activated"         => ( $send_activation_mail ? false : time() ),
				"source"            => $source,
				"attributes"        => $attributes['attributes'],
				"global_attributes" => $attributes['global_attributes']
			);

			$result = $this->post( "groups/" . $list_id . '/receivers', array( 'postdata' => $user ) );

			if ( $result && array_key_exists( 'id', $result ) ) {
				if ( $send_activation_mail ) {
					$this->post( "forms/" . $form_id . '/send/activate', array(
							'email'     => $email,
							'groups_id' => $list_id,
							'doidata'   => $this->get_activation_data( $user_data, $source )
						)
					);
				}

				$return['success'] = true;
			} else {
				$return['message'] = $result['message'] === 'duplicate data'
					? $settings['form_message_entry_exists'] : $result['message'];
			}
		} catch ( Exception $e ) {
			$exception_message = $e->getMessage();

			$return['message'] = strpos( $exception_message, 'duplicate address' ) !== false
				? $settings['form_message_entry_exists']
				: __( 'Could not connect to the CleverReach API.', 'cleverreach' ) . ' (' . $exception_message . ')';

			Logger::error( 'Could not subscribe user. ' . $exception_message, array(
				'trace' => $e->getTraceAsString()
			) );
		}

		return $return;
	}

	/**
	 * Retrieve user info
	 *
	 * @return mixed|null
	 */
	public function get_user_info() {
		try {
			return $this->get( "debug/whoami.json" );
		} catch ( Exception $exception ) {
			Logger::error( 'Could not retrieve user info from CleverReach API. ' . $exception->getMessage(), array(
				'trace' => $exception->getTraceAsString()
			) );
		}

		return null;
	}

	/**
	 * Returns authentication information (AuthInfo).
	 *
	 * @param $code
	 * @param $redirectUrl
	 *
	 * @return Auth_Info
	 * @throws HttpCommunicationException
	 * @throws BadAuthInfoException
	 */
	public function get_auth_info( $code, $redirectUrl ) {
		$header = array(
			'accept'  => 'Accept: application/json',
			'content' => 'Content-Type: application/json',
		);

		// Assemble POST parameters for the request.
		$postFields = array(
			'grant_type'    => 'authorization_code',
			'client_id'     => self::CLIENT_ID,
			'client_secret' => self::CLIENT_SECRET,
			'code'          => $code,
			'redirect_uri'  => urlencode( $redirectUrl ),
		);

		$response = $this->http_client->request( 'POST', $this->get_token_url(), $header, json_encode( $postFields ) );
		$result   = json_decode( $response->get_body(), true );
		if ( isset( $result['error'] )
		     || empty( $result['access_token'] )
		     || empty( $result['expires_in'] )
		     || empty( $result['refresh_token'] )
		) {
			throw new BadAuthInfoException(
				isset( $result['error_description'] ) ? $result['error_description'] : ''
			);
		}
		$result['expires_in'] = time() + $result['expires_in'];

		return Auth_Info::from_array( $result );
	}

	/**
	 * Remove LIST_ and GLOBAL_ prefixed from attributes
	 *
	 * @param $user_data
	 *
	 * @return array[]
	 */
	private function remove_attributes_prefix( $user_data ) {
		$attributes        = array();
		$global_attributes = array();

		foreach ( $user_data as $data ) {
			if ( strpos( $data['key'], 'LIST_' ) !== false ) {
				$data['key']                = str_replace( 'LIST_', '', $data['key'] );
				$attributes[ $data['key'] ] = $data['value'];
			} elseif ( strpos( $data['key'], 'GLOBAL_' ) !== false ) {
				$data['key']                       = str_replace( 'GLOBAL_', '', $data['key'] );
				$global_attributes[ $data['key'] ] = $data['value'];
			}
		}

		return array(
			'attributes'        => $attributes,
			'global_attributes' => $global_attributes
		);
	}

	/**
	 * Sets user data
	 *
	 * @param $submission
	 *
	 * @return array
	 */
	private function set_user_data( $submission ) {
		$user_data = array();
		foreach ( $submission as $key => $data ) {
			if ( $key !== 'cleverreach_email' ) {
				$user_data[] = array(
					'key'   => $key,
					'value' => $data->value
				);
			}
		}

		return $user_data;
	}

	/**
	 * Gets data for activation
	 *
	 * @param $user_data
	 * @param $source
	 *
	 * @return array
	 */
	private function get_activation_data( $user_data, $source ) {
		$post_data = '';
		foreach ( $user_data as $user_data_entry ) {
			$post_data = $user_data_entry['key'] . ':' . $user_data_entry['value'] . ',';
		}

		return array(
			"user_ip"    => $_SERVER['REMOTE_ADDR'], //the IP of the user who registered. not yours!
			"user_agent" => "Mozilla/5.0 (X11; Ubuntu; Linux i686; rv:14.0) Gecko/20100101 Firefox/14.0.1",
			"referer"    => esc_url( home_url( '/' ) ),
			"postdata"   => $post_data,
			"info"       => $source,
		);
	}
}