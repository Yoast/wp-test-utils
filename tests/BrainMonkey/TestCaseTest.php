<?php

namespace Yoast\WPTestUtils\Tests\BrainMonkey;

use Brain\Monkey\Functions;
use Mockery;
use ReflectionClass;
use RuntimeException;
use stdClass;
use UnavailableClassB;
use WP_ClassA;
use WP_ClassB;
use Yoast\WPTestUtils\BrainMonkey\TestCase;
use Yoast\WPTestUtils\Tests\BrainMonkey\Fixtures\AnotherAvailableClass;
use Yoast\WPTestUtils\Tests\BrainMonkey\Fixtures\AvailableClass;

/**
 * Basic test for the BrainMonkey TestCase setup.
 *
 * @covers \Yoast\WPTestUtils\BrainMonkey\TestCase
 */
final class TestCaseTest extends TestCase {

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
	 * Verify the input dependent behaviour of the stub for the `_n()` function.
	 *
	 * @return void
	 */
	public function testStubTranslationFunctionsN() {
		$this->stubTranslationFunctions();

		$this->assertSame(
			'chair',
			\_n( 'chair', 'chairs', 1 ),
			'Function stub for _n() does not return singular when number is 1'
		);
		$this->assertSame(
			'chairs',
			\_n( 'chair', 'chairs', 10 ),
			'Function stub for _n() does not return plural when number is not 1'
		);
	}

	/**
	 * Verify the input dependent behaviour of the stub for the `_nx()` function.
	 *
	 * @return void
	 */
	public function testStubTranslationFunctionsNx() {
		$this->stubTranslationFunctions();

		$this->assertSame(
			'table',
			\_nx( 'table', 'tables', 1, 'test' ),
			'Function stub for _nx() does not return singular when number is 1'
		);
		$this->assertSame(
			'tables',
			\_nx( 'table', 'tables', 10, 'test' ),
			'Function stub for _nx() does not return plural when number is not 1'
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
	 * Verify that creating a test double for an unavailable class with a valid class name
	 * works as expected.
	 *
	 * @dataProvider dataMakeDoubleForUnavailableClass
	 *
	 * @param string $class_name The name of the desired class.
	 *
	 * @return void
	 */
	public function testMakeDoubleForUnavailableClass( $class_name ) {
		$this->assertFalse(
			\class_exists( $class_name ),
			"Class $class_name appears to already exist"
		);

		$this->makeDoubleForUnavailableClass( $class_name );

		$this->assertTrue(
			\class_exists( $class_name ),
			"Class $class_name still doesn't appear to exist"
		);

		$reflection_class = new ReflectionClass( $class_name );
		$parent_class     = $reflection_class->getParentClass();
		$this->assertSame(
			stdClass::class,
			$parent_class->getName(),
			"Class $class_name does not extend stdClass"
		);
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataMakeDoubleForUnavailableClass() {
		return [
			'Global class name'                        => [ 'GlobalClassName' ],
			'Global class name with leading backslash' => [ '\BackslashedClassName' ],
			'Partially qualified class name'           => [ 'My\\Namespaced\\ClassName' ],
			'Fully qualified class name'               => [ '\\Fully\\Qualified\\ClassName' ],
		];
	}

	/**
	 * Verify that creating a test double for an unavailable class throws an exception when
	 * the provided class name is not a valid name for a PHP namespace/class.
	 *
	 * @dataProvider dataMakeDoubleForUnavailableClassThrowsExceptionWithInvalidName
	 *
	 * @param string $class_name The name of the desired class.
	 *
	 * @return void
	 */
	public function testMakeDoubleForUnavailableClassThrowsExceptionWithInvalidName( $class_name ) {
		$this->assertFalse(
			\class_exists( $class_name ),
			"Class $class_name appears to already exist"
		);

		$this->expectException( RuntimeException::class );
		$this->expectExceptionMessage( \sprintf( 'Class name %s is not a valid name in PHP', \ltrim( $class_name, '\\' ) ) );

		$this->makeDoubleForUnavailableClass( $class_name );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataMakeDoubleForUnavailableClassThrowsExceptionWithInvalidName() {
		return [
			'Empty string as class name'                       => [ '' ],
			'Only backslashes'                                 => [ '\\\\\\' ],
			'Global class name - invalid name'                 => [ 'Class**Name' ],
			'Fully qualified class name - invalid name start'  => [ '\\My*\\ClassName' ],
			'Fully qualified class name - invalid name middle' => [ '\\My\2ndNS\\ClassName' ],
		];
	}

	/**
	 * Verify that two different test doubles created using the `makeDoubleForUnavailableClass()` method
	 * do not identify as the same class.
	 *
	 * @return void
	 */
	public function testDoublesDoNotIdentifyAsSameClass() {
		$classes = [
			WP_ClassA::class,
			WP_ClassB::class,
		];

		foreach ( $classes as $class_name ) {
			$this->assertFalse(
				\class_exists( $class_name ),
				"Class $class_name appears to already exist"
			);
		}

		self::makeDoublesForUnavailableClasses( $classes );

		$mock_a = Mockery::mock( WP_ClassA::class );
		$mock_b = Mockery::mock( WP_ClassB::class );
		$this->assertFalse( $mock_a instanceof $mock_b, 'Mock of WP_ClassA identifies as an instance of WP_ClassB' );
		$this->assertFalse( $mock_b instanceof $mock_a, 'Mock of WP_ClassB identifies as an instance of WP_ClassA' );

		$instance_a = new WP_ClassA();
		$instance_b = new WP_ClassB();
		$this->assertFalse( $instance_a instanceof $instance_b, 'WP_ClassA identifies as an instance of WP_ClassB' );
		$this->assertFalse( $instance_b instanceof $instance_a, 'WP_ClassB identifies as an instance of WP_ClassA' );
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

		self::makeDoubleForUnavailableClass( UnavailableClassB::class );

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
			'My\\Namespaced\\UnavailableClassY',
			'\\Other\\UnavailableClassZ',
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
			$parent_class     = $reflection_class->getParentClass();
			$this->assertSame(
				stdClass::class,
				$parent_class->getName(),
				"Class $class_name does not extend stdClass"
			);
		}
	}
}
