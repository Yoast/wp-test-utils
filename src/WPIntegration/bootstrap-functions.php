<?php
/**
 * PHPUnit bootstrap file for tests based on the WP native testing framework.
 *
 * @package Yoast\WPTestUtils
 *
 * @since 0.2.0
 */

namespace Yoast\WPTestUtils\WPIntegration;

/**
 * Retrieves the path to the WordPress `tests/phpunit/` directory.
 *
 * The path will be determined based on the following, in this order:
 * - The `WP_TESTS_DIR` environment variable, if set.
 *   This environment variable can be set in the OS or via a custom `phpunit.xml` file
 *   and should point to the `tests/phpunit` directory of a WordPress clone.
 * - The `WP_DEVELOP_DIR` environment variable, if set.
 *   This environment variable can be set in the OS or via a custom `phpunit.xml` file
 *   and should point to the root directory of a WordPress clone.
 * - The plugin potentially being installed in a WordPress install.
 *   In that case, the plugin is expected to be in the `src/wp-content/plugin/plugin-name` directory.
 * - The plugin using a test setup as typically created by the WP-CLI scaffold command,
 *   which creates directories with the relevant test files in the system temp directory.
 *
 * Note: The path will be checked to make sure it is a valid path and actually points to
 * a directory containing the `includes/bootstrap.php` file.
 *
 * @since 1.0.0 Added fallback to typical WP-CLI scaffold command install directory.
 *
 * @return string|false Path to the WP `tests/phpunit/` directory (or similar) containing
 *                      the test bootstrap file. The path will include a trailing slash.
 *                      FALSE if the path couldn't be determined or if the path *could*
 *                      be determined, but doesn't exist.
 */
function get_path_to_wp_test_dir() {
	/**
	 * Normalizes all slashes in a file path to forward slashes.
	 *
	 * @param string $path File path.
	 *
	 * @return string The file path with normalized slashes.
	 */
	$normalize_path = static function( $path ) {
		return \str_replace( '\\', '/', $path );
	};

	if ( \getenv( 'WP_TESTS_DIR' ) !== false ) {
		$tests_dir = \getenv( 'WP_TESTS_DIR' );
		$tests_dir = \realpath( $tests_dir );
		if ( $tests_dir !== false ) {
			$tests_dir = $normalize_path( $tests_dir ) . '/';
			if ( \is_dir( $tests_dir ) === true
				&& @\file_exists( $tests_dir . 'includes/bootstrap.php' )
			) {
				return $tests_dir;
			}
		}

		unset( $tests_dir );
	}

	if ( \getenv( 'WP_DEVELOP_DIR' ) !== false ) {
		$dev_dir = \getenv( 'WP_DEVELOP_DIR' );
		$dev_dir = \realpath( $dev_dir );
		if ( $dev_dir !== false ) {
			$dev_dir = $normalize_path( $dev_dir ) . '/';
			if ( \is_dir( $dev_dir ) === true
				&& @\file_exists( $dev_dir . 'tests/phpunit/includes/bootstrap.php' )
			) {
				return $dev_dir . 'tests/phpunit/';
			}
		}

		unset( $dev_dir );
	}

	/*
	 * If neither of the constants was set, check whether the plugin is installed
	 * in `src/wp-content/plugins`. In that case, this file would be in
	 * `src/wp-content/plugins/plugin-name/vendor/yoast/wp-test-utils/src/WPIntegration`.
	 */
	if ( @\file_exists( __DIR__ . '/../../../../../../../../../tests/phpunit/includes/bootstrap.php' ) ) {
		$tests_dir = __DIR__ . '/../../../../../../../../../tests/phpunit/';
		$tests_dir = \realpath( $tests_dir );
		if ( $tests_dir !== false ) {
			return $normalize_path( $tests_dir ) . '/';
		}

		unset( $tests_dir );
	}

	/*
	 * Last resort: see if this is a typical WP-CLI scaffold command set-up where a subset of
	 * the WP test files have been put in the system temp directory.
	 */
	$tests_dir = \sys_get_temp_dir() . '/wordpress-tests-lib';
	$tests_dir = \realpath( $tests_dir );
	if ( $tests_dir !== false ) {
		$tests_dir = $normalize_path( $tests_dir ) . '/';
		if ( \is_dir( $tests_dir ) === true
			&& @\file_exists( $tests_dir . 'includes/bootstrap.php' )
		) {
			return $tests_dir;
		}
	}

	return false;
}

/**
 * Loads the WP native integration test bootstrap and register a custom autoloader.
 *
 * Use this function to make sure the autoloaders are registered in the correct order.
 *
 * @return void
 */
function bootstrap_it() {
	// Make sure the Composer autoload file has been generated.
	namespace\check_composer_autoload_exists();

	/*
	 * Load the PHPUnit Polyfills autoload file before bootstrapping WordPress for compatibility
	 * with the test changes per WP 5.9 (and backported to WP 5.2 - 5.8).
	 */
	require_once __DIR__ . '/../../../phpunit-polyfills/phpunitpolyfills-autoload.php';

	// Load WordPress.
	namespace\load_wp_test_bootstrap();

	/*
	 * Register the custom autoloader to overload the PHPUnit MockObject classes when running on PHP 8.
	 *
	 * This function has to be called _last_, after the WP test bootstrap to make sure it registers
	 * itself in FRONT of the Composer autoload (which also prepends itself to the autoload queue).
	 */
	namespace\register_mockobject_autoloader();
}

/**
 * Registers a custom autoload file to use the WP overload MockBuilder classes when
 * on PHP 8 combined with an incompatible PHPUnit version.
 *
 * @return void
 */
function register_mockobject_autoloader() {
	if ( \class_exists( Autoload::class ) === false ) {
		namespace\load_composer_autoload();
	}

	/*
	 * Register the autoloader and prepend it before any existing autoloaders
	 * to ensure that any PHPUnit overloaded files get loaded via this Autoloader,
	 * not via the Composer one.
	 */
	\spl_autoload_register( __NAMESPACE__ . '\Autoload::load', true, true );
}

/**
 * Loads the WordPress native test bootstrap file to set up the environment
 * for integration tests.
 *
 * @return void
 */
function load_wp_test_bootstrap() {
	$wp_test_path = namespace\get_path_to_wp_test_dir();

	if ( $wp_test_path !== false ) {
		// We can safely load the bootstrap file as the `get_path_to_wp_test_dir()` function
		// already verifies it exists.
		require_once $wp_test_path . 'includes/bootstrap.php';
		return;
	}

	echo \PHP_EOL, 'ERROR: The WordPress native unit test bootstrap file could not be found. Please set either the WP_TESTS_DIR or the WP_DEVELOP_DIR environment variable, either in your OS or in a custom phpunit.xml file.', \PHP_EOL;
	exit( 1 );
}

/**
 * Load the Composer autoload file of a plugin which uses this library as a dependency.
 *
 * @return void
 */
function load_composer_autoload() {
	namespace\check_composer_autoload_exists();
	require_once __DIR__ . '/../../../../autoload.php';
}

/**
 * Verifies whether the Composer autoload file exists for a plugin which uses this libary
 * as a dependency.
 *
 * @since 1.0.0 Also checks that the PHPUnit Polyfills autoload file exists, just to be sure.
 *
 * @return void
 */
function check_composer_autoload_exists() {
	if ( @\file_exists( __DIR__ . '/../../../../autoload.php' ) === false
		&& @\file_exists( __DIR__ . '/../../../phpunit-polyfills/phpunitpolyfills-autoload.php' ) === false
	) {
		echo \PHP_EOL, 'ERROR: Run `composer install` or `composer update -W` to install the dependencies',
			' and generate the autoload files before running the unit tests.', \PHP_EOL;
		exit( 1 );
	}
}
