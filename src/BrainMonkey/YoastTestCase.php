<?php

namespace Yoast\WPTestUtils\BrainMonkey;

use Brain\Monkey;

/**
 * Test case for the Yoast plugins for use with a BrainMonkey based test suite.
 */
abstract class YoastTestCase extends TestCase {

	/**
	 * Sets up test fixtures and function stubs.
	 *
	 * @return void
	 */
	protected function set_up() {
		parent::set_up();

		/*
		 * Create select additional function stubs.
		 * Null makes it so the function returns its first argument.
		 */
		Monkey\Functions\stubs(
			[
				// Passing "null" makes the function return it's first argument.
				'get_bloginfo'         => static function( $show ) {
					switch ( $show ) {
						case 'charset':
							return 'UTF-8';
						case 'language':
							return 'English';
					}

					return $show;
				},
				'is_multisite'         => static function() {
					if ( \defined( 'WP_TESTS_MULTISITE' ) ) {
						return (bool) \WP_TESTS_MULTISITE;
					}

					return false;
				},
				'mysql2date'           => static function( $format, $date ) {
					return $date;
				},
				'number_format_i18n'   => null,
				'sanitize_text_field'  => null,
				'site_url'             => 'https://www.example.org',
				'wp_kses_post'         => null,
				'wp_parse_args'        => static function ( $settings, $defaults ) {
					return \array_merge( $defaults, $settings );
				},
				'wp_strip_all_tags'    => static function( $string, $remove_breaks = false ) {
					$string = \preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
					$string = \strip_tags( $string );

					if ( $remove_breaks ) {
						$string = \preg_replace( '/[\r\n\t ]+/', ' ', $string );
					}

					return \trim( $string );
				},
				'wp_slash'             => null,
				'wp_unslash'           => static function( $value ) {
					return \is_string( $value ) ? \stripslashes( $value ) : $value;
				},
			]
		);
	}
}
