<?php

namespace CleverReach\Newsletter\Utility;

use CleverReach\Newsletter\CleverReach;
use RuntimeException;

class View {
	const VIEW_FOLDER_PATH = '/resources/views';

	/**
	 * @var string
	 */
	private $file;

	/**
	 * View constructor.
	 *
	 * @param $file
	 */
	private function __construct( $file ) {
		$this->file = $file;
	}

	/**
	 * Returns view instance if the provided file exists
	 *
	 * @param $view_name
	 *
	 * @return View
	 */
	public static function file( $view_name ) {
		$file = CleverReach::get_plugin_dir_path() . self::VIEW_FOLDER_PATH . $view_name;
		if ( file_exists( $file ) ) {
			return new self( $file );
		}

		throw new RuntimeException( "Could not find specified view file: {$view_name} " );
	}

	/**
	 * Render page
	 *
	 * @param array $data
	 *
	 * @return false|string
	 * @noinspection PhpUnusedParameterInspection
	 */
	public function render( $data = array() ) {
		ob_start();

		require $this->file;

		return ob_get_clean();
	}
}