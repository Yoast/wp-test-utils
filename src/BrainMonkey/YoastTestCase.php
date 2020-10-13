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
				'is_multisite'         => false,
				'mysql2date'           => static function( $format, $date ) {
					return $date;
				},
				'number_format_i18n'   => null,
				'sanitize_text_field'  => null,
				'site_url'             => 'https://www.example.org',

				/*
				 * This stub can be removed once PR {@link https://github.com/Brain-WP/BrainMonkey/pull/86}
				 * has been merged and included in a new tagged release and the minimum supported version
				 * of the BrainMonkey package has been upped to that tagged release.
				 */
				'user_trailingslashit' => static function( $string ) {
					return \trailingslashit( $string );
				},

				/*
				 * This stub can be removed once PR {@link https://github.com/Brain-WP/BrainMonkey/pull/86}
				 * has been merged and included in a new tagged release and the minimum supported version
				 * of the BrainMonkey package has been upped to that tagged release.
				 */
				'wp_json_encode'       => static function( $data, $options = 0, $depth = 512 ) {
					return \json_encode( $data, $options, $depth );
				},
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
