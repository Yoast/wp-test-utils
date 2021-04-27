<?php

namespace Yoast\WPTestUtils\WPIntegration;

use PHPUnit\Runner\Version as PHPUnit_Version;

/**
 * Custom autoloader.
 *
 * Autoloader file for the PHPUnit 9 MockObject classes.
 *
 * Hack around PHPUnit < 9.3 mocking not being compatible with PHP 8.
 *
 * This allows for cross-version compatibility with various PHP, PHPUnit and WP versions.
 *
 * Use the `Yoast\WPTestUtils\WPIntegration\register_mockobject_autoloader()` function
 * as defined in the `src/WPIntegration/bootstrap-functions.php` file to register the autoloader.
 *
 * @since 0.2.0
 */
final class Autoload {

	/**
	 * A list of the classes this autoloader handles.
	 *
	 * @var string[] => true
	 */
	private static $supported_classes = [
		'PHPUnit\Framework\MockObject\Builder\NamespaceMatch'  => true,
		'PHPUnit\Framework\MockObject\Builder\ParametersMatch' => true,
		'PHPUnit\Framework\MockObject\InvocationMocker'        => true,
		'PHPUnit\Framework\MockObject\MockMethod'              => true,
	];

	/**
	 * Loads a class.
	 *
	 * @param string $class_name The name of the class to load.
	 *
	 * @return bool
	 */
	public static function load( $class_name ) {

		if ( \PHP_VERSION_ID < 80000 ) {
			// This autoloader is only needed when the tests are being run on PHP >= 8.0.
			// Let the standard Composer autoloader handle things otherwise.
			return false;
		}

		if ( \class_exists( PHPUnit_Version::class ) === false
			|| \version_compare( PHPUnit_Version::id(), '8.0.0', '>=' )
		) {
			// This autoloader is only needed when the tests are being run on PHPUnit < 8
			// and won't work with PHPUnit 5 anyway.
			return false;
		}

		if ( isset( self::$supported_classes[ $class_name ] ) === false ) {
			// Bow out, not a class this autoloader handles.
			return false;
		}

		$wp_test_dir = namespace\get_path_to_wp_test_dir();
		if ( $wp_test_dir === false ) {
			// We don't know where WP is installed...
			return false;
		}

		// Try getting the overloaded file as included in WP 5.6/master.
		$relative_filename = \strtr( \substr( $class_name, 18 ), '\\', \DIRECTORY_SEPARATOR ) . '.php';
		$file              = \realpath( $wp_test_dir . 'includes/phpunit7/' . $relative_filename );

		if ( $file === false || @\file_exists( $file ) === false ) {
			return false;
		}

		require_once $file;
		return true;
	}
}
