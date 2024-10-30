<?php

namespace CleverReach\Newsletter\Form;

use CleverReach\Newsletter\CleverReach;
use CleverReach\Newsletter\Proxy\CleverReach_ResourceProxy;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\View;
use stdClass;

class CleverReach_Form {
	/**
	 * @var CleverReach_Configuration
	 */
	private $configuration;

	/**
	 * @var CleverReach_ResourceProxy
	 */
	private $resourceProxy;

	/**
	 * @var Auth_Service
	 */
	private $auth_service;

	/**
	 * CleverReach_Form constructor.
	 */
	public function __construct() {
		$this->configuration = new CleverReach_Configuration();
		$this->resourceProxy = new CleverReach_ResourceProxy();
		$this->auth_service  = new Auth_Service();
		add_action( 'init', array( $this, 'process_form' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'form_scripts_and_styles' ) );
		add_action( 'wp_ajax_cleverreach_submit', array( $this, 'ajax_submit' ) );
		add_action( 'wp_ajax_nopriv_cleverreach_submit', array( $this, 'ajax_submit' ) );
		add_shortcode( 'cleverreach_signup', array( $this, 'register_shortcode' ) );
	}

	/**
	 *  Register Shortcode [cleverreach_signup]
	 */
	function register_shortcode( $atts ) {
		ob_start();
		$this->show_form( false );

		return ob_get_clean();
	}

	/**
	 *  Load Frontent JS and CSS
	 */
	public function form_scripts_and_styles( $page ) {
		wp_enqueue_script( 'cleverreach_script', CleverReach::get_plugin_url() . '/resources/js/form.js',
			array( 'jquery' ), '', true );
		wp_localize_script( 'cleverreach_script', 'haet_cr_ajax',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ) )
		);
		wp_enqueue_style( 'cleverreach_style', CleverReach::get_plugin_url() . '/resources/css/frontend.css' );
	}

	/**
	 *  Process the submitted form
	 */
	public function process_form( $submission = null ) {
		if ( ! $submission ) {
			$submission = $_POST;
		}

		if ( isset( $submission['haet-cleverreach-form-id'] ) && isset( $submission['haet-cleverreach-list-id'] ) ) {
			$settings = $this->configuration->get_settings();
			if ( isset( $settings['form_attributes_used'] ) ) {
				$attributes            = json_decode( $settings['form_attributes_used'] );
				$validation            = new stdClass();
				$validation->is_widget = (bool) sanitize_text_field( $submission['haet-cleverreach-is-widget'] );
				$validation->valid     = true;
				$validation->message   = $settings['form_message_success'];

				foreach ( $attributes as $attribute ) {
					$submission_field = array_key_exists( 'haet-cleverreach-' . $attribute->field,
						$submission ) ? sanitize_text_field( $submission[ 'haet-cleverreach-' . $attribute->field ] ) : null;
					if ( in_array( $attribute->type, array( 'text', 'email', 'gender', 'number', 'date' ) ) ) {
						if ( isset( $submission_field ) ) {
							$valid = true;
							$error = '';
							if ( $attribute->type == 'email' ) {
								list( $valid, $error ) = $this->validate_email( $submission_field, $settings,
									$validation );
							}
							$this->set_validation_fields( $validation, $attribute, $valid, $submission_field,
								$error );
						} else {
							//field missing
							$validation->message = $settings['form_message_invalid_email'];
						}
					} elseif ( $attribute->type === 'policy_confirm' && $submission_field !== null ) {
						if ( $submission_field === '' ) {
							$validation->valid   = false;
							$validation->message = '';
							$this->set_validation_fields( $validation, $attribute, false, $submission_field,
								$settings['form_message_required_field'] );
						}
					}
				}

				if ( ! $this->auth_service->get_access_token() ) {
					$validation->valid   = false;
					$validation->message = __( 'There has been an error, please try again later.', 'cleverreach' );
				} elseif ( $validation->valid ) {
					$form_id = $settings['selected_form_id'];
					$list_id = $settings['selected_group_list_id'];
					if ( $validation->is_widget ) {
						$source = get_bloginfo( 'name' ) . ' (Widget)';
					} else {
						$source = get_bloginfo( 'name' ) . ' (Newsletter form)';
					}
					$subscription_result = $this->resourceProxy->subscribe_user( $settings, $validation->fields,
						$form_id, $list_id, $source );
					if ( ! $subscription_result['success'] ) {
						$validation->valid   = false;
						$validation->message = $subscription_result['message'];
					}
				}
				if ( ! session_id() ) {
					session_start();
				}
				$_SESSION['haet_cleverreach_validation'] = $validation;
			}
		}
	}

	/**
	 *  Output the form
	 */
	public function show_form( $is_widget = false ) {
		if ( session_id() && isset( $_SESSION['haet_cleverreach_validation'] ) ) {
			$validation = $_SESSION['haet_cleverreach_validation'];
			// if two forms are displayed on the same page (sidebar && content) assign validation to the correct form
			if ( ( $validation->is_widget && $is_widget ) || ( $validation->is_widget === false && ! $is_widget ) ) {
				unset( $_SESSION['haet_cleverreach_validation'] );
			} else {
				unset( $validation );
			}
		}

		echo View::file( '/storefront/form.php' )->render(
			array(
				'validation' => $validation ?? null,
				'is_widget'  => $is_widget,
				'settings'   => $this->configuration->get_settings(),
			)
		);
	}

	/**
	 *  Form submitted via AJAX
	 */
	public function ajax_submit() {
		if ( isset( $_POST['submission'] ) ) {
			$submission = $_POST['submission'];
			$this->process_form( $submission );
			$this->show_form( sanitize_text_field( $_POST['submission']['haet-cleverreach-is-widget'] ) );
		}
		wp_die();
	}

	/**
	 * Validate email
	 *
	 * @param $submission_field
	 * @param $settings
	 * @param $validation
	 *
	 * @return array
	 */
	private function validate_email( &$submission_field, $settings, $validation ) {
		$valid = true;
		$error = '';
		if ( $submission_field === '' ) {
			$valid               = false;
			$validation->valid   = false;
			$error               = $settings['form_message_required_field'];
			$validation->message = $settings['form_message_invalid_email'];
		} elseif ( ! is_email( $submission_field ) ) {
			$valid               = false;
			$validation->valid   = false;
			$error               = $settings['form_message_invalid_email'];
			$validation->message = $settings['form_message_invalid_email'];
			$submission_field    = str_replace( '"', '', $submission_field );
			$submission_field    = strip_tags( $submission_field );
		}

		return [ $valid, $error ];
	}

	/**
	 * Sets validation fields
	 *
	 * @param $validation
	 * @param $attribute
	 * @param $valid
	 * @param $submission_field
	 * @param $error
	 */
	private function set_validation_fields( $validation, $attribute, $valid, $submission_field, $error ) {
		$validation->fields[ $attribute->field ]        = new stdClass();
		$validation->fields[ $attribute->field ]->valid = $valid;
		$validation->fields[ $attribute->field ]->value = $submission_field;
		$validation->fields[ $attribute->field ]->error = $error;
	}
}