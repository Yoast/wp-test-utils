<?php

namespace Yoast\WPTestUtils\Tests\BrainMonkey;

use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;

/**
 * Basic test for the Yoast BrainMonkey TestCase setup.
 *
 * @covers \Yoast\WPTestUtils\BrainMonkey\YoastTestCase
 */
final class YoastTestCaseTest extends YoastTestCase {

	/**
	 * Verify the behaviour of the `get_bloginfo()` stub.
	 *
	 * @dataProvider dataStubGetBlogInfo
	 *
	 * @param string $show     Value to pass to the function.
	 * @param string $expected Expected return value.
	 *
	 * @return void
	 */
	public function testStubGetBlogInfo( $show, $expected ) {
		$this->assertSame( $expected, \get_bloginfo( $show ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array<string, array<string, string>>
	 */
	public function dataStubGetBlogInfo() {
		return [
			// Explicit cases.
			'charset' => [
				'show'     => 'charset',
				'expected' => 'UTF-8',
			],
			'language' => [
				'show'     => 'language',
				'expected' => 'English',
			],
			// Default behaviour, return input unchanged.
			'name' => [
				'show'     => 'name',
				'expected' => 'name',
			],
			'pingback_url' => [
				'show'     => 'pingback_url',
				'expected' => 'pingback_url',
			],
		];
	}

	/**
	 * Verify the behaviour of the `is_multisite()` stub, which should always return
	 * false, except when the `WP_TESTS_MULTISITE` constant has been defined (which we're not testing).
	 *
	 * @return void
	 */
	public function testStubIsMultisite() {
		$this->assertFalse( \is_multisite() );
	}

	/**
	 * Verify the behaviour of the `mysql2date()` stub, which should ignore the $format parameter completely.
	 *
	 * @return void
	 */
	public function testStubMysql2Date() {
		$date = '2022-11-16 00:25:41';
		$this->assertSame( $date, \mysql2date( 'U', $date ) );
	}

	/**
	 * Verify the behaviour of the `number_format_i18n()` stub, which should return
	 * the first parameter passed unchanged.
	 *
	 * @return void
	 */
	public function testStubNumberFormatI18n() {
		$number = 123e7;
		$this->assertSame( $number, \number_format_i18n( $number, 5 ) );
	}

	/**
	 * Verify the behaviour of the `sanitize_text_field()` stub, which should return
	 * the first parameter passed unchanged.
	 *
	 * @return void
	 */
	public function testStubSanitizeTextField() {
		$text = 'some text < which <span> needs 	 sanitization    ';
		$this->assertSame( $text, \sanitize_text_field( $text ) );
	}

	/**
	 * Verify the behaviour of the `site_url()` stub, which should always return
	 * the same value regardless of the passed parameters.
	 *
	 * @return void
	 */
	public function testStubSiteUrl() {
		$this->assertSame( 'https://www.example.org', \site_url( 'some/path', 'rest' ) );
	}

	/**
	 * Verify the behaviour of the `wp_kses_post()` stub, which should return
	 * the first parameter passed unchanged.
	 *
	 * @return void
	 */
	public function testStubWpKsesPost() {
		$text = 'some text < which <span> needs 	 sanitization    ';
		$this->assertSame( $text, \wp_kses_post( $text ) );
	}

	/**
	 * Verify the behaviour of the `wp_parse_args()` stub, which should return
	 * a merged array.
	 *
	 * Note: not testing invalid input handling as these are function stubs to be used in tests,
	 * so invalid input throwing errors is exactly the right behaviour.
	 *
	 * @dataProvider dataStubWpParseArgs
	 *
	 * @param array<string, mixed> $settings Value for settings to pass to the function.
	 * @param array<string, mixed> $defaults Value for defaults to pass to the function.
	 * @param array<string, mixed> $expected Expected return value.
	 *
	 * @return void
	 */
	public function testStubWpParseArgs( $settings, $defaults, $expected ) {
		$this->assertSame( $expected, \wp_parse_args( $settings, $defaults ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array<string, array<string, array<string, mixed>>>
	 */
	public function dataStubWpParseArgs() {
		return [
			'two empty arrays' => [
				'settings' => [],
				'defaults' => [],
				'expected' => [],
			],
			'settings array empty, defaults array has values' => [
				'settings' => [
					'setting A' => true,
					'setting B' => 'string',
					'setting C' => 10,
				],
				'defaults' => [],
				'expected' => [
					'setting A' => true,
					'setting B' => 'string',
					'setting C' => 10,
				],
			],
			'settings array has values, defaults array empty' => [
				'settings' => [],
				'defaults' => [
					'setting A' => true,
					'setting B' => 'string',
					'setting C' => 10,
				],
				'expected' => [
					'setting A' => true,
					'setting B' => 'string',
					'setting C' => 10,
				],
			],
			'both arrays have values' => [
				'settings' => [
					'setting A' => true,
					'setting B' => 'string',
					'setting C' => 10,
					'setting E' => 'world!',
				],
				'defaults' => [
					'setting A' => false,
					'setting B' => 'default',
					'setting C' => 0,
					'setting D' => 'hello!',
				],
				'expected' => [
					'setting A' => true,
					'setting B' => 'string',
					'setting C' => 10,
					'setting D' => 'hello!',
					'setting E' => 'world!',
				],
			],
		];
	}

	/**
	 * Verify the behaviour of the `wp_strip_all_tags()` stub, which should return
	 * a merged array.
	 *
	 * Note: not testing invalid input handling as these are function stubs to be used in tests,
	 * so invalid input throwing errors is exactly the right behaviour.
	 *
	 * @dataProvider dataStubWpStripAllTags
	 *
	 * @param string $text          Text to strip tags from.
	 * @param bool   $remove_breaks Whether or not to strip line breaks and tabs.
	 * @param string $expected      Expected return value.
	 *
	 * @return void
	 */
	public function testStubWpStripAllTags( $text, $remove_breaks, $expected ) {
		$this->assertSame( $expected, \wp_strip_all_tags( $text, $remove_breaks ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array<string, array<string, string|bool>>
	 */
	public function dataStubWpStripAllTags() {
		return [
			'Empty string' => [
				'text'          => '',
				'remove_breaks' => true,
				'expected'      => '',
			],
			'Text containing script tag; no remove breaks' => [
				// phpcs:ignore WordPress.WP.EnqueuedResources.NonEnqueuedScript -- Not relevant.
				'text'          => '<script src="https://maliciousurl.com/">Click me
									</script>',
				'remove_breaks' => false,
				'expected'      => '',
			],
			'Text containing other HTML tags; no remove breaks' => [
				'text'          => '<span class="foo">my text</span>',
				'remove_breaks' => false,
				'expected'      => 'my text',
			],
			'Text surrounded by whitespace; no remove breaks' => [
				'text'          => '  	  my text
				',
				'remove_breaks' => false,
				'expected'      => 'my text',
			],
			'Text containing line breaks and tabs; no remove breaks' => [
				'text'          => 'my
					text
					and more',
				'remove_breaks' => false,
				'expected'      => 'my
					text
					and more',
			],
			'Text containing line breaks and tabs; with remove breaks' => [
				'text'          => 'my
					text
					and more',
				'remove_breaks' => true,
				'expected'      => 'my text and more',
			],
		];
	}

	/**
	 * Verify the behaviour of the `wp_slash()` stub, which should return
	 * the first parameter passed unchanged.
	 *
	 * @dataProvider dataStubWpSlash
	 *
	 * @param mixed $input Value to pass to the function.
	 *
	 * @return void
	 */
	public function testStubWpSlash( $input ) {
		$this->assertSame( $input, \wp_slash( $input ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function dataStubWpSlash() {
		return [
			'string' => [
				'input' => "O'Reilly?",
			],
			'array' => [
				'input' => [
					"O'Reilly?",
					"aa\'bb",
				],
			],
			'invalid input: null' => [
				'input' => null,
			],
		];
	}

	/**
	 * Verify the behaviour of the `wp_unslash()` stub, which should return
	 * the first parameter without slashes if a string or the first parameter
	 * unchanged if a non-string input was received.
	 *
	 * @dataProvider dataStubWpUnslash
	 *
	 * @param mixed $input    Value to pass to the function.
	 * @param mixed $expected Expected return value.
	 *
	 * @return void
	 */
	public function testStubWpUnslash( $input, $expected ) {
		$this->assertSame( $expected, \wp_unslash( $input ) );
	}

	/**
	 * Data provider.
	 *
	 * @return array<string, array<string, mixed>>
	 */
	public function dataStubWpUnslash() {
		return [
			'string' => [
				'input'    => "O\'Reilly\?",
				'expected' => "O'Reilly?",
			],
			'array' => [
				'input'    => [
					"O\'Reilly?",
					"aa\'bb",
				],
				'expected' => [
					"O\'Reilly?",
					"aa\'bb",
				],
			],
			'invalid input: null' => [
				'input'    => null,
				'expected' => null,
			],
		];
	}
}
