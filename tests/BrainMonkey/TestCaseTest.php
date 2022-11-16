<?php

namespace Yoast\WPTestUtils\Tests\BrainMonkey;

use Brain\Monkey\Functions;
use Mockery;
use ReflectionClass;
use UnavailableClassB;
use Yoast\WPTestUtils\BrainMonkey\Doubles\DummyTestDouble;
use Yoast\WPTestUtils\BrainMonkey\TestCase;
use Yoast\WPTestUtils\Tests\BrainMonkey\Fixtures\AnotherAvailableClass;
use Yoast\WPTestUtils\Tests\BrainMonkey\Fixtures\AvailableClass;

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

	/**
	 * Verify that creating a test double aliases for an unavailable class works as expected.
	 *
	 * @return void
	 */
	public function testMakeDoubleForUnavailableClass() {
		$this->assertFalse(
			\class_exists( 'UnavailableClassA' ),
			'Class UnavailableClassA appears to already exist'
		);

		$this->makeDoubleForUnavailableClass( 'UnavailableClassA' );

		$this->assertTrue(
			\class_exists( 'UnavailableClassA' ),
			'Class UnavailableClassA still doesn\'t appear to exist'
		);

		$reflection_class = new ReflectionClass( 'UnavailableClassA' );
		$this->assertSame(
			DummyTestDouble::class,
			$reflection_class->getName(),
			'Class UnavailableClassA is not an alias for the DummyTestDouble class'
		);
	}

	/**
	 * Verify that properties can be set on a test double created using the `makeDoubleForUnavailableClass()` method.
	 *
	 * @return void
	 */
	public function testDoubleForUnavailableClassAllowsSettingProperties() {
		$this->assertFalse(
			\class_exists( UnavailableClassB::class ),
			'Class UnavailableClassB appears to already exist'
		);

		static::makeDoubleForUnavailableClass( UnavailableClassB::class );

		$unavailable_class           = new UnavailableClassB();
		$unavailable_class->property = 10;

		$this->assertTrue(
			\property_exists( $unavailable_class, 'property' ),
			'Property does not exist on test double'
		);
		$this->assertSame(
			10,
			$unavailable_class->property,
			'Property value on test double does not match expected value'
		);
	}

	/**
	 * Verify that properties can be set on a mock of a test double, which was created
	 * using the `makeDoubleForUnavailableClass()` method.
	 *
	 * @return void
	 */
	public function testDoubleForUnavailableClassAllowsSettingPropertiesWhenMocked() {
		$this->assertFalse(
			\class_exists( 'UnavailableClassC' ),
			'Class UnavailableClassC appears to already exist'
		);

		self::makeDoubleForUnavailableClass( 'UnavailableClassC' );

		$mock_of_unavailable_class           = Mockery::mock( 'UnavailableClassC' );
		$mock_of_unavailable_class->property = 'test';

		$this->assertTrue(
			\property_exists( $mock_of_unavailable_class, 'property' ),
			'Property does not exist on mocked test double'
		);
		$this->assertSame(
			'test',
			$mock_of_unavailable_class->property,
			'Property value on mocked test double does not match expected value'
		);
	}

	/**
	 * Verify that no errors or warnings are thrown when a test double is requested for a class
	 * which already exists, but wasn't loaded prior to the `makeDoubleForUnavailableClass()` function being called.
	 *
	 * @return void
	 */
	public function testMakeDoubleForAvailableClassNotYetInMemoryDoesNotCreateDouble() {
		$this->assertFalse(
			\class_exists( AvailableClass::class, false ), // Don't autoload this class!
			'Class Yoast\WPTestUtils\Tests\BrainMonkey\Fixtures\AvailableClass already loaded, test is invalid'
		);

		$this->makeDoubleForUnavailableClass( AvailableClass::class );

		$reflection_class = new ReflectionClass( AvailableClass::class );
		$this->assertSame(
			AvailableClass::class,
			$reflection_class->getName(),
			'The class does not point to the original, available class'
		);
	}

	/**
	 * Verify that no errors or warnings are thrown when a test double is requested for a class
	 * which already exists and was already loaded prior to the `makeDoubleForUnavailableClass()` function being called.
	 *
	 * @return void
	 */
	public function testMakeDoubleForAvailableClassAlreadyInMemoryDoesNotCreateDouble() {
		// Don't load via autoloader.
		require_once __DIR__ . '/Fixtures/AnotherAvailableClass.php';

		$this->assertTrue(
			\class_exists( AnotherAvailableClass::class ),
			'Class Yoast\WPTestUtils\Tests\BrainMonkey\Fixtures\AnotherAvailableClass doesn\'t exist prior to this test'
		);

		$this->makeDoubleForUnavailableClass( AnotherAvailableClass::class );

		$reflection_class = new ReflectionClass( AnotherAvailableClass::class );
		$this->assertSame(
			AnotherAvailableClass::class,
			$reflection_class->getName(),
			'The class does not point to the original, available class'
		);
	}

	/**
	 * Verify that creating multiple test double aliases in one go works as expected.
	 *
	 * This test also safeguards that the functionality also works with namespaced class names.
	 *
	 * @return void
	 */
	public function testMakeDoublesForUnavailableClasses() {
		$classes = [
			'UnavailableClassX',
			'My\\Namespace\\UnavailableClassY',
			'Other\\UnavailableClassZ',
		];

		foreach ( $classes as $class_name ) {
			$this->assertFalse(
				\class_exists( $class_name ),
				"Class $class_name appears to already exist"
			);
		}

		self::makeDoublesForUnavailableClasses( $classes );

		foreach ( $classes as $class_name ) {
			$this->assertTrue(
				\class_exists( $class_name ),
				"Class $class_name still doesn't appear to exist"
			);

			$reflection_class = new ReflectionClass( $class_name );
			$this->assertSame(
				DummyTestDouble::class,
				$reflection_class->getName(),
				"Class $class_name is not an alias for the DummyTestDouble class"
			);
		}
	}
}
