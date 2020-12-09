<?php

namespace Yoast\WPTestUtils\Tests\Helpers\Fixtures;

use PHPUnit\Framework\TestCase;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

/**
 * Fixture to test the expectOutputContains() method correctly fails when the output is not as expected.
 */
class IncorrectOutputShouldUseExpectOutputRegexTestCase extends TestCase {

	use ExpectOutputHelper;

	/**
	 * Test resulting in a failure for output not matching the expectation due to regex syntax
	 * in the substring, which is expected to be matched.
	 *
	 * Note: for a regex match, the PHPUnit `expectOutputRegex()` method should be used instead.
	 *
	 * @return void
	 */
	public function test() {
		echo 'This will match (me). Will it ? And \[not\] break on regex metachars';

		$this->expectOutputContains( 'match (me).+ And ' );
	}
}
