<?php

namespace CleverReach\Newsletter\Utility;

class Logger {
	/**
	 * Log levels
	 */
	const EMERGENCY = 'EMERGENCY';
	const ALERT = 'ALERT';
	const CRITICAL = 'CRITICAL';
	const ERROR = 'ERROR';
	const WARNING = 'WARNING';
	const NOTICE = 'NOTICE';
	const INFO = 'INFO';
	const DEBUG = 'DEBUG';

	/**
	 * @var Logger
	 */
	private static $instance;

	/**
	 * Gets logger instance
	 *
	 * @return Logger
	 */
	private static function get_instance() {
		if ( self::$instance === null ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Log info level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function info( $message, $context = array() ) {
		self::get_instance()->log( self::INFO, $message, $context );
	}

	/**
	 * Log debug level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function debug( $message, $context = array() ) {
		self::get_instance()->log( self::DEBUG, $message, $context );
	}

	/**
	 * Log error level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function error( $message, $context = array() ) {
		self::get_instance()->log( self::ERROR, $message, $context );
	}

	/**
	 * Log notice level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function notice( $message, $context = array() ) {
		self::get_instance()->log( self::NOTICE, $message, $context );
	}

	/**
	 * Log warning level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function warning( $message, $context = array() ) {
		self::get_instance()->log( self::WARNING, $message, $context );
	}

	/**
	 * Log alert level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function alert( $message, $context = array() ) {
		self::get_instance()->log( self::ALERT, $message, $context );
	}

	/**
	 * Log critical level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function critical( $message, $context = array() ) {
		self::get_instance()->log( self::CRITICAL, $message, $context );
	}

	/**
	 * Log emergency level message
	 *
	 * @param $message
	 * @param array $context
	 */
	public static function emergency( $message, $context = array() ) {
		self::get_instance()->log( self::EMERGENCY, $message, $context );
	}

	/**
	 * Log message
	 *
	 * @param $level
	 * @param $message
	 * @param array $context
	 */
	private function log( $level, $message, $context = array() ) {
		if ( ! empty( $context['trace'] ) ) {
			$message .= PHP_EOL . 'Stack trace: ' . PHP_EOL . $context['trace'];
		}

		$folder_name = self::get_log_folder();

		if ( ! file_exists( $folder_name ) ) {
			if ( ! mkdir( $folder_name, 0777, true ) && ! is_dir( $folder_name ) ) {
				throw new \RuntimeException( sprintf( 'Directory "%s" was not created', $folder_name ) );
			}

			$htaccess = fopen( $folder_name . '/.htaccess', 'a+' );
			if ( $htaccess ) {
				fwrite(
					$htaccess,
					'# Disabling log file access from outside
					<FilesMatch .*>
						<IfModule mod_authz_core.c>
							Require all denied
						</IfModule>
						<IfModule !mod_authz_core.c>
							Order allow,deny
							Deny from all
						</IfModule>
					</FilesMatch>
					
					Options -Indexes'
				);
				fclose( $htaccess );
			}
		}
		$log = fopen( $folder_name . '/cleverreach_' . date( 'Y_m_d', time() ) . '.log', 'a+' );
		if ( $log ) {
			fwrite(
				$log,
				"[$level][" . (string) date( 'Y-m-d H:i:s' ) . '] ' . $message . "\n"
			);
			fclose( $log );
		}
	}

	/**
	 * Get log folder
	 *
	 * @return string
	 */
	public static function get_log_folder() {
		$upload_dir = wp_upload_dir();

		return $upload_dir['basedir'] . '/cleverreach-newsletter-logs';
	}
}