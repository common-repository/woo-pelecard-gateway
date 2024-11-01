<?php

namespace Pelecardwc;

use Throwable;

class Log {

	const WC_LOG_FILENAME = 'wppc';

	const SENSITIVE_FIELDS = ['user', 'password', 'InvoiceUserName', 'InvoicePassword', 'pass'];

	public static $logger;

	public static function __callStatic( $method, $args ) {
		if ( ! class_exists( '\WC_Logger' ) ) {
			return false;
		}
		if ( empty( self::$logger ) ) {
			self::$logger = wc_get_logger();
		}
		list( $val, $context ) = array_merge( $args, [ null, null ] );
		if ( is_object( $val ) ) {
			//$val = json_encode( $val, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
			$val = json_encode( self::maskSensitiveData((array) $val), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE );
		} elseif ( is_array( $val ) ) {
			//$val = wc_print_r( $val, true );
			$val = wc_print_r( self::maskSensitiveData($val), true );
		}
		if ( empty( $val ) ) {
			return false;
		}
		$context = [ 'source' => $context ?: self::WC_LOG_FILENAME ];

		try {
			return self::$logger->$method( $val, $context );
		} catch ( Throwable $th ) {
			self::$logger->critical( $th->getMessage(), $context );
		}
	}

	private static function maskSensitiveData(array $data) {
        foreach ($data as $key => $value) {
            if (in_array($key, self::SENSITIVE_FIELDS)) {
                $data[$key] = '********';
            } elseif (is_array($value)) {
                $data[$key] = self::maskSensitiveData($value); // Рекурсивный вызов для вложенных массивов
            }
        }
        return $data;
    }
}
