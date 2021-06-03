WP Test Utils
=====================================================

[![Version](https://poser.pugx.org/yoast/wp-test-utils/version)](https://packagist.org/packages/yoast/wp-test-utils)
[![CS Build Status](https://github.com/Yoast/wp-test-utils/actions/workflows/cs.yml/badge.svg)](https://github.com/Yoast/wp-test-utils/actions/workflows/cs.yml)
[![Test Build Status](https://github.com/Yoast/wp-test-utils/actions/workflows/test.yml/badge.svg)](https://github.com/Yoast/wp-test-utils/actions/workflows/test.yml)
[![Minimum PHP Version](https://img.shields.io/packagist/php-v/yoast/wp-test-utils.svg?maxAge=3600)](https://packagist.org/packages/yoast/wp-test-utils)
[![License: BSD3](https://poser.pugx.org/yoast/wp-test-utils/license)](https://github.com/Yoast/wp-test-utils/blob/master/LICENSE)

This library contains a set of utilities for running automated tests for WordPress plugins and themes.

* [Requirements](#requirements)
* [Installation](#installation)
* [Features](#features)
    - [Utilities for running tests using BrainMonkey](#utilities-for-running-tests-using-brainmonkey)
    - [Utilities for running integration tests with WordPress](#utilities-for-running-integration-tests-with-wordpress)
    - [Test Helpers](#test-helpers)
* [Contributing](#contributing)
* [License](#license)


Requirements
-------------------------------------------

* PHP 5.6 or higher.

The following packages will be automatically required via Composer:
* [PHPUnit Polyfills] 0.2.0 or higher.
* [PHPUnit] 5.7 - 9.x.
* [BrainMonkey] 2.5 or higher.

Installation
-------------------------------------------

To install this package, run:
```bash
composer require --dev yoast/wp-test-utils
```

To update this package, run:
```bash
composer update --dev yoast/wp-test-utils --with-dependencies
```

Features
-------------------------------------------

This library contains a set of utilities for running automated tests for WordPress plugins and themes.

### Utilities for running tests using BrainMonkey

#### Basic `TestCase` for use with BrainMonkey

Features of this `TestCase`:
1. Cross-version compatibility with PHPUnit 5.7 - 9.x via the [PHPUnit Polyfills] package.
2. The BrainMonkey and Mockery set up and tear down is already handled.
3. Tests using Mockery expectations will not be marked as "risky", even when there are no assertions.
4. Makes alternative implementations of the BrainMonkey native [`stubTranslationFunctions()`](https://giuseppe-mazzapica.gitbook.io/brain-monkey/functions-testing-tools/function-stubs#pre-defined-stubs-for-translation-functions) and [`stubEscapeFunctions()`](https://giuseppe-mazzapica.gitbook.io/brain-monkey/functions-testing-tools/function-stubs#pre-defined-stubs-for-escaping-functions) functions available.
    The BrainMonkey native functions create stubs which will apply basic HTML escaping if the stubbed function is an escaping function, like `esc_html__()`.
    The alternative implementations of these functions will create stubs which will return the original value without change. This makes creating tests easier as the `$expected` value does not need to account for the HTML escaping.
    _Note: the alternative implementation should be used selectively._
5. Helper functions for setting expectations for generated output.

Implementation example:
```php
<?php
namespace PackageName\Tests;

use Yoast\WPTestUtils\BrainMonkey\TestCase;

class FooTest extends TestCase {
    protected function set_up() {
        parent::set_up();
        // Your own additional setup.
    }

    protected function tear_down() {
        // Your own additional tear down.
        parent::tear_down();
    }
    
    public function testAFunctionContainingStringTranslating() {
        $this->stubTranslationFunctions(); // No HTML escaping will be applied.
        // Or:
        \Brain\Monkey\Functions\stubTranslationFunctions(); // HTML escaping will be applied.

        // Test your code.
    }

    public function testAFunctionContainingOutputEscaping() {
        $this->stubEscapeFunctions(); // No HTML escaping will be applied.
        // Or:
        \Brain\Monkey\Functions\stubEscapeFunctions(); // HTML escaping will be applied.

        // Test your code.
    }
}
```


#### Yoast TestCase for use with BrainMonkey

Features of this TestCase:
1. All the benefits of the basic TestCase as outlined above.
2. By default, the following WordPress functions will be stubbed, in addition to [the stubs already provided by BrainMonkey](https://giuseppe-mazzapica.gitbook.io/brain-monkey/wordpress-specific-tools/wordpress-tools):

    | WP function                                            | Stub will return                                                                      | Notes                                                                    |
    |--------------------------------------------------------|---------------------------------------------------------------------------------------|--------------------------------------------------------------------------|
    | `get_bloginfo( 'charset' )`                            | `'UTF-8'`                                                                             |                                                                          |
    | `get_bloginfo( 'language' )`                           | `'English'`                                                                           |                                                                          |
    | `is_multisite()`                                       | `false`                                                                               |                                                                          |
    | `mysql2date( $format, $date )`                         | `$date` (original value)                                                              |                                                                          |
    | `number_format_i18n( $number, $decimals )`             | `$number` (original value)                                                            |                                                                          |
    | `sanitize_text_field( $str )`                          | `$str` (original value)                                                               |                                                                          |
    | `site_url()`                                           | `'https://www.example.org'`                                                           |                                                                          |
    | `wp_kses_post( $data )`                                | `$data` (original value)                                                              |                                                                          |
    | `wp_parse_args( $args, $defaults )`                    | `array_merge( $defaults, $args )`                                                     |                                                                          |
    | `wp_strip_all_tags( $string, $remove_breaks = false )` | emulated return value as per the WP native functionality, might miss some edge cases  |                                                                          |
    | `wp_slash( $value )`                                   | `$value` (original value)                                                             |                                                                          |
    | `wp_unslash( $value )`                                 | `stripslashes( $value )` if `$value` is a string, otherwise `$value` (original value) |                                                                          |

Implementation example:
```php
<?php
namespace PackageName\Tests;

use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;

class FooTest extends YoastTestCase {
    // Your test code.
}
```

#### Bootstrap file for use with BrainMonkey

Most of the time, using the Composer `vendor/autoload.php` file as your bootstrap file for PHPUnit will be sufficient.

However, in the context of testing WordPress plugins and themes, it can be useful to have access to certain constants which WordPress natively declares.

The bootstrap file for use with BrainMonkey will make sure that the following constants are defined:
|                   |                     |                    |
|-------------------|---------------------|--------------------|
| `ABSPATH`         | `MINUTE_IN_SECONDS` | `HOUR_IN_SECONDS`  |
| `DAY_IN_SECONDS`  | `WEEK_IN_SECONDS`   | `MONTH_IN_SECONDS` |
| `YEAR_IN_SECONDS` | `DB_HOST`           | `DB_NAME`          |
| `DB_USER`         | `DB_PASSWORD`       |                    |

In addition to that, it will clear the PHP Opcache before running the tests.

Implementation example:
```php
<?php
// File: tests/bootstrap.php
require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/BrainMonkey/bootstrap.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';
```

To tell PHPUnit to use this bootstrap file, use `--bootstrap tests/bootstrap.php` on the command-line or add the `bootstrap="tests/bootstrap.php"` attribute to your `phpunit.xml.dist` file.


### Utilities for running integration tests with WordPress

#### Basic `TestCase` for WordPress integration tests

Features of this `TestCase`:
1. Extends the WP native base test case `WP_UnitTestCase`, making all the WP Core test utilities available to your integration test classes.
2. Cross-version compatibility with PHPUnit 5.7 - 9.x via the [PHPUnit Polyfills] package.
    _Note: WordPress Core limit tests to running on PHPUnit 7.5 max. However, using these polyfill you can already start using the up-to-date PHPUnit 9.x syntax, even though the tests don't use PHPUnit 9 yet._
3. Helper functions for setting expectations for generated output.

Implementation example:
```php
<?php
namespace PackageName\Tests;

use Yoast\WPTestUtils\WPIntegration\TestCase;

class FooTest extends TestCase {
    protected function setUp() {
        parent::setUp();
        // Your own additional setup.
    }
}
```

#### Bootstrap utility functions and custom autoloader

In most cases, for WordPress integration tests, the WordPress Core native test bootstrap file will be loaded to set up the test environment, including the database.
However, WordPress has a hard limit on PHPUnit 7.5 max, while PHPUnit 9.3 is the first PHPUnit version which has full PHP 8.0 support, making testing on PHP 8.0 problematic.

In WordPress Core, this problem has been solved by adding copies of select PHPUnit 9.x files to the WordPress test suite and loading those files when running PHPUnit 7.x on PHP 8.0 instead of the PHPUnit 7 native ones.
The way this solution has been implemented, however, is not portable to plugins.

In comes WP Test Utils... which offers:
* A `bootstrap-functions.php` file with utility functions for use in the test `bootstrap` file for the integration tests for a plugin.
* A custom autoloader which will selectively autoload the WP copies of the PHPUnit 9 native MockBuilder files for PHPUnit < 9 when run on PHP 8 to get round the use of the new reserved keyword `match` as was used in older versions of these files.

The functionality within these files presumes three things:
1. This package is installed as a dependency of a plugin via Composer and will be in the `vendor/yoast/wp-test-utils/` directory.
2. WordPress itself is available in its entirety, inclusing the `tests` directory.
3. Either the `WP_TESTS_DIR` environment variable (path to the WordPress Core `./tests/phpunit` directory) or the `WP_DEVELOP_DIR` environment variable (path to the WordPress Core root directory) will be set.
    These environment variables can be set on the OS level or from within a `phpunit.xml[.dist]` file.
    If neither of the environment variables is available, the plugin is presumed to be installed in a `src/wp-content/plugins/plugin-name` directory, with this package in the `src/wp-content/plugins/plugin-name/vendor/yoast/wp-test-utils` directory.

This is in line with a typical integration test setup in the context of WordPress.

Implementation example for how this functionality would typically be used in the bootstrap file of a WordPress plugin:
```php
use Yoast\WPTestUtils\WPIntegration;

if ( getenv( 'WP_PLUGIN_DIR' ) !== false ) {
    define( 'WP_PLUGIN_DIR', getenv( 'WP_PLUGIN_DIR' ) );
}

$GLOBALS['wp_tests_options'] = [
    'active_plugins' => [ 'plugin-name/main-file.php' ],
];

require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

/*
 * Load WordPress, which will load the Composer autoload file, and load the MockObject autoloader after that.
 */
WPIntegration\bootstrap_it();

if ( ! defined( 'WP_PLUGIN_DIR' ) || file_exists( WP_PLUGIN_DIR . '/plugin-name/main-file.php' ) === false ) {
    echo PHP_EOL, 'ERROR: Please check whether the WP_PLUGIN_DIR environment variable is set and set to the correct value. The unit test suite won\'t be able to run without it.', PHP_EOL;
    exit( 1 );
}
```


### Test Helpers

#### `Yoast\WPTestUtils\Helpers\EscapeOutputHelper` trait

PHPUnit natively contains the `expectOutputString()` (exact string) and the `expectOutputRegex()` (regex match) method, but sometimes you need a little more flexibility.

A typical pattern used is to check whether the output generated contains certain substrings.
And a typical reason for mismatched output versus expectation, is a mismatch in line endings.

This `EscapeOutputHelper` trait adds the following functions to solve these issues:
* `expectOutputContains( $expected, $ignoreEolDiff = true )` to verify whether generated output contains a certain substring.
    In the same vein as the PHPUnit native methods, only one such expectation can be set in a test.
    If a test needs to check that the generated output contains multiple different substrings, refactor the test to use a data provider feeding the test one substring at a time.
* `normalizeLineEndings( $output )` which is intended to be used in conjunction with the PHPUnit native `setOutputCallback()` method to normalize line endings in the actual output before comparing output expectations with the actual output.

:point_right: This trait is automatically available to test classes based on the BrainMonkey or WPIntegration `TestCase`s as included in this package.


Contributing
-------
Contributions to this project are welcome. Clone the repo, branch off from `develop`, make your changes, commit them and send in a pull request.

If you are unsure whether the changes you are proposing would be welcome, please open an issue first to discuss your proposal.


License
-------
This code is released under the [BSD-3-Clause License](https://opensource.org/licenses/BSD-3-Clause).


[PHPUnit Polyfills]: https://packagist.org/packages/yoast/phpunit-polyfills
[PHPUnit]:           https://packagist.org/packages/phpunit/phpunit
[BrainMonkey]:       https://packagist.org/packages/brain/monkey
