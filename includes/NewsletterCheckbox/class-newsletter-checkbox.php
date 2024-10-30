<?php

namespace CleverReach\Newsletter\NewsletterCheckbox;

use CleverReach\Newsletter\Proxy\CleverReach_ResourceProxy;
use CleverReach\Newsletter\Services\Business\Auth_Service;
use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\View;
use stdClass;

class CleverReach_NewsletterCheckbox {
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
	 * CleverReach_NewsletterCheckbox constructor.
	 */
	public function __construct() {
		$this->configuration = new CleverReach_Configuration();
		$this->resourceProxy = new CleverReach_ResourceProxy();
		$this->auth_service  = new Auth_Service();
	}

	/**
	 * Initialize
	 */
	public function init() {
		add_action( 'comment_form', array( $this, 'show_checkbox_comments' ) );
		add_action( 'comment_post', array( $this, 'process_form_comment' ), 40, 2 );
	}

	/**
	 * Renders newsletter checkbox in comments section
	 */
	public function show_checkbox_comments() {
		$settings = $this->configuration->get_settings();
		if ( isset( $settings['show_comments_section_checkbox'] ) && (int) $settings['show_comments_section_checkbox'] === 1 ) {
			echo View::file( '/storefront/newsletter-checkbox.php' )->render( array(
				'settings' => $settings
			) );
		}
	}

	/**
	 * Process form in comment section
	 *
	 * @param $comment_id
	 * @param string $comment_approved
	 *
	 * @return false
	 */
	public function process_form_comment( $comment_id, $comment_approved = '' ) {
		$settings = $this->configuration->get_settings();

		if ( ! isset( $_POST['cleverreach_checkbox_comments'] ) || (int) $_POST['cleverreach_checkbox_comments'] !== 1 ) {
			return false;
		}

		if ( $comment_approved === 'spam' ) {
			return false;
		}

		$comment = get_comment( $comment_id );

		$fields                             = array();
		$fields['cleverreach_email']        = new stdClass();
		$fields['cleverreach_email']->value = $comment->comment_author_email;

		if ( $this->auth_service->get_access_token() ) {
			if ( defined( 'ICL_LANGUAGE_CODE' ) ) { //WPML is active
				$attribute = $settings[ 'selected_name_attribute_' . ICL_LANGUAGE_CODE ];
				$list_form = $settings[ 'selected_group_and_form_' . ICL_LANGUAGE_CODE ];
			} else {
				$attribute = $settings['selected_name_attribute'];
				$list_form = $settings['selected_group_and_form'];
			}

			$fields[ $attribute ]        = new stdClass();
			$fields[ $attribute ]->value = $comment->comment_author;
		}

		if ( isset( $this->resourceProxy ) ) {
			$list_form_array = explode( '-', $list_form );
			$form_id         = $list_form_array[1];
			$list_id         = $list_form_array[0];
			$source          = get_bloginfo( 'name' ) . ' (Comments)';
			$this->resourceProxy->subscribe_user( $settings, $fields, $form_id, $list_id, $source );
		}
	}
}