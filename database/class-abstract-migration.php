<?php

namespace CleverReach\Newsletter\Database;

use CleverReach\Newsletter\Database\Exceptions\Migration_Exception;
use wpdb;

abstract class Abstract_Migration {
	/**
	 * @var wpdb
	 */
	protected $db;

	/**
	 * Abstract_Migration constructor.
	 *
	 * @param wpdb $db
	 */
	public function __construct( $db ) {
		$this->db = $db;
	}

	/**
	 * Executes migration.
	 *
	 * @throws Migration_Exception
	 */
	abstract public function execute();
}