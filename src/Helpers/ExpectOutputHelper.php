<?php

namespace Yoast\WPTestUtils\Helpers;

/**
 * Helper to set output expectations.
 *
 * PHPUnit natively contains the `expectOutputString()` (exact string) and the
 * `expectOutputRegex()` (regex match) method, but sometimes you need a little
 * more flexibility.
 *
 * @since 0.2.0
 */
trait ExpectOutputHelper {

	/**
	 * Set an expectation that output will be generated and that the generated output contains a certain string.
	 *
	 * Important: in the same vein as the PHPUnit native `expectOutput*()` methods, only ONE call
	 * to this method per test is supported.
	 * If within one test, multiple calls to this method are made, only the last expectation will
	 * be tested!
	 * Refactor the test to use a data provider instead when a test needs to verify whether
	 * multiple substrings exist within the output.
	 *
	 * @param string $expected      The text which is expected to be a part of the output.
	 * @param bool   $ignoreEolDiff Optional. Whether or not to ignore differences in line endings
	 *                              (Linux vs Windows vs MacOS).
	 *                              Only different type and amount of line endings will be ignored,
	 *                              not that a line ending is expected.
	 *                              Defaults to true.
	 *
	 * @return void
	 */
	public function expectOutputContains( $expected, $ignoreEolDiff = true ) {
		$regex = '#' . \preg_quote( $expected, '#' ) . '#';
		if ( $ignoreEolDiff === true ) {
			// Meta: find line endings in the regex and replace them with regex syntax for line endings.
			$regex = \preg_replace( '`\R+`', '\R+', $regex );
		}

		$this->expectOutputRegex( $regex );
	}

	/**
	 * Normalize line endings in an arbitrary text string to Unix LF only.
	 *
	 * Helper method intended to be used in conjunction with the PHPUnit native `setOutputCallback()` method.
	 *
	 * @param string $output Output as caught by PHPUnit.
	 *
	 * @return string
	 */
	public function normalizeLineEndings( $output ) {
		return \preg_replace( '`\R`', "\n", $output );
	}
}
