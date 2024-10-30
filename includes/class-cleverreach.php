<?php

namespace CleverReach\Newsletter;

use CleverReach\Newsletter\Admin\CleverReach_FormBuilderPage;
use CleverReach\Newsletter\Admin\CleverReach_GeneralPage;
use CleverReach\Newsletter\Admin\CleverReach_InfoBanner;
use CleverReach\Newsletter\Admin\CleverReach_IntegrationsPage;
use CleverReach\Newsletter\Database\Exceptions\Migration_Exception;
use CleverReach\Newsletter\Form\CleverReach_Form;
use CleverReach\Newsletter\NewsletterCheckbox\CleverReach_NewsletterCheckbox;
use CleverReach\Newsletter\Repository\Auth_Info_Repository;
use CleverReach\Newsletter\Repository\Plugin_Options_Repository;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\Database;
use CleverReach\Newsletter\Utility\Logger;
use CleverReach\Newsletter\Utility\View;
use CleverReach\Newsletter\Widget\CleverReach_Widget;

class CleverReach {
	const PLUGIN_VERSION = '2.3.0';

	/**
	 * @var CleverReach
	 */
	protected static $instance;

	/**
	 * @var string
	 */
	private $cleverreach_plugin_path;

	/**
	 * @var CleverReach_Configuration
	 */
	private $configuration;

	/**
	 * @var CleverReach_Form
	 */
	private $form;

	/**
	 * @var Database
	 */
	private $database;

	/**
	 * @var Auth_Service
	 */
	private $auth_service;

	/**
	 * @var CleverReach_GeneralPage
	 */
	private $general_page;

	/**
	 * @var CleverReach_IntegrationsPage
	 */
	private $integrations_page;

	/**
	 * @var CleverReach_FormBuilderPage
	 */
	private $form_builder_page;

	/**
	 * CleverReach constructor.
	 *
	 * @param $cleverreach_plugin_path
	 */
	public function __construct( $cleverreach_plugin_path ) {
		$this->cleverreach_plugin_path = $cleverreach_plugin_path;
		$this->configuration           = new CleverReach_Configuration();
		$this->database                = new Database( new Plugin_Options_Repository() );
		$this->auth_service            = new Auth_Service();
		$this->general_page            = new CleverReach_GeneralPage();
		$this->integrations_page       = new CleverReach_IntegrationsPage();
		$this->form_builder_page       = new CleverReach_FormBuilderPage();
	}

	/**
	 * Plugin Initialization
	 */
	public static function init( $cleverreach_plugin_path ) {
		if ( self::$instance === null ) {
			self::$instance = new self( $cleverreach_plugin_path );
		}

		self::$instance->initialize();

		return self::$instance;
	}

	/**
	 * Initializes plugin
	 */
	public function initialize() {
		if ( class_exists( CleverReach_Form::class ) ) {
			$this->form = new CleverReach_Form();
			( new CleverReach_NewsletterCheckbox() )->init();
		}

		try {
			$this->database->update( is_multisite() );
		} catch ( Migration_Exception $e ) {
			Logger::error( 'Unable to migrate database:' . $e->getMessage(),
				array( 'trace' => $e->getTraceAsString() ) );
		}

		register_activation_hook( $this->cleverreach_plugin_path, array( $this, 'cleverreach_activate' ) );
		register_deactivation_hook( $this->cleverreach_plugin_path, array( $this, 'cleverreach_deactivate' ) );
		add_action( 'admin_menu', array( $this, 'add_admin_pages' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_page_scripts_and_styles' ) );
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		add_action( 'wp_ajax_cleverreach_preview_form', array( $this, 'ajax_preview_form' ) );
		add_action( 'widgets_init', array( $this, 'cleverreach_register_widget' ) );
		add_action( 'plugins_loaded', array( $this, 'cleverreach_load' ) );
		register_shutdown_function( array( $this, 'log_errors' ) );
	}

	/**
	 * Delete data from database on plugin uninstall
	 */
	public static function uninstall() {
		$configuration            = new CleverReach_Configuration();
		$plugin_option_repository = new Plugin_Options_Repository();
		$auth_info_repository     = new Auth_Info_Repository();
		if ( is_multisite() ) {
			$sites = get_sites();
			foreach ( $sites as $site ) {
				switch_to_blog( $site->blog_id );
				$configuration->delete_settings();
				$plugin_option_repository->delete_schema_version();
				$auth_info_repository->delete_auth_info();

				restore_current_blog();
			}
		} else {
			$configuration->delete_settings();
			$plugin_option_repository->delete_schema_version();
			$auth_info_repository->delete_auth_info();
		}
	}

	/**
	 * Returns base directory path
	 *
	 * @return string
	 */
	public static function get_plugin_dir_path() {
		return rtrim( plugin_dir_path( __DIR__ ), '/' );
	}

	/**
	 * Gets plugin url
	 *
	 * @return string
	 */
	public static function get_plugin_url() {
		return rtrim( plugin_dir_url( __DIR__ ), '/' );
	}

	/**
	 * Activation
	 */
	public function cleverreach_activate() {
	}

	/**
	 * Deactivation
	 */
	public function cleverreach_deactivate() {
	}

	/**
	 * Register widget
	 */
	public function cleverreach_register_widget() {
		register_widget( CleverReach_Widget::class );
	}

	/**
	 * Adds action on plugins_loaded
	 */
	public function cleverreach_load() {
		load_plugin_textdomain( 'cleverreach', false,
			basename( dirname( $this->cleverreach_plugin_path ) ) . '/i18n/languages/' );

		if ( defined( 'HAET_CLEVERREACHWOOCOMMERCE_PATH' ) ) {
			$woocommerce_plugin_data = get_plugin_data( HAET_CLEVERREACHWOOCOMMERCE_PATH . '/cleverreach-woocommerce.php' );

			if ( version_compare( $woocommerce_plugin_data['Version'], '2.0', '<' ) ) {
				add_action( 'admin_notices', array( $this, 'cleverreach_woocommerce_version_notice' ) );
			}
		}

		add_action( 'admin_notices', array( $this, 'cleverreach_reconnect_notice' ) );
	}

	/**
	 * WooCommerce version notice
	 */
	public function cleverreach_woocommerce_version_notice() {
		$min_version = '2.0';
		$message     = printf( __( '<strong>Warning:</strong> CleverReach has changed its API, so you really <strong>have to</strong> update CleverReach WooCommerce to version %s.',
			'cleverreach' ), $min_version );

		echo View::file( '/admin/notices/notice-warning.php' )->render( array(
			'message' => $message
		) );
	}

	/**
	 * On plugin install/update renders notice for reconnection
	 */
	public function cleverreach_reconnect_notice() {
		if ( ! $this->auth_service->get_access_token() && array_key_exists( 'token',
				$this->configuration->get_settings() ) ) {
			$message = ( new CleverReach_InfoBanner() )->get_please_reconnect_message();
			echo View::file( '/admin//notices/notice-error.php' )->render( array(
				'message' => $message
			) );
		}
	}

	/**
	 * Register all settings and fields
	 */
	public function register_settings() {
		register_setting( 'cleverreach_newsletter_option_group', 'cleverreach_newsletter_settings',
			array( $this->configuration, 'update_setting_fields' ) );

		$this->general_page->register_settings();
		$this->integrations_page->register_settings();
		$this->form_builder_page->register_settings();
	}

	/**
	 * Creates admin pages for CleverReach
	 */
	public function add_admin_pages() {
		$this->general_page->add_admin_pages();
		$this->integrations_page->add_admin_pages();
		$this->form_builder_page->add_admin_pages();
	}

	/**
	 * Load Admin JS and CSS
	 */
	public function admin_page_scripts_and_styles( $page ) {
		// Gutenberg compatibility
		if ( function_exists( 'register_block_type' ) ) {
			wp_register_script(
				'cleverreach-gutenberg-js',
				self::get_plugin_url() . '/resources/js/cleverreach-gutenberg.js',
				[ 'wp-blocks', 'wp-element' ]
			);

			wp_register_style(
				'cleverreach-frontend-styles',
				self::get_plugin_url() . '/resources/css/frontend.css'
			);

			wp_localize_script( 'cleverreach-gutenberg-js', 'haet_cr_ajax',
				array(
					'ajax_url'     => admin_url( 'admin-ajax.php' ),
					'translations' => array(
						'description' => __( 'Embed your signup form', 'cleverreach' ),
						'loading'     => __( 'Loading signup form...', 'cleverreach' ),
						'editform'    => __( 'Edit form in new tab', 'cleverreach' )
					)
				)
			);

			register_block_type( 'cleverreach/form', [
				'editor_script'   => 'cleverreach-gutenberg-js',
				'editor_style'    => 'cleverreach-frontend-styles',
				'render_callback' => array( new CleverReach_Form(), 'render_block' )
			] );
		}

		if ( strpos( $page, 'page_cleverreach' ) ) {
			wp_enqueue_script( 'cleverreach_admin_script',
				self::get_plugin_url() . '/resources/js/admin_script.js',
				array( 'jquery-ui-sortable', 'jquery' ) );
			wp_localize_script( 'cleverreach_admin_script', 'haet_cr_ajax',
				array(
					'ajax_url'     => admin_url( 'admin-ajax.php' ),
					'translations' => array(
						'label'             => __( 'Label', 'cleverreach' ),
						'text'              => __( 'Text', 'cleverreach' ),
						'required'          => __( 'required', 'cleverreach' ),
						'available_options' => __( 'Available Options', 'cleverreach' ),
					)
				)
			);
			wp_enqueue_style( 'cleverreach_admin_style',
				self::get_plugin_url() . '/resources/css/backend.css' );
		}
	}

	/**
	 * Shows form
	 */
	public function ajax_preview_form() {
		$this->form->show_form();
		die();
	}

	/**
	 * Logs errors
	 */
	public function log_errors() {
		$error = error_get_last();
		if ( $error && in_array( $error['type'], array(
				E_ERROR,
				E_PARSE,
				E_COMPILE_ERROR,
				E_USER_ERROR,
				E_RECOVERABLE_ERROR
			), true ) ) {
			Logger::critical( sprintf( '%1$s in %2$s on line %3$s', $error['message'], $error['file'],
					$error['line'] ) .
			                  PHP_EOL );
		}
	}
}