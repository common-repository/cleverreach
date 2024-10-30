<?php

namespace CleverReach\Newsletter\Admin;

use CleverReach\Newsletter\Proxy\CleverReach_ResourceProxy;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;

class CleverReach_InfoBanner {

	/**
	 * @var CleverReach_Configuration
	 */
	private $configuration;

	/**
	 * @var CleverReach_ResourceProxy
	 */
	private $resource_proxy;

	/**
	 * CleverReach_InfoBanner constructor.
	 */
	public function __construct() {
		$this->configuration  = new CleverReach_Configuration();
		$this->resource_proxy = new CleverReach_ResourceProxy();
	}

	/**
	 * Returns message for reconnection
	 *
	 * @return string
	 */
	public function get_please_reconnect_message() {
		$link        = admin_url( 'admin.php?page=cleverreach' );
		$description = __( 'Reconnect to new CleverReach API.', 'cleverreach' );
		$message     = sprintf( __( 'CleverReach has changed its API, please reconnect your account <a href="%s">here</a> using the new REST API and review all of your settings.',
			'cleverreach' ), $link );

		return "<strong>{$description}</strong> " . $message;
	}

	/**
	 * Get info banner message for integration page
	 *
	 * @param $settings
	 *
	 * @return array
	 * @throws \CleverReach\Newsletter\Exceptions\BadAuthInfoException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpAuthenticationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpCommunicationException
	 * @throws \CleverReach\Newsletter\Exceptions\HttpRequestException
	 * @throws \CleverReach\Newsletter\Exceptions\RefreshTokenExpiredException
	 */
	public function get_integration_page_message( $settings ) {
		$is_successful = true;
		$message       = '';

		if ( ! $settings['group_lists'] ) {
			$is_successful = false;
			$message       = $this->get_no_lists_message();
		} elseif ( ! $this->check_if_any_form_exists( $settings['group_lists'] ) ) {
			$is_successful = false;
			$message       = $this->get_no_forms_message();
		} elseif ( ! $this->newsletter_form_exists( $settings['group_lists'] ) ) {
			$is_successful = false;
			$message       = $this->get_integration_nonexistent_selected_form_message();
		} elseif ( ! array_key_exists( 'selected_name_attribute',
				$settings ) && ! $this->resource_proxy->get_global_attributes() ) {
			$is_successful = false;
			$message       = $this->get_no_attributes_message();
		}

		return [ $is_successful, $message ];
	}

	/**
	 * Get info banner message for form builder page
	 *
	 * @param $settings
	 *
	 * @return array
	 */
	public function get_form_builder_page_message( $settings ) {
		$is_successful = true;
		$message       = '';

		if ( ! $settings['group_lists'] ) {
			$is_successful = false;
			$message       = $this->get_no_lists_message();
		} elseif ( ! $this->check_if_any_form_exists( $settings['group_lists'] ) ) {
			$is_successful = false;
			$message       = $this->get_no_forms_message();
		} elseif ( ! $this->form_builder_form_exists( $settings['group_lists'] ) ) {
			$is_successful = false;
			$message       = $this->get_form_builder_nonexistent_selected_form_message();
		}

		return [ $is_successful, $message ];
	}

	/**
	 * Return message for info banner when form is not found in integrations tab
	 *
	 * @return string
	 */
	public function get_integration_nonexistent_selected_form_message() {
		$description = __( 'The selected form for the newsletter form doesn’t exist anymore.', 'cleverreach' );
		$message     = __( 'The form which you have selected for the newsletter checkbox under the Integration tab, doesn’t exist anymore. Please set a new one.',
			'cleverreach' );

		return "<strong>{$description}</strong> " . $message;
	}

	/**
	 * Return message for info banner when form is not found in form builder tab
	 *
	 * @return string
	 */
	public function get_form_builder_nonexistent_selected_form_message() {
		$description = __( 'The selected form for the form builder doesn’t exist anymore.', 'cleverreach' );
		$message     = __( 'The form which you have selected for the form builder under the Form Builder tab, doesn’t exist anymore. Please set a new one.',
			'cleverreach' );

		return "<strong>{$description}</strong> " . $message;
	}

	/**
	 * Checks if saved form in form builder tab still exists on the customer's account
	 *
	 * @param $lists
	 *
	 * @return bool
	 */
	public function form_builder_form_exists( $lists ) {
		$settings = $this->configuration->get_settings();
		if ( $settings['selected_group_list_id'] === '' ||
		     $settings['selected_form_id'] === '' ) {
			return true;
		}
		foreach ( $lists as $list ) {
			if ( $list['id'] === $settings['selected_group_list_id'] ) {
				foreach ( $list['forms'] as $form ) {
					if ( $form['id'] === $settings['selected_form_id'] ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Checks if saved form in integrations tab still exists on the customer's account
	 *
	 * @param $lists
	 *
	 * @return bool
	 */
	public function newsletter_form_exists( $lists ) {
		$settings = $this->configuration->get_settings();

		if ( ! array_key_exists( 'selected_group_and_form', $settings ) ) {
			return true;
		}

		list( $list_id, $form_id ) = explode( '-', $settings['selected_group_and_form'] );
		foreach ( $lists as $list ) {
			if ( $list['id'] === $list_id ) {
				foreach ( $list['forms'] as $form ) {
					if ( $form['id'] === $form_id ) {
						return true;
					}
				}
			}
		}

		return false;
	}

	/**
	 * Return message for info banner when there is no lists on user account
	 *
	 * @return string
	 */
	private function get_no_lists_message() {
		$description = __( 'There are no recipient lists in your CleverReach account.', 'cleverreach' );
		$message     = __( 'Please create list in your CleverReach account and reload lists in plugin general page.',
			'cleverreach' );

		return "<strong>{$description}</strong> " . $message;
	}

	/**
	 * Return message for info banner when there is no lists on user account
	 *
	 * @return string
	 */
	private function get_no_forms_message() {
		$description = __( 'There are no forms in your CleverReach account.', 'cleverreach' );
		$message     = __( 'Please create form in your CleverReach account and reload lists in plugin general page.',
			'cleverreach' );

		return "<strong>{$description}</strong> " . $message;
	}

	/**
	 * Return message for info banner when there are no attributes
	 *
	 * @return string
	 */
	private function get_no_attributes_message() {
		$description = __( 'There are no attributes in your CleverReach account.', 'cleverreach' );
		$message     = __( 'Please create attribute field in your CleverReach list and reload lists in plugin general page.',
			'cleverreach' );

		return "<strong>{$description}</strong> " . $message;
	}

	/**
	 * Checks if any form exists in users account
	 */
	private function check_if_any_form_exists( $lists ) {
		foreach ( $lists as $list ) {
			if ( ! empty( $list['forms'] ) ) {
				return true;
			}
		}

		return false;
	}

}