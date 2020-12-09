<?php

namespace Yoast\WPTestUtils\BrainMonkey;

use Brain\Monkey;
use Brain\Monkey\Functions;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
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
}
