<?php

namespace CleverReach\Newsletter\Admin;

use CleverReach\Newsletter\CleverReach;
use CleverReach\Newsletter\Proxy\CleverReach_ResourceProxy;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\Logger;
use CleverReach\Newsletter\Utility\View;
use Exception;

class CleverReach_FormBuilderPage {

	/**
	 * @var CleverReach_Configuration
	 */
	private $configuration;

	/**
	 * @var CleverReach_ResourceProxy
	 */
	private $resource_proxy;

	/**
	 * @var Auth_Service
	 */
	private $auth_service;

	/**
	 * @var CleverReach_InfoBanner
	 */
	private $info_banner;

	/**
	 * CleverReach_FormBuilderPage constructor.
	 */
	public function __construct() {
		$this->configuration  = new CleverReach_Configuration();
		$this->resource_proxy = new CleverReach_ResourceProxy();
		$this->auth_service   = new Auth_Service();
		$this->info_banner    = new CleverReach_InfoBanner();
	}

	public function register_settings() {
		add_settings_section(
			'form_settings',
			__( 'Form Settings', 'cleverreach' ),
			array( $this, 'section_intro' ),
			'cleverreach_page_cleverreach-forms'
		);
		add_settings_field(
			'form_message_success',
			__( 'Success message', 'cleverreach' ),
			array( $this, 'field_message_success' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'form_message_invalid_email',
			__( 'Invalid Email', 'cleverreach' ),
			array( $this, 'field_message_invalid_email' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'form_message_entry_exists',
			__( 'Already subscribed', 'cleverreach' ),
			array( $this, 'field_message_entry_exists' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'form_message_error',
			__( 'An Error occured', 'cleverreach' ),
			array( $this, 'field_message_error' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'form_message_required_field',
			__( 'Required Field Error', 'cleverreach' ),
			array( $this, 'field_message_required_field' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'label_position_in_from',
			__( 'Label position', 'cleverreach' ),
			array( $this, 'field_label_position' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'form_attributes_used',
			'',
			array( $this, 'field_attributes_used' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
		add_settings_field(
			'form_attributes_available',
			'',
			array( $this, 'field_attributes_available' ),
			'cleverreach_page_cleverreach-forms',
			'form_settings'
		);
	}

	public function add_admin_pages() {
		add_submenu_page(
			'cleverreach',
			__( 'Form Builder', 'cleverreach' ),
			__( 'Form Builder', 'cleverreach' ),
			apply_filters( 'haet_cleverreach_capabilities', 'manage_options' ),
			'cleverreach-forms',
			array( $this, 'show_settings_form' )
		);
	}

	public function section_intro() {
	}

	/**
	 * Settings Page Form
	 */
	public function show_settings_form() {
		$settings = $this->configuration->get_settings();

		if ( isset( $_POST['haet_cleverreach_get_fields'] ) && $this->auth_service->get_access_token() ) {
			$list_form       = sanitize_text_field( $_POST['haet_cleverreach_get_fields'] );
			$list_form_array = explode( '-', $list_form );
			$form_id         = $list_form_array[1];

			$list_id                            = $list_form_array[0];
			$settings['selected_form_id']       = $form_id;
			$settings['selected_group_list_id'] = $list_id;
			$this->configuration->save_settings( array( 'selected_form_id' => $form_id, 'selected_group_list_id' => $list_id) );
			try {
				$attributes_result = $this->resource_proxy->get_list_attributes( $list_id );
				$attributes        = array_merge( $attributes_result['global_attributes'],
					$attributes_result['list_attributes'] );
			} catch ( Exception $exception ) {
				Logger::error( 'Failed to retrieve list attributes. ' . $exception->getMessage() );
			}
		}

		$is_successful = false;
		$message       = $this->info_banner->get_please_reconnect_message();
		if ( $this->auth_service->get_access_token() ) {
			list($is_successful, $message) = $this->info_banner->get_form_builder_page_message($settings);
		}

		echo View::file( '/admin/settings-form.php' )->render( array(
			'plugin_url'      => CleverReach::get_plugin_url(),
			'plugin_dir_path' => CleverReach::get_plugin_dir_path(),
			'settings'        => $settings,
			'attributes'      => $attributes ?? null,
			'access_token'    => $this->auth_service->get_access_token(),
			'message'         => $message,
			'is_successful'   => $is_successful,
			'selected_value'  => array_key_exists( 'selected_form_id',
				$settings ) && array_key_exists( 'selected_group_list_id', $settings )
				? $settings['selected_group_list_id'] . '-' . $settings['selected_form_id'] : ''
		) );
	}

	/**
	 * Renders success message for form widget
	 */
	public function field_message_success() {
		echo View::file( '/admin/form/field-message.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_message_success'
			)
		);
	}

	/**
	 * Renders already subscribed message for form widget
	 */
	public function field_message_entry_exists() {
		echo View::file( '/admin/form/field-message.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_message_entry_exists'
			)
		);
	}

	/**
	 *  Renders error message for form widget
	 */
	public function field_message_error() {
		echo View::file( '/admin/form/field-message.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_message_error'
			)
		);
	}

	/**
	 *  Renders invalid message for form widget
	 */
	public function field_message_invalid_email() {
		echo View::file( '/admin/form/field-message.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_message_invalid_email'
			)
		);
	}

	/**
	 *  Renders required field message for form widget
	 */
	public function field_message_required_field() {
		echo View::file( '/admin/form/field-message.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_message_required_field'
			)
		);
	}

	/**
	 * Form builder
	 * Render available attributes in form builder page
	 */
	public function field_attributes_available() {
		echo View::file( '/admin/form/field-attributes.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_attributes_available'
			)
		);
	}

	/**
	 * Form builder
	 * Render used attributes in form builder page
	 */
	public function field_attributes_used() {
		echo View::file( '/admin/form/field-attributes.php' )->render( array(
				'options' => $this->configuration->get_settings(),
				'message' => 'form_attributes_used'
			)
		);
	}

	/**
	 * Form builder
	 * Render select box for setting label position
	 */
	public function field_label_position() {
		$available_options = array(
			'left'   => __( 'Left of field', 'cleverreach' ),
			'top'    => __( 'Above field', 'cleverreach' ),
			'right'  => __( 'Right of field', 'cleverreach' ),
			'inside' => __( 'Inside field', 'cleverreach' )
		);

		echo View::file( '/admin/form/field-label-position.php' )->render( array(
			'options'           => $this->configuration->get_settings(),
			'available_options' => $available_options
		) );
	}
}