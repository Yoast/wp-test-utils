<?php

namespace Yoast\WPTestUtils\WPIntegration;

use PHPUnit\Runner\Version as PHPUnit_Version;
use WP_UnitTestCase;

/**
 * Custom autoloader.
 *
 * Autoloader file for the PHPUnit 9 MockObject classes as included in WP 5.6 - 5.8
 * and the WP Test Utils Integration test TestCase.
 *
 * - Hack around PHPUnit < 9.3 mocking not being compatible with PHP >= 8.
 * - Load the most appropriate TestCase depending on the features available in the WP version
 *   tests are being run against.
 *
 * This allows for cross-version compatibility with various PHP, PHPUnit and WP versions.
 *
 * Use the `Yoast\WPTestUtils\WPIntegration\register_mockobject_autoloader()` function
 * as defined in the `src/WPIntegration/bootstrap-functions.php` file to register the autoloader.
 *
 * @since 0.2.0
 * @since 1.0.0 Now also handles the loading of the WP Test Utils Integration TestCase.
 */
final class Autoload {

	/**
	 * A list of the classes this autoloader handles.
	 *
	 * @var array<string, true>
	 */
	private static $supported_classes = [
		'PHPUnit\\Framework\\MockObject\\Builder\\NamespaceMatch'  => true,
		'PHPUnit\\Framework\\MockObject\\Builder\\ParametersMatch' => true,
		'PHPUnit\\Framework\\MockObject\\InvocationMocker'         => true,
		'PHPUnit\\Framework\\MockObject\\MockMethod'               => true,
		'Yoast\\WPTestUtils\\WPIntegration\\TestCase'              => true,
	];

	/**
	 * Loads a class.
	 *
	 * @param string $class_name The name of the class to load.
	 *
	 * @return bool
	 */
	public static function load( $class_name ) {
		if ( isset( self::$supported_classes[ $class_name ] ) === false ) {
			// Bow out, not a class this autoloader handles.
			return false;
		}

		if ( \strpos( $class_name, 'PHPUnit' ) === 0 ) {
			return self::load_mockobject_class( $class_name );
		}

		return self::load_test_case();
	}

	/**
	 * Loads the PHPUnit 9.x mock object classes as included with WP 5.6 - 5.8
	 * when relevant.
	 *
	 * @param string $class_name The name of the class to load.
	 *
	 * @return bool
	 */
	private static function load_mockobject_class( $class_name ) {
		if ( \PHP_VERSION_ID < 80000 ) {
			// The mock object autoloading is only needed when the tests are being run on PHP >= 8.0.
			// Let the standard Composer autoloader handle things otherwise.
			return false;
		}

		if ( \class_exists( PHPUnit_Version::class ) === false
			|| \version_compare( PHPUnit_Version::id(), '8.0.0', '>=' )
		) {
			// The mock object autoloading is only needed when the tests are being run on PHPUnit < 8
			// and won't work with PHPUnit 5 anyway.
			return false;
		}

		$wp_test_dir = namespace\get_path_to_wp_test_dir();
		if ( $wp_test_dir === false ) {
			// We don't know where WP is installed...
			return false;
		}

		// Try getting the overloaded file as included in WP 5.6 - 5.8.
		$relative_filename = \strtr( \substr( $class_name, 18 ), '\\', \DIRECTORY_SEPARATOR ) . '.php';
		$file              = \realpath( $wp_test_dir . 'includes/phpunit7/' . $relative_filename );

		if ( $file === false || @\file_exists( $file ) === false ) {
			return false;
		}

		require_once $file;
		return true;
	}

	/**
	 * Loads the most appropriate test case depending on the WP version the tests are
	 * being run against.
	 *
	 * @return bool
	 */
	private static function load_test_case() {
		if ( \method_exists( WP_UnitTestCase::class, 'set_up' ) === false ) {
			// Older WP version from before the test changes.
			require_once __DIR__ . '/TestCase.php';
			return true;
		}

		if ( \method_exists( WP_UnitTestCase::class, 'assertObjectHasProperty' ) === false ) {
			// WP 5.2 - 5.8 version which includes the Polyfills and the fixture method wrappers,
			// but doesn't include the latest polyfill.
			require_once __DIR__ . '/TestCaseOnlyObjectPropertyPolyfill.php';
			return true;
		}

		// WP 5.9 or higher which automatically includes all Polyfills and the fixture method wrappers.
		require_once __DIR__ . '/TestCaseNoPolyfills.php';
		return true;
	}
}
