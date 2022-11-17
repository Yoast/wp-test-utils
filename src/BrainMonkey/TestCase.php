<?php

namespace Yoast\WPTestUtils\BrainMonkey;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Yoast\PHPUnitPolyfills\TestCases\TestCase as Polyfill_TestCase;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

/**
 * Basic test case for use with a BrainMonkey based test suite.
 */
abstract class TestCase extends Polyfill_TestCase {

	use ExpectOutputHelper;
	// Adds Mockery expectations to the PHPUnit assertions count.
	use MockeryPHPUnitIntegration;

	/**
	 * Regular expression to check if a given identifier name is valid for use in PHP.
	 *
	 * @var string
	 */
	const PHP_LABEL_REGEX = '`^[a-zA-Z_\x80-\xff][a-zA-Z0-9_\x80-\xff]*$`';

	/**
	 * Template for class double generation for a global class.
	 *
	 * @var string
	 */
	const TEMPLATE_GLOBAL_CLASS_DECLARATION = <<<'TPL'
class %s extends stdClass {}
TPL;

	/**
	 * Template for class double generation for a namespaced class.
	 *
	 * @var string
	 */
	const TEMPLATE_FQN_CLASS_DECLARATION = <<<'TPL'
namespace %s;
use stdClass;
class %s extends stdClass {}
TPL;

	/**
	 * Sets up test fixtures.
	 *
	 * @return void
	 */
	protected function set_up() {
		parent::set_up();
		Monkey\setUp();
	}

	/**
	 * Tears down test fixtures previously setup.
	 *
	 * @return void
	 */
	protected function tear_down() {
		Monkey\tearDown();
		parent::tear_down();
	}

	/**
	 * Stub the WP native escaping functions.
	 *
	 * The stubs created by this function return the original input string unchanged.
	 *
	 * Alternative to the BrainMonkey `Monkey\Functions\stubTranslationFunctions()` function
	 * which does apply some form of escaping to the input if the function called is a
	 * "translate and escape" function.
	 *
	 * @return void
	 */
	public function stubTranslationFunctions() {
		Functions\stubs(
			[
				'__'         => null,
				'_x'         => null,
				'_n'         => static function( $single, $plural, $number ) {
					return ( $number === 1 ) ? $single : $plural;
				},
				'_nx'        => static function( $single, $plural, $number ) {
					return ( $number === 1 ) ? $single : $plural;
				},
				'translate'  => null,
				'esc_html__' => null,
				'esc_html_x' => null,
				'esc_attr__' => null,
				'esc_attr_x' => null,
			]
		);

		Functions\when( '_e' )->echoArg();
		Functions\when( '_ex' )->echoArg();
		Functions\when( 'esc_html_e' )->echoArg();
		Functions\when( 'esc_attr_e' )->echoArg();
	}

	/**
	 * Stub the WP native escaping functions.
	 *
	 * The stubs created by this function return the original input string unchanged.
	 *
	 * Alternative to the BrainMonkey `Monkey\Functions\stubEscapeFunctions()` function
	 * which does apply some form of escaping to the input.
	 *
	 * @return void
	 */
	public function stubEscapeFunctions() {
		Functions\stubs(
			[
				'esc_js',
				'esc_sql',
				'esc_attr',
				'esc_html',
				'esc_textarea',
				'esc_url',
				'esc_url_raw',
				'esc_xml',
			]
		);
	}

	/**
	 * On the fly create a "fake" test double class, which allows for setting
	 * (dynamic) properties on it.
	 *
	 * This method is solely intended for classes which are unavailable during
	 * the test run.
	 *
	 * Typically a mock for an unavailable class is created using `Mockery::mock()`
	 * or `Mockery::mock( 'Unavailable' )`.
	 * When either the test or the code under test sets a property on such a mock,
	 * this will lead to a "Creation of dynamic properties is deprecated"
	 * notice on PHP >= 8.2, which can cause tests to error out.
	 *
	 * This method provides a work-around for this by on the fly creating a test double
	 * for the unavailable class which allows for setting dynamic properties.
	 *
	 * This method can be called during the test bootstrapping, in test `set_up()`
	 * methods or in the test itself (also see the linked helper functions).
	 *
	 * For setting expectations on the "fake" test double, use `Mockery::mock( 'FakedClass' )`.
	 *
	 * @see Yoast\WPTestUtils\BrainMonkey\makeDoublesForUnavailableClasses() Create one or more fake doubles during the test bootstrapping.
	 * @see Yoast\WPTestUtils\BrainMonkey\TestCase::makeDoublesForUnavailableClasses() Create one or more fake doubles in one go.
	 *
	 * @param string $class_name Name of the class to be "faked". This can be a fully qualified name.
	 *
	 * @return void
	 *
	 * @throws RuntimeException When an invalid class name is passed.
	 */
	public static function makeDoubleForUnavailableClass( $class_name ) {
		if ( \class_exists( $class_name ) === true ) {
			return;
		}

		// Remove potential leading backslash for fully qualified names.
		$class_name = \ltrim( $class_name, '\\' );
		if ( empty( $class_name ) ) {
			throw new RuntimeException( "Class name $class_name is not a valid name in PHP" );
		}

		$parts = \explode( '\\', $class_name );

		// Validate that each part of the name is valid.
		foreach ( $parts as $part ) {
			if ( \preg_match( self::PHP_LABEL_REGEX, $part ) !== 1 ) {
				throw new RuntimeException( "Class name $class_name is not a valid name in PHP" );
			}
		}

		$class_name = \array_pop( $parts );
		$code       = '';
		if ( empty( $parts ) ) {
			// No namespace.
			$code = \sprintf( self::TEMPLATE_GLOBAL_CLASS_DECLARATION, $class_name );
		}
		else {
			// FQN name.
			$namespace = \implode( '\\', $parts );
			$code      = \sprintf( self::TEMPLATE_FQN_CLASS_DECLARATION, $namespace, $class_name );
		}

		// phpcs:ignore Squiz.PHP.Eval.Discouraged -- No risk here, this is intentional (and only in non-production code).
		eval( $code );
	}

	/**
	 * On the fly create multiple "fake" test double classes which allow for setting
	 * (dynamic) properties on them.
	 *
	 * @see TestCase::makeDoubleForUnavailableClass()
	 *
	 * @param string[] $class_names List of class names to be "faked".
	 *
	 * @return void
	 */
	public static function makeDoublesForUnavailableClasses( array $class_names ) {
		foreach ( $class_names as $class_name ) {
			self::makeDoubleForUnavailableClass( $class_name );
		}
	}
}
