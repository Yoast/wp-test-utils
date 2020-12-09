<?php

namespace Yoast\WPTestUtils\Tests\Helpers;

use PHPUnit\Framework\TestCase;
use Yoast\PHPUnitPolyfills\Polyfills\AssertionRenames;
use Yoast\PHPUnitPolyfills\Polyfills\AssertStringContains;
use Yoast\WPTestUtils\Helpers\ExpectOutputHelper;
use Yoast\WPTestUtils\Tests\Helpers\Fixtures\IncorrectOutputMismatchedLineEndingAmountTestCase;
use Yoast\WPTestUtils\Tests\Helpers\Fixtures\IncorrectOutputMismatchedLineEndingsTestCase;
use Yoast\WPTestUtils\Tests\Helpers\Fixtures\IncorrectOutputShouldUseExpectOutputRegexTestCase;
use Yoast\WPTestUtils\Tests\Helpers\Fixtures\IncorrectOutputSingleExpectationTestCase;

/**
 * Test the helper methods in the ExpectOutputHelper trait.
 *
 * @covers \Yoast\WPTestUtils\Helpers\ExpectOutputHelper
 */
class ExpectOutputHelperTest extends TestCase {

	use AssertionRenames;
	use AssertStringContains;
	use ExpectOutputHelper;

	/**
	 * Verify that an exact output match satisfies expectations and that the placement
	 * of the output expectation is irrelevant.
	 *
	 * Note: in reality, for an exact match, the PHPUnit `expectOutputString()` method
	 * _should_ be used instead.
	 *
	 * @return void
	 */
	public function testExactMatchWithExpectationBeforeOutput() {
		$this->expectOutputContains( 'foobar' );
		echo 'foobar';
	}

	/**
	 * Verify that an exact output match satisfies expectations and that the placement
	 * of the output expectation is irrelevant.
	 *
	 * Note: in reality, for an exact match, the PHPUnit `expectOutputString()` method
	 * _should_ be used instead.
	 *
	 * @return void
	 */
	public function testExactMatchWithExpectationAfterOutput() {
		echo 'foobar';
		$this->expectOutputContains( 'foobar' );
	}

	/**
	 * Verify that an output containing the expected substring satisfies expectations.
	 *
	 * @return void
	 */
	public function testMatchingSingleSubstring() {
		$this->expectOutputContains( 'quick brown' );
		echo 'The quick brown fox jumps over the lazy dog';
	}

	/**
	 * Verify (and document) that an output containing multiple expected substrings only
	 * actually tests against the last set expectation.
	 *
	 * @return void
	 */
	public function testNOTMatchingMultipleSubstrings() {
		echo 'The quick brown fox jumps over the lazy dog';

		$this->expectOutputContains( 'brown dog' ); // Not failing.
		$this->expectOutputContains( 'lazy dog' );
	}

	/**
	 * Verify that the escaping of regex meta characters in the expected substring works correctly.
	 *
	 * @return void
	 */
	public function testMatchingSubstringContainingRegexMetachars() {
		echo 'This will match (me). Will it ? And \[not\] break on regex metachars';

		$this->expectOutputContains( 'match (me). Will it ? And \[' );
	}

	/**
	 * Verify that an output containing the expected substring, with the same line endings,
	 * satisfies expectations.
	 *
	 * @dataProvider dataMatchingSubstringsMismatchedLineEndings
	 *
	 * @param string $expected Expected output substring containing a line ending.
	 *
	 * @return void
	 */
	public function testMatchingSubstringsWithDifferentLineEndings( $expected ) {
		echo 'The quick brown fox
jumps over the lazy dog';

		$this->expectOutputContains( $expected );
	}

	/**
	 * Verify that an output containing the expected substring, though with a different type
	 * and amount of line endings, satisfies expectations.
	 *
	 * @dataProvider dataMatchingSubstringsMismatchedLineEndings
	 *
	 * @param string $expected Expected output substring containing a line ending.
	 *
	 * @return void
	 */
	public function testMatchingSubstringsWithMismatchedLineEndingsAmount( $expected ) {
		echo 'The quick brown fox


jumps over the lazy dog';

		$this->expectOutputContains( $expected, true );
	}

	/**
	 * Data provider.
	 *
	 * @return array
	 */
	public function dataMatchingSubstringsMismatchedLineEndings() {
		return [
			// Actual line ending type.
			[ "fox\njumps" ],

			// Mismatched line ending types.
			[ "fox\r\njumps" ],
			[ "fox\n\rjumps" ],
		];
	}

	/**
	 * Verify that a substring not being found in the output fails a test.
	 *
	 * @return void
	 */
	public function testFailingOnMismatchedExpectation() {
		$test   = new IncorrectOutputSingleExpectationTestCase( 'test' );
		$result = $test->run();

		$this->assertSame( 1, $result->failureCount() );
		$this->assertSame( 1, \count( $result ) );

		$failures = $result->failures();
		$this->assertStringContainsString(
			"Failed asserting that 'The quick brown fox jumps over the lazy dog' matches PCRE pattern \"#",
			$failures[0]->exceptionMessage()
		);
	}

	/**
	 * Verify that a regex being passed as an expectation will be treated as a literal substring.
	 *
	 * @return void
	 */
	public function testFailingOnUseOfRegexWhenNoRegexExpected() {
		$test   = new IncorrectOutputShouldUseExpectOutputRegexTestCase( 'test' );
		$result = $test->run();

		$this->assertSame( 1, $result->failureCount() );
		$this->assertSame( 1, \count( $result ) );

		$failures = $result->failures();
		$this->assertMatchesRegularExpression(
			"`^Failed asserting that '[^']+' matches PCRE pattern \"#`",
			$failures[0]->exceptionMessage()
		);
	}

	/**
	 * Verify that a mismatch between line endings in the expected substring and the
	 * actual output will fail the test when `$ignoreEolDiff` is set to `false`.
	 *
	 * @return void
	 */
	public function testSendingOutputWithMismatchedLineEndings() {
		$test   = new IncorrectOutputMismatchedLineEndingsTestCase( 'test' );
		$result = $test->run();

		$this->assertSame( 1, $result->failureCount() );
		$this->assertSame( 1, \count( $result ) );

		$failures = $result->failures();
		$this->assertMatchesRegularExpression(
			"`^Failed asserting that '[^']+' matches PCRE pattern \"#`",
			$failures[0]->exceptionMessage()
		);
	}

	/**
	 * Verify that a mismatch between the amount of line endings in the expected substring and the
	 * actual output will fail the test when `$ignoreEolDiff` is set to `false`.
	 *
	 * @return void
	 */
	public function testSendingOutputWithMismatchedLineEndingAmount() {
		$test   = new IncorrectOutputMismatchedLineEndingAmountTestCase( 'test' );
		$result = $test->run();

		$this->assertSame( 1, $result->failureCount() );
		$this->assertSame( 1, \count( $result ) );

		$failures = $result->failures();
		$this->assertMatchesRegularExpression(
			"`^Failed asserting that '[^']+' matches PCRE pattern \"#`",
			$failures[0]->exceptionMessage()
		);
	}

	/**
	 * Verify line endings are correctly normalized.
	 *
	 * @dataProvider dataNormalizeLineEndings
	 *
	 * @param string $input    Input text string.
	 * @param string $expected Expected function return value.
	 *
	 * @return void
	 */
	public function testNormalizeLineEndings( $input, $expected ) {
		$this->assertSame( $expected, $this->normalizeLineEndings( $input ) );
	}

	/**
	 * Data provider for the `testNormalizeLineEndings()` test.
	 *
	 * @return array
	 */
	public function dataNormalizeLineEndings() {
		return [
			'already-lf'            => [ "foo\n\nbar\n", "foo\n\nbar\n" ],
			'windows-crlf'          => [ "foo\r\n\r\nbar\r\n", "foo\n\nbar\n" ],
			'old-mac-cr-only'       => [ "foo\r\rbar\r", "foo\n\nbar\n" ],
			'reversed-manual-input' => [ "foo\n\r\n\rbar\n\r", "foo\n\n\nbar\n\n" ],
		];
	}
}
