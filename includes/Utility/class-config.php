<?php

namespace CleverReach\Newsletter\Utility;

use CleverReach\Newsletter\Form\CleverReach_Form;
use CleverReach\Newsletter\Repository\Config_Repository;
use Exception;

class CleverReach_Configuration {

	const CLEVERREACH_CLIENT_ID = 'uadDU0wHla';
	const CLEVERREACH_AUTH_URL = 'https://rest.cleverreach.com/oauth/authorize.php';
	/**
	 * @var Config_Repository
	 */
	private $config_repository;

	/**
	 * CleverReach_Configuration constructor.
	 */
	public function __construct() {
		$this->config_repository = new Config_Repository();
	}

	/**
	 * Gets settings with fallback to default ones
	 *
	 * @return false|mixed|void
	 */
	public function get_settings() {
		try {
			$settings = $this->config_repository->get_settings();
            if ( $settings === false ) {
                $settings = array();
            }

			$default_settings = $this->get_default_settings();

			foreach ( $default_settings as $key => $value ) {
				if ( ! isset( $settings[ $key ] ) ) {
					$settings[ $key ] = $value;
				}
			}

			return $settings;
		} catch ( Exception $exception ) {
			Logger::error( 'Failed to retrieve settings from database. ' . $exception->getMessage() );
		}
	}

	/**
	 * Saves settings with fallback to default ones
	 *
	 * @param $settings
	 */
	public function save_settings( $settings ) {
		try {
			$this->config_repository->save_settings( $this->update_setting_fields( $settings ) );
		} catch ( Exception $exception ) {
			Logger::error( 'Failed to save settings. ' . $exception->getMessage() );
		}
	}

	/**
	 * Deletes settings
	 */
	public function delete_settings() {
		try {
			$this->config_repository->delete_settings();
		} catch ( Exception $exception ) {
			Logger::error( 'Failed to delete settings from database. ' . $exception->getMessage() );
		}
	}

	/**
	 * Gets array with default settings
	 *
	 * @return array
	 */
	public function get_default_settings() {
		return array(
			'show_comments_section_checkbox'         => 1,
			'caption_for_comments_section_checkbox'  => __( 'Sign up for our newsletter', 'cleverreach' ),
			'show_comments_section_checkbox_default' => 0,
			'label_position_in_from'                 => 'left',
			'form_message_error'                     => __( 'Oops. Something went wrong. Please try again later.',
				'cleverreach' ),
			'form_message_success'                   => __( 'Thank you for your subscription.', 'cleverreach' ),
			'form_message_entry_exists'              => __( 'You\'re already subscribed.', 'cleverreach' ),
			'form_message_invalid_email'             => __( 'Please provide a valid email address.', 'cleverreach' ),
			'form_message_required_field'            => __( 'This is a mandatory field.', 'cleverreach' ),
			'selected_form_id'                       => '',
			'selected_group_list_id'                 => ''
		);
	}

	/**
	 * Prints form
	 *
	 * @param false $is_widget
	 */
	public function print_cleverreach_form( $is_widget = false ) {
		$cleverreach_form = new CleverReach_Form();
		$cleverreach_form->show_form( $is_widget );
	}

	/**
	 * Returns CleverReach application client id
	 *
	 * @return string
	 */
	public function get_client_id() {
		return self::CLEVERREACH_CLIENT_ID;
	}

	/**
	 * Returns CleverReach auth URL
	 *
	 * @return string
	 */
	public function get_auth_url() {
		return self::CLEVERREACH_AUTH_URL;
	}

	/**
	 * Update settings array
	 *
	 * @param $fields
	 *
	 * @return array
	 */
	public function update_setting_fields( $fields ) {
		$settings = $this->config_repository->get_settings();

		foreach ( $fields as $field_name => $field_value ) {
			$settings[ $field_name ] = $field_value;
		}

		return $settings;
	}

}