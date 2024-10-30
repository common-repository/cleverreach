<?php

namespace CleverReach\Newsletter\Database\Migrations;

use CleverReach\Newsletter\Database\Abstract_Migration;
use CleverReach\Newsletter\Repository\Config_Repository;
use CleverReach\Newsletter\Widget\CleverReach_Widget;

class Migration_2_3_0 extends Abstract_Migration {
	const CURRENT_SETTINGS_OPTION_NAME = "haet_cleverreach_settings";
	const CURRENT_WIDGET_SETTINGS_OPTION_NAME = "haet_cleverreach_widget";

	const SETTINGS_NAMES = [
		'show_in_comments'                => 'show_comments_section_checkbox',
		'show_in_comments_caption'        => 'caption_for_comments_section_checkbox',
		'show_in_comments_defaultchecked' => 'show_comments_section_checkbox_default',
		'label_position'                  => 'label_position_in_from',
		'message_error'                   => 'form_message_error',
		'message_success'                 => 'form_message_success',
		'message_entry_exists'            => 'form_message_entry_exists',
		'message_invalid_email'           => 'form_message_invalid_email',
		'message_required_field'          => 'form_message_required_field',
		'signup_form_id'                  => 'selected_form_id',
		'signup_list_id'                  => 'selected_group_list_id',
		'lists'                           => 'group_lists',
		'attributes_used'                 => 'form_attributes_used',
		'attributes_available'            => 'form_attributes_available',
		'show_in_comments_form'           => 'selected_group_and_form',
		'show_in_comments_name_attribute' => 'selected_name_attribute',
	];

	/**
	 * Executes migration
	 */
	public function execute() {
		$this->migrate_general_settings();
		$this->migrate_widget_settings();
	}

	/**
	 * Migrate settings
	 */
	private function migrate_general_settings() {
		$settings = get_option( self::CURRENT_SETTINGS_OPTION_NAME );
		if ( ! $settings ) {
			return;
		}

		$new_settings = [];

		foreach ( $settings as $key => $setting ) {
			if ( array_key_exists( $key, self::SETTINGS_NAMES ) ) {
				$new_settings[ self::SETTINGS_NAMES[ $key ] ] = $setting;
			} else {
				$new_settings[ $key ] = $setting;
			}
		}

		if ( array_key_exists( 'group_lists', $new_settings ) ) {
			$new_settings['group_lists'] = $this->change_list_type( $new_settings['group_lists'] );
		}

		update_option( Config_Repository::SETTINGS_OPTION_NAME, $new_settings );
		delete_option( self::CURRENT_SETTINGS_OPTION_NAME );
	}

	/**
	 * Migrate widget settings
	 */
	private function migrate_widget_settings() {
		$widget_settings = get_option( 'widget_' . self::CURRENT_WIDGET_SETTINGS_OPTION_NAME );
		update_option( 'widget_' . CleverReach_Widget::CLEVERREACH_WIDGET_ID, $widget_settings );
		delete_option( 'widget_' . self::CURRENT_WIDGET_SETTINGS_OPTION_NAME );

		$sidebar_widgets = get_option( 'sidebars_widgets' );

		foreach ( $sidebar_widgets as &$sidebar ) {
			$cr_widgets = is_array( $sidebar ) ? preg_grep( '/' . self::CURRENT_WIDGET_SETTINGS_OPTION_NAME . '/', $sidebar ) : null;
			if ( $cr_widgets ) {
				foreach ( $cr_widgets as $key => $cr_widget ) {
					$index           = explode( '-', $cr_widget )[1];
					$sidebar[ $key ] = CleverReach_Widget::CLEVERREACH_WIDGET_ID . '-' . $index;
				}
			}
		}

		update_option( 'sidebars_widgets', $sidebar_widgets );
	}

	/**
	 * Changes list type from stdClass to array
	 *
	 * @param $settings_lists
	 *
	 * @return array
	 */
	private function change_list_type( $settings_lists ) {
		$lists = [];
		foreach ( $settings_lists as &$list ) {
			$list_array                 = array();
			$list_array['id']           = $list->id;
			$list_array['name']         = $list->name;
			$list_array['is_locked']    = $list->isLocked;
			$list_array['stamp']        = $list->stamp;
			$list_array['last_mailing'] = $list->last_mailing;
			$list_array['last_changed'] = $list->last_changed;
			$list_array['count']        = $list->count;
			$list_array['forms']        = array();
			foreach ( $list->forms as &$form ) {
				$form_array                       = array();
				$form_array['id']                 = $form->id;
				$form_array['name']               = $form->name;
				$form_array['customer_tables_id'] = $form->customer_tables_id;
				$list_array['forms'][]            = $form_array;
			}
			unset( $form );
			$lists[] = $list_array;
		}

		return $lists;
	}
}