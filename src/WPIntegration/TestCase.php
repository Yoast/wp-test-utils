<?php

namespace Yoast\WPTestUtils\WPIntegration;

use WP_UnitTestCase;
use Yoast\PHPUnitPolyfills\Helpers\AssertAttributeHelper;
use Yoast\PHPUnitPolyfills\Polyfills\AssertEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertFileEqualsSpecializations;
use Yoast\PHPUnitPolyfills\Polyfills\AssertionRenames;
use Yoast\PHPUnitPolyfills\Polyfills\AssertIsType;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;
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
 * @since 0.2.0
 */
abstract class TestCase extends WP_UnitTestCase {

	use AssertAttributeHelper;
	use AssertEqualsSpecializations;
	use AssertFileEqualsSpecializations;
	use AssertionRenames;
	use AssertIsType;
	use AssertStringContains;
	use ExpectExceptionMessageMatches;
	use ExpectExceptionObject;
	use ExpectOutputHelper;
	use ExpectPHPException;

}
