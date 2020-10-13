<?php

namespace Yoast\WPTestUtils\Tests\BrainMonkey;

use Brain\Monkey\Functions;
use Yoast\WPTestUtils\BrainMonkey\TestCase;

/**
 * Basic test for the BrainMonkey TestCase setup.
 *
 * @covers \Yoast\WPTestUtils\BrainMonkey\TestCase
 */
class TestCaseTest extends TestCase {

	/**
	 * Verify that the basic BrainMonkey functionality has been made available.
	 *
	 * @return void
	 */
	public function testSetUp() {
		\add_filter( 'testing', 'is_int' );
		$this->assertSame( 10, \has_filter( 'testing', 'is_int' ) );

		$this->assertFalse( \__return_false() );
	}

	/**
	 * Verify the BrainMonkey native translations function stubbing is available
	 * and that the functions return the input escaped.
	 *
	 * @return void
	 */
	public function testBrainMonkeyStubTranslationFunctions() {
		Functions\stubTranslationFunctions();

		$this->assertSame(
			'text &lt;i&gt;string&lt;/i&gt;',
			\esc_html__( 'text <i>string</i>', 'domain' )
		);
	}

	/**
	 * Verify the alternative translations function stubbing is available
	 * and that the functions return the input unchanged.
	 *
	 * @return void
	 */
	public function testStubTranslationFunctions() {
		$this->stubTranslationFunctions();

		$this->assertSame(
			'text <i>string</i>',
			\esc_html__( 'text <i>string</i>', 'domain' )
		);
	}

	/**
	 * Verify the alternative translations function stubbing for functions echo-ing output is available
	 * and that the functions echo out the input unchanged.
	 *
	 * @return void
	 */
	public function testStubTranslationFunctionsWithOutput() {
		$this->stubTranslationFunctions();

		$this->expectOutputString( 'foo' );

		\esc_html_e( 'foo', 'domain' );
	}

	/**
	 * Verify the BrainMonkey native escape function stubbing is available
	 * and that the functions return the input escaped.
	 *
	 * @return void
	 */
	public function testBrainMonkeyStubEscapeFunctions() {
		Functions\stubEscapeFunctions();

		$this->assertSame(
			'some &lt;div&gt;test&lt;/div&gt;',
			\esc_html( 'some <div>test</div>' )
		);
	}

	/**
	 * Verify the alternative escape function stubbing is available
	 * and that the functions return the input unchanged.
	 *
	 * @return void
	 */
	public function testStubEscapeFunctions() {
		$this->stubEscapeFunctions();

		$this->assertSame(
			'some <div>test</div>',
			\esc_html( 'some <div>test</div>' )
		);
	}
}
