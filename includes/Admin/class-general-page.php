<?php

namespace CleverReach\Newsletter\Admin;

use CleverReach\Newsletter\CleverReach;
use CleverReach\Newsletter\Proxy\CleverReach_ResourceProxy;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\Logger;
use CleverReach\Newsletter\Utility\View;
use Exception;

class CleverReach_GeneralPage {

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
	 * CleverReach_GeneralPage constructor.
	 */
	public function __construct() {
		$this->configuration  = new CleverReach_Configuration();
		$this->resource_proxy = new CleverReach_ResourceProxy();
		$this->auth_service   = new Auth_Service();
		$this->info_banner    = new CleverReach_InfoBanner();
	}

	public function register_settings() {
		add_settings_section(
			'general_settings',
			__( 'General settings', 'cleverreach' ),
			array( $this, 'section_intro' ),
			'toplevel_page_cleverreach'
		);

		add_settings_field(
			'token',
			__( 'CleverReach Connection', 'cleverreach' ),
			array( $this, 'authenticate' ),
			'toplevel_page_cleverreach',
			'general_settings'
		);
	}

	public function add_admin_pages() {
		add_menu_page(
			'CleverReach',
			'CleverReach',
			apply_filters( 'haet_cleverreach_capabilities', 'manage_options' ),
			'cleverreach',
			array( $this, 'show_settings_general' ),
			CleverReach::get_plugin_url() . '/resources/images/menu-icon.png',
			'99.3235345'
		);
	}

	public function section_intro() {
	}

	/**
	 * General page
	 * Performs authentication
	 */
	public function authenticate() {
		$options   = $this->configuration->get_settings();
		$client_id = $this->configuration->get_client_id();
		$auth_url  = $this->configuration->get_auth_url();

		$redirect_url = admin_url( 'admin.php?page=cleverreach&cleverreach-athenticate=1' );
		$message      = '';

		if ( isset( $_GET["cleverreach-athenticate"] ) && isset( $_GET["code"] ) ) {
			Logger::info( "Connecting to CleverReach." );

			try {
				$auth_info = $this->resource_proxy->get_auth_info( sanitize_text_field( $_GET["code"] ), $redirect_url );
				$this->auth_service->set_auth_info( $auth_info );

				//Save lists
				$lists                  = $this->resource_proxy->get_lists();
				$options['group_lists'] = $lists;

				//Save customer ID
				$user_info      = $this->resource_proxy->get_user_info();
				$cleverreach_id = array_key_exists( 'id', $user_info ) ? $user_info['id'] : '';
				if ( array_key_exists( 'cleverreach_id', $options ) && $options['cleverreach_id'] !== $cleverreach_id ) {
					$this->configuration->delete_settings();
				}

				$options['cleverreach_id'] = $cleverreach_id;
				$this->configuration->save_settings( array('group_lists' => $lists, 'cleverreach_id' => $cleverreach_id) );
			} catch ( Exception $exception ) {
				Logger::error( "Couldn't connect to CleverReach. " );
				$message = '<div class="error"><p>' . $exception->getMessage() . '</p></div>';
			}
		}

		echo View::file( '/admin/general/authenticate.php' )->render( array(
			'options'      => $options,
			'auth_url'     => $auth_url,
			'client_id'    => $client_id,
			'redirect_url' => urlencode( $redirect_url ),
			'message'      => $message,
		) );
	}

	/**
	 * Settings Page General
	 */
	public function show_settings_general() {
		$settings      = $this->configuration->get_settings();
		$is_successful = true;
		$api_message   = __( 'Please click the button below to connect to your CleverReach account.<br><br>If you don\'t have an account yet <a href="https://www.cleverreach.com/en/login/" target="_blank">SIGNUP HERE</a> for a free newsletter system for up to 250 receivers.',
			'cleverreach' );
		$form_messages = [];
		$refresh_lists = false;
		$list_result   = array();

		if ( array_key_exists( 'token', $settings ) ) {
			$is_successful = false;
			$api_message   = $this->info_banner->get_please_reconnect_message();
		}

		if ( $access_token = $this->auth_service->get_access_token() ) {
			$is_successful = true;
			$api_message   = __( 'Successfully connected to CleverReach.', 'cleverreach' );

			try {
				if ( isset($_POST['haet_cleverreach_refresh']) || ! isset( $settings['group_lists'] ) ) {
					$lists = $this->resource_proxy->get_lists();
					$settings['group_lists'] = $lists;
					$this->configuration->save_settings( array( 'group_lists' => $lists ) );
				}

				if ( ! $this->info_banner->newsletter_form_exists( $settings['group_lists'] ) ) {
					$is_successful   = false;
					$form_messages[] = $this->info_banner->get_integration_nonexistent_selected_form_message();
				}

				if ( ! $this->info_banner->form_builder_form_exists( $settings['group_lists'] ) ) {
					$is_successful   = false;
					$form_messages[] = $this->info_banner->get_form_builder_nonexistent_selected_form_message();
				}

				$list_result['success'] = ! empty( $lists );
				$list_result['message'] = empty( $lists ) ? __( 'You don\'t have any CleverReach forms.',
					'cleverreach' ) : '';
			} catch ( Exception $exception ) {
				$list_result['success'] = false;
				$list_result['message'] = __( 'Could not load lists.',
						'cleverreach' ) . ' [ ' . $exception->getMessage() . ' ]';
			}
		}

		echo View::file( '/admin/settings-general.php' )->render( array(
			'plugin_url'      => CleverReach::get_plugin_url(),
			'plugin_dir_path' => CleverReach::get_plugin_dir_path(),
			'is_successful'   => $is_successful,
			'api_message'     => $api_message,
			'refresh_lists'   => $refresh_lists,
			'list_result'     => $list_result,
			'settings'        => $settings,
			'access_token'    => $access_token ?? null,
			'form_messages'   => $form_messages,
		) );
	}
}