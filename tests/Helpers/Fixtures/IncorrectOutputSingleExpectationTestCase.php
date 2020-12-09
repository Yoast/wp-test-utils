<?php

namespace Yoast\WPTestUtils\Tests\Helpers\Fixtures;

use PHPUnit\Framework\TestCase;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

/**
 * Fixture to test the expectOutputContains() method correctly fails when the output is not as expected.
 */
class IncorrectOutputSingleExpectationTestCase extends TestCase {

	use ExpectOutputHelper;

	/**
	 * Test resulting in a failure for output not matching the expectation.
	 *
	 * @return void
	 */
	public function test() {
		$this->expectOutputContains( 'brown dog' );

		echo 'The quick brown fox jumps over the lazy dog';
	}
}
