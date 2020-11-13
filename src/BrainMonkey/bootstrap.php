<?php
/**
 * PHPUnit bootstrap file for tests based on the BrainMonkey testing framework for WP.
 *
 * @package Yoast\WPTestUtils
 */

namespace Yoast\WPTestUtils\BrainMonkey;

/*
 * Make a number of commonly used WP constants available.
 */
\define( 'ABSPATH', true );

\define( 'MINUTE_IN_SECONDS', 60 );
\define( 'HOUR_IN_SECONDS', 3600 );
\define( 'DAY_IN_SECONDS', 86400 );
\define( 'WEEK_IN_SECONDS', 604800 );
\define( 'MONTH_IN_SECONDS', 2592000 );
\define( 'YEAR_IN_SECONDS', 31536000 );

\define( 'DB_HOST', 'nowhere' );
\define( 'DB_NAME', 'none' );
\define( 'DB_USER', 'nobody' );
\define( 'DB_PASSWORD', 'nothing' );

/*
 * Clear the opcache if it exists.
 *
 * Wrapped in a `function exists()` as the extension may not be enabled.
 */
if ( \function_exists( 'opcache_reset' ) ) {
	\opcache_reset();
}
