<?php

namespace Yoast\WPTestUtils\WPIntegration;

use WP_UnitTestCase;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

/**
 * Basic test case for use with a WP Integration test test suite.
 *
 * This test case extends the WordPress native base test case and
 * adds a limited set of test helper functions.
 *
 * The WordPress native base test case will include all relevant
 * polyfills to allow for using PHPUnit 9.x assertion and expectation syntax.
 *
 * This test case is suitable for use with:
 * - WP 5.2.13 and higher;
 * - WP 5.3.10 and higher;
 * - WP 5.4.8 and higher;
 * - WP 5.5.7 and higher;
 * - WP 5.6.6 and higher;
 * - WP 5.7.4 and higher;
 * - WP 5.8.1 and higher;
 * - WP 5.9.0 and higher.
 *
 * The included autoloader will automatically load the correct test case for
 * the WordPress version the tests are being run on.
 *
 * @since 1.0.0
 */
abstract class TestCase extends WP_UnitTestCase {

	use ExpectOutputHelper;

}
