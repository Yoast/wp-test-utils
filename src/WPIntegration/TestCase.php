<?php

namespace Yoast\WPTestUtils\WPIntegration;

use WP_UnitTestCase;
use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use Yoast\PHPUnitPolyfills\Polyfills\AssertClosedResource;
use Yoast\PHPUnitPolyfills\Polyfills\AssertEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertFileEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertionRenames;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;
use Yoast\PHPUnitPolyfills\Polyfills\AssertObjectEquals;
use Yoast\PHPUnitPolyfills\Polyfills\AssertObjectProperty;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;
use Yoast\PHPUnitPolyfills\Polyfills\EqualToSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectExceptionMessageMatches;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectExceptionObject;
use Yoast\PHPUnitPolyfills\Polyfills\ExpectPHPException;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

/**
 * Basic test case for use with a WP Integration test test suite.
 *
 * This test case extends the WordPress native base test case and makes
 * all relevant polyfills available to allow for using PHPUnit 9.x
 * assertion and expectation syntax.
 *
 * This test case is suitable for use with:
 * - WP < 5.2;
 * - WP 5.2.0 - 5.2.12;
 * - WP 5.3.0 - 5.3.9;
 * - WP 5.4.0 - 5.4.7;
 * - WP 5.5.0 - 5.5.6;
 * - WP 5.6.0 - 5.6.5;
 * - WP 5.7.0 - 5.7.3;
 * - WP 5.8.0 - 5.8.1.
 *
 * The included autoloader will automatically load the correct test case for
 * the WordPress version the tests are being run on.
 *
 * @since 0.2.0
 * @since 1.0.0 Added the snake_case wrapper methods, same as done in WP 5.2 - 5.8
 *              in the backports.
 */
abstract class TestCase extends WP_UnitTestCase {

	use AssertAttributeHelper;
	use AssertClosedResource;
	use AssertEqualsSpecializations;
	use AssertFileEqualsSpecializations;
	use AssertionRenames;
	use AssertIsType;
	use AssertObjectEquals;
	use AssertObjectProperty;
	use AssertStringContains;
	use EqualToSpecializations;
	use ExpectExceptionMessageMatches;
	use ExpectExceptionObject;
	use ExpectOutputHelper;
	use ExpectPHPException;

	/**
	 * Wrapper method for the `set_up_before_class()` method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	public static function setUpBeforeClass() {
		parent::setUpBeforeClass();
		static::set_up_before_class();
	}

	/**
	 * Wrapper method for the `tear_down_after_class()` method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	public static function tearDownAfterClass() {
		static::tear_down_after_class();
		parent::tearDownAfterClass();
	}

	/**
	 * Wrapper method for the `set_up()` method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	public function setUp() {
		parent::setUp();
		$this->set_up();
	}

	/**
	 * Wrapper method for the `tear_down()` method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	public function tearDown() {
		$this->tear_down();
		parent::tearDown();
	}

	/**
	 * Wrapper method for the `assert_pre_conditions()` method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	protected function assertPreConditions() {
		parent::assertPreConditions();
		$this->assert_pre_conditions();
	}

	/**
	 * Wrapper method for the `assert_post_conditions()` method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	protected function assertPostConditions() {
		parent::assertPostConditions();
		$this->assert_post_conditions();
	}

	/**
	 * Placeholder method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	public static function set_up_before_class() {}

	/**
	 * Placeholder method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	public static function tear_down_after_class() {}

	/**
	 * Placeholder method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	protected function set_up() {}

	/**
	 * Placeholder method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	protected function tear_down() {}

	/**
	 * Placeholder method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	protected function assert_pre_conditions() {}

	/**
	 * Placeholder method for forward-compatibility with WP 5.9.
	 *
	 * @return void
	 */
	protected function assert_post_conditions() {}
}
