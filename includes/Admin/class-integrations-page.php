<?php

namespace CleverReach\Newsletter\Admin;

use CleverReach\Newsletter\CleverReach;
use CleverReach\Newsletter\Proxy\CleverReach_ResourceProxy;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\Logger;
use CleverReach\Newsletter\Utility\View;
use Exception;

class CleverReach_IntegrationsPage {

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
	 * CleverReach_IntegrationsPage constructor.
	 */
	public function __construct() {
		$this->configuration  = new CleverReach_Configuration();
		$this->resource_proxy = new CleverReach_ResourceProxy();
		$this->auth_service   = new Auth_Service();
		$this->info_banner    = new CleverReach_InfoBanner();
	}

	public function register_settings() {
		add_settings_section(
			'integration_comments',
			__( 'Comment Form Integration', 'cleverreach' ),
			array( $this, 'section_intro' ),
			'cleverreach_page_cleverreach-integrations'
		);
		add_settings_field(
			'show_comments_section_checkbox',
			__( 'Show checkbox in comments', 'cleverreach' ),
			array( $this, 'field_show_comments_section_checkbox' ),
			'cleverreach_page_cleverreach-integrations',
			'integration_comments'
		);
		add_settings_field(
			'caption_for_comments_section_checkbox',
			__( 'Checkbox caption', 'cleverreach' ),
			array( $this, 'field_caption_for_comments_section_checkbox' ),
			'cleverreach_page_cleverreach-integrations',
			'integration_comments'
		);
		add_settings_field(
			'selected_group_and_form',
			__( 'CleverReach form', 'cleverreach' ),
			array( $this, 'field_form_selection' ),
			'cleverreach_page_cleverreach-integrations',
			'integration_comments'
		);
		add_settings_field(
			'selected_name_attribute',
			__( 'Name attribute', 'cleverreach' ),
			array( $this, 'field_name_attribute_selection' ),
			'cleverreach_page_cleverreach-integrations',
			'integration_comments'
		);
		add_settings_field(
			'show_comments_section_checkbox_default',
			__( 'Checked by default', 'cleverreach' ),
			array( $this, 'field_comments_section_checkbox_default' ),
			'cleverreach_page_cleverreach-integrations',
			'integration_comments'
		);
	}

	public function add_admin_pages() {
		add_submenu_page(
			'cleverreach',
			__( 'CleverReach Form Integrations', 'cleverreach' ),
			__( 'Integrations', 'cleverreach' ),
			apply_filters( 'haet_cleverreach_capabilities', 'manage_options' ),
			'cleverreach-integrations',
			array( $this, 'show_settings_integrations' )
		);
	}

	public function section_intro() {
	}

	/**
	 * Integrations tab
	 * Renders field for setting checkbox in comments section
	 */
	public function field_show_comments_section_checkbox() {
		echo View::file( '/admin/integrations/field-show-comments-section-checkbox.php' )->render( array(
			'options' => $this->configuration->get_settings(),
		) );
	}

	/**
	 * Settings Page Integrations
	 *
	 */
	public function show_settings_integrations() {
		$settings      = $this->configuration->get_settings();
		$is_successful = false;
		$message       = $this->info_banner->get_please_reconnect_message();
		if ( $this->auth_service->get_access_token() ) {
			list( $is_successful, $message ) = $this->info_banner->get_integration_page_message( $settings );
		}

		echo View::file( '/admin/settings-integrations.php' )->render( array(
				'settings'        => $settings,
				'plugin_url'      => CleverReach::get_plugin_url(),
				'plugin_dir_path' => CleverReach::get_plugin_dir_path(),
				'access_token'    => $this->auth_service->get_access_token(),
				'message'         => $message,
				'is_successful'   => $is_successful
			)
		);
	}

	/**
	 * Integrations tab
	 * Renders field for setting caption for checkbox in comments section
	 */
	public function field_caption_for_comments_section_checkbox() {
		echo View::file( '/admin/integrations/field-caption-for-comments-section-checkbox.php' )->render( array(
			'options' => $this->configuration->get_settings()
		) );
	}

	/**
	 * Integrations tab
	 * Renders field for form selection
	 */
	public function field_form_selection() {
		$options = $this->configuration->get_settings();
		if ( isset( $options['group_lists'] ) && is_array( $options['group_lists'] ) ) {
			echo View::file( '/admin/integrations/field-form-selection.php' )->render( array(
					'options' => $options,
				)
			);
		}
	}

	/**
	 * Integrations tab
	 * Renders field for name attribute selection
	 */
	public function field_name_attribute_selection() {
		$settings = $this->configuration->get_settings();
		if ( $this->auth_service->get_access_token() ) {
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) { //WPML is active
				$languages = apply_filters( 'wpml_active_languages', null, 'orderby=id&order=desc' );
				if ( ! empty( $languages ) ) {
					foreach ( $languages as $language ) {
						$list_form = $settings[ 'selected_group_and_form_' . $language['language_code'] ];

						$this->render_list_attributes( $list_form, $settings, $language );
					}
				}
			} else {
				$list_form = ( array_key_exists( 'selected_group_and_form',
					$settings ) ? $settings['selected_group_and_form'] : false );

				$this->render_list_attributes( $list_form, $settings );
			}
		}
	}

	/**
	 * Integrations tab
	 * Renders field for setting default for checkbox in comments section
	 */
	public function field_comments_section_checkbox_default() {
		echo View::file( '/admin/integrations/field-comments-section-checkbox-default.php' )->render( array(
			'options' => $this->configuration->get_settings()
		) );
	}

	/**
	 * Renders list attributes
	 *
	 * @param $list_form
	 * @param $settings
	 * @param null $language
	 */
	private function render_list_attributes( $list_form, $settings, $language = null ) {
		$list_form_array   = explode( '-', $list_form );
		$attributes_result = null;
		if ( count( $list_form_array ) === 2 ) {
			$form_id = $list_form_array[1];
			$list_id = $list_form_array[0];

			if ( $form_id && $list_id ) {
				try {
					$attributes_result            = $this->resource_proxy->get_list_attributes( $list_id );
					$attributes_result['success'] = true;
				} catch ( Exception $exception ) {
					$attributes_result['success'] = false;
					$attributes_result['message'] = __( 'Could not connect to the CleverReach API.' );
					Logger::error( 'Could not load lists. ' . $exception->getMessage() );
				}
			}
		}

		if ( ! empty( $attributes_result ) && $attributes_result['success'] ) {
			echo View::file( '/admin/integrations/field-name-attribute-selection.php' )->render(
				array(
					'attributes' => $attributes_result,
					'settings'   => $settings,
					'language'   => $language
				)
			);
		} else {
			_e( 'Please select a CleverReach form above.', 'cleverreach' );
			echo '<br>';
		}
	}
}