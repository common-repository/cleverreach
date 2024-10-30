<?php

namespace CleverReach\Newsletter\Repository;

class Plugin_Options_Repository {
	/**
	 * Provides current schema version.
	 *
	 * @NOTICE default version is 2.2.2 if version has not been previously set.
	 * @return string
	 */
	public function get_schema_version() {
		return get_option( 'CR_SCHEMA_VERSION', '2.2.2' );
	}

	/**
	 * Sets schema version.
	 *
	 * @param string $version
	 */
	public function set_schema_version( $version ) {
		update_option( 'CR_SCHEMA_VERSION', $version );
	}

	/**
	 * Delete schema version.
	 */
	public function delete_schema_version() {
		delete_option( 'CR_SCHEMA_VERSION' );
	}

}