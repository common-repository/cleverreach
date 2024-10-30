<?php

namespace CleverReach\Newsletter\Widget;

use CleverReach\Newsletter\Utility\CleverReach_Configuration;
use CleverReach\Newsletter\Utility\View;
use WP_Widget;

class CleverReach_Widget extends WP_Widget {
	const CLEVERREACH_WIDGET_ID = "cleverreach_mail_widget";
	
	public function __construct() {
		// Instantiate the parent object
		parent::__construct( self::CLEVERREACH_WIDGET_ID, __( 'CleverReach Sign-Up Form', 'cleverreach' ) );
	}

	/**
	 * Show widget in storefront
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'];
			echo esc_html( $instance['title'] );
			echo $args['after_title'];
		}
		$configuration = new CleverReach_Configuration();
		$configuration->print_cleverreach_form( true );
		echo $args['after_widget'];
	}

	/**
	 * Updates widget
	 *
	 * @param array $new_instance
	 * @param array $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		$instance['title'] = strip_tags( $new_instance['title'] );

		return $instance;
	}

	/**
	 * Show widget in backend
	 *
	 * @param array $instance
	 *
	 * @return string|void
	 */
	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? esc_attr( $instance['title'] ) : '';

		echo View::file( '/storefront/widget-customize.php' )->render( array(
			'title'      => $title,
			'field_id'   => $this->get_field_id( 'title' ),
			'field_name' => $this->get_field_name( 'title' )
		) );
	}
}