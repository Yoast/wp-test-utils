<?php

namespace Yoast\WPTestUtils\Tests\Helpers\Fixtures;

use PHPUnit\Framework\TestCase;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;

/**
 * Fixture to test the expectOutputContains() method correctly fails when the output is not as expected.
 */
class IncorrectOutputMismatchedLineEndingAmountTestCase extends TestCase {

	use ExpectOutputHelper;

	/**
	 * Test resulting in a failure for output not matching the expectation based on mismatched
	 * amount of new lines.
	 *
	 * @return void
	 */
	public function test() {
		echo 'The quick brown fox

jumps over the lazy dog';

		// Real line ending is \n.
		$this->expectOutputContains( "fox\njumps", false );
	}
}
