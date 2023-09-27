WP Test Utils
=====================================================

[![Version](https://poser.pugx.org/yoast/wp-test-utils/version)](https://packagist.org/packages/yoast/wp-test-utils)
[![CS Build Status](https://github.com/Yoast/wp-test-utils/actions/workflows/cs.yml/badge.svg)](https://github.com/Yoast/wp-test-utils/actions/workflows/cs.yml)
[![Test Build Status](https://github.com/Yoast/wp-test-utils/actions/workflows/test.yml/badge.svg)](https://github.com/Yoast/wp-test-utils/actions/workflows/test.yml)
[![Coverage Status](https://coveralls.io/repos/github/Yoast/wp-test-utils/badge.svg?branch=develop)](https://coveralls.io/github/Yoast/wp-test-utils?branch=develop)

[![Minimum PHP Version](https://img.shields.io/packagist/php-v/yoast/wp-test-utils.svg?maxAge=3600)](https://packagist.org/packages/yoast/wp-test-utils)
[![License: BSD3](https://poser.pugx.org/yoast/wp-test-utils/license)](https://github.com/Yoast/wp-test-utils/blob/main/LICENSE)

This library contains a set of utilities for running automated tests for WordPress plugins and themes.

* [Requirements](#requirements)
* [Installation](#installation)
* [Features](#features)
    - [Utilities for running tests using BrainMonkey](#utilities-for-running-tests-using-brainmonkey)
        - [Basic `TestCase` for use with BrainMonkey](#basic-testcase-for-use-with-brainmonkey)
        - [Yoast TestCase for use with BrainMonkey](#yoast-testcase-for-use-with-brainmonkey)
        - [Bootstrap file for use with BrainMonkey](#bootstrap-file-for-use-with-brainmonkey)
        - [Helpers to create test doubles for unavailable classes](#helpers-to-create-test-doubles-for-unavailable-classes)
    - [Utilities for running integration tests with WordPress](#utilities-for-running-integration-tests-with-wordpress)
        - [What these utilities solve](#what-these-utilities-solve)
        - [Basic `TestCase` for WordPress integration tests](#basic-testcase-for-wordpress-integration-tests)
        - [Bootstrap utility functions and custom autoloader](#bootstrap-utility-functions-and-custom-autoloader)
    - [Test Helpers](#test-helpers)
        - [`Yoast\WPTestUtils\Helpers\EscapeOutputHelper` trait](#yoastwptestutilshelpersescapeoutputhelper-trait)
* [Contributing](#contributing)
* [License](#license)


Requirements
-------------------------------------------

* PHP 5.6 or higher.

The following packages will be automatically required via Composer:
* [PHPUnit Polyfills] 1.1.0 or higher.
* [PHPUnit] 5.7 - 9.x.
* [BrainMonkey] 2.6.1 or higher.


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
4. Makes alternative implementations of the BrainMonkey native [`stubTranslationFunctions()`](https://giuseppe-mazzapica.gitbook.io/brain-monkey/functions-testing-tools/function-stubs#pre-defined-stubs-for-translation-functions) and [`stubEscapeFunctions()`](https://giuseppe-mazzapica.gitbook.io/brain-monkey/functions-testing-tools/function-stubs#pre-defined-stubs-for-escaping-functions) functions available.<br/>
    The BrainMonkey native functions create stubs which will apply basic HTML escaping if the stubbed function is an escaping function, like `esc_html__()`.<br/>
    The alternative implementations of these functions will create stubs which will return the original value without change. This makes creating tests easier as the `$expected` value does not need to account for the HTML escaping.<br/>
    _Note: the alternative implementation should be used selectively._
5. Helper functions for [setting expectations for generated output](#yoastwptestutilshelpersescapeoutputhelper-trait).
6. Helper functions for [creating "dummy" test doubles for unavailable classes](#helpers-to-create-test-doubles-for-unavailable-classes).

**_Implementation example:_**
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

    | WP function                                            | Stub will return                                                                      |
    |--------------------------------------------------------|---------------------------------------------------------------------------------------|
    | `get_bloginfo( 'charset' )`                            | `'UTF-8'`                                                                             |
    | `get_bloginfo( 'language' )`                           | `'English'`                                                                           |
    | `is_multisite()`                                       | Value of the `WP_TESTS_MULTISITE` PHP constant as a boolean (if defined), otherwise `false` |                                                                          |
    | `mysql2date( $format, $date )`                         | `$date` (original value)                                                              |
    | `number_format_i18n( $number, $decimals )`             | `$number` (original value)                                                            |
    | `sanitize_text_field( $str )`                          | `$str` (original value)                                                               |
    | `site_url()`                                           | `'https://www.example.org'`                                                           |
    | `wp_kses_post( $data )`                                | `$data` (original value)                                                              |
    | `wp_parse_args( $args, $defaults )`                    | `array_merge( $defaults, $args )`                                                     |
    | `wp_strip_all_tags( $string, $remove_breaks = false )` | Emulated return value as per the WP native functionality, might miss some edge cases  |
    | `wp_slash( $value )`                                   | `$value` (original value)                                                             |
    | `wp_unslash( $value )`                                 | `stripslashes( $value )` if `$value` is a string, otherwise `$value` (original value) |


**_Implementation example:_**
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
|                   |                     |                   |                  |
|-------------------|---------------------|-------------------|------------------|
| `ABSPATH`         | `MINUTE_IN_SECONDS` | `HOUR_IN_SECONDS` | `DAY_IN_SECONDS` |
| `WEEK_IN_SECONDS` | `MONTH_IN_SECONDS`  | `YEAR_IN_SECONDS` |                  |
| `DB_HOST`         | `DB_NAME`           | `DB_USER`         | `DB_PASSWORD`    |


In addition to that, it will clear the PHP Opcache before running the tests.

**_Implementation example:_**
```php
<?php
// File: tests/bootstrap.php
require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/BrainMonkey/bootstrap.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';
```

To tell PHPUnit to use this bootstrap file, use `--bootstrap tests/bootstrap.php` on the command-line or add the `bootstrap="tests/bootstrap.php"` attribute to your `phpunit.xml.dist` file.


#### Helpers to create test doubles for unavailable classes

> :bulb: The problem this feature solves has been fixed in Mockery 1.6.0, so if you use Mockery 1.6.0 or higher for test runs against PHP 8.2 or higher, you should no longer need this solution.

##### Why you may need to create test doubles for unavailable classes

Typically a mock for an unavailable class is created using `Mockery::mock()` or `Mockery::mock( 'Unavailable' )`.

When either the test or the code under test sets a property on such a mock, this will lead to a ["Creation of dynamic properties is deprecated" notice](https://wiki.php.net/rfc/deprecate_dynamic_properties) on PHP >= 8.2, which can cause tests to error out.

If you know for sure the property being set on the mock is a declared property or a property supported via [magic methods](https://www.php.net/oop5.overloading#language.oop5.overloading.members) on the class which is being mocked, the _code under test_ will under normal circumstances never lead to this deprecation notice, but your tests now do.

Primarly this is an issue with Mockery. A [question on how to handle this/to add support for this in Mockery itself](https://github.com/mockery/mockery/issues/1197) is open, but in the mean time a work-around is needed (if you're interested, have a read through the Mockery issue for details about alternative solutions and why those don't work as intended).

##### How to use the functionality

WP Test Utils offers three new utilities to solve this (as of version 1.1.0).
* `Yoast\WPTestUtils\BrainMonkey\makeDoublesForUnavailableClasses( array $class_names )` for use from within a test bootstrap file.
* `Yoast\WPTestUtils\BrainMonkey\TestCase::makeDoublesForUnavailableClasses( array $class_names )` and `Yoast\WPTestUtils\BrainMonkey\TestCase::makeDoubleForUnavailableClass( string $class_name )` for use from within a test class.

These methods can be used to create one or more "fake" test double classes on the fly, which allow for setting (dynamic) properties.
These "fake" test double classes are effectively opaque classes which extend `stdClass`.

These methods are solely intended for classes which are unavailable during the test run and have no effect (at all!) on classes which _are_ available during the test run.

For setting expectations on the "fake" test double, use `Mockery::mock( 'FakedClass' )`.

**_Implementation example using the bootstrap function:_**

You can create the "fake" test doubles for a complete test suite in one go in your test bootstrap file like so:

```php
<?php
// File: tests/bootstrap.php
require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/BrainMonkey/bootstrap.php';
require_once dirname( __DIR__ ) . '/vendor/autoload.php';

Yoast\WPTestUtils\BrainMonkey\makeDoublesForUnavailableClasses( [ 'WP_Post', 'wpdb' ] );
```

**_Implementation example using these functions from within a test class:_**

Alternatively, you can create the "fake" test double(s) last minute in each test class which needs them.

```php
<?php
namespace PackageName\Tests;

use Mockery;
use WP_Post;
use wpdb;
use Yoast\WPTestUtils\BrainMonkey\TestCase;

class FooTest extends TestCase {
    protected function set_up_before_class() {
        parent::set_up_before_class();
        self::makeDoublesForUnavailableClasses( [ WP_Post::class, wpdb::class ] );
        // or if only one class is needed:
        self::makeDoubleForUnavailableClass( WP_Post::class );
    }

    public function testSomethingWhichUsesWpPost() {
        $wp_post = Mockery::mock( WP_Post::class );
        $wp_post->post_title = 'my test title';
        $wp_post->post_type  = 'my_custom_type';

        $this->assertSame( 'expected', \function_under_test( $wp_post ) );
    }
}
```


### Utilities for running integration tests with WordPress

#### What these utilities solve

1. **Running tests using the PHPUnit native [mocking functionality](https://phpunit.readthedocs.io/en/stable/test-doubles.html) against PHP 8.0, while testing against WordPress 5.6 - 5.8.**

    WP 5.6 is the first WordPress version with (beta) support for PHP 8.0.

    In most cases, for WordPress integration tests, the WordPress Core native test bootstrap file will be loaded to set up the test environment, including the database.<br/>
    However, WordPress, until WP 5.9, had a hard limit on PHPUnit 7.5 max, while PHPUnit 9.3 is the first PHPUnit version which has full PHP 8.0 support, making testing on PHP 8.0 with WP 5.6 - 5.8 problematic.

    In WordPress 5.6 to 5.8, this problem was solved by adding copies of select PHPUnit 9.x files to the WordPress test suite and loading those files when running PHPUnit 7.x on PHP 8.0, instead of the PHPUnit 7.x native ones.<br/>
    The way this solution was implemented, however, is not portable to plugins/themes.

    WP Test Utils solves this problem for plugin and theme integration tests via the WP Integration test bootstrap utilities.

2. **WP 5.9 makes significant changes to the WP Core test suite**

    As of WP 5.9, the [PHPUnit Polyfills] package has become a requirement, test fixtures now need to be declared in `snake_case` etc.<br/>
    For full details about what has changed in the WP Core test suite in WP 5.9, please see the [Make Core dev-note about these changes](https://make.wordpress.org/core/2021/09/27/changes-to-the-wordpress-core-php-test-suite/).

    These WP Core test changes have been partially backported and once a plugin/theme integration test suite has been upgraded for the WP 5.9 changes, it can be safely run against the WP `trunk` and `5.x` (`5.2` - `5.8`) branches.

    There is a caveat to this however:
    - Plugins/themes which test against older WP versions (< `5.2`) don't have access to either the polyfills or the snake_case fixture method wrappers.
    - Same goes for tests being run against WP `latest` until WordPress `5.8.2` has been tagged.
    - Same goes for tests being run against specific WP 5.2 - 5.8 minors released before the backports were committed, i.e. WP `5.2.0` - `5.2.12`, WP `5.3.0` - `5.3.9`, WP `5.4.0` - `5.4.7`, WP `5.5.0` - `5.5.6`, WP `5.6.0` - `5.6.5`, WP `5.7.0` - `5.7.3` and WP `5.8.0` - `5.8.1`.

    WP Test Utils solves this problem by having two version of the `TestCase` offered and loading the correct one depending on the WP version the tests are being run against.<br/>
    The loading of the correct `TestCase` is, again, handled via the WP Integration test bootstrap utilities.


#### Basic `TestCase` for WordPress integration tests

Features of this `TestCase`:
1. Extends the WP native base test case `WP_UnitTestCase`, making all the WP Core test utilities available to your integration test classes.
2. Cross-version compatibility with PHPUnit 5.7 - 9.x via the [PHPUnit Polyfills] package.<br/>
    _Using these polyfills you can use the up-to-date PHPUnit 9.x syntax, independently of the WP version against which the tests are being run._<br/>
    _Note: WordPress Core in WP < 5.9 still limits integration tests to running on PHPUnit 7.5 max. As of WP 5.9, tests can run cross-version on PHPUnit 5.7 - 9.x._
3. Ability to use the fixture method `snake_case` wrappers independently of the WP version against which the tests are being run.
4. Helper functions for setting expectations for generated output.

**_Implementation example:_**
```php
<?php
namespace PackageName\Tests;

use Yoast\WPTestUtils\WPIntegration\TestCase;

class FooTest extends TestCase {
    protected function set_up() {
        parent::set_up();
        // Your own additional setup.
    }
}
```


#### Bootstrap utility functions and custom autoloader

The WP Integration bootstrap utilities consist of:
* A `bootstrap-functions.php` file with utility functions for use in the test `bootstrap` file for the integration tests for a plugin or theme.
* A custom autoloader which will selectively autoload the WP copies of the PHPUnit 9 native MockBuilder files for PHPUnit < 9 when run on PHP 8 and which will handle loading the correct `TestCase` version depending on the WP version tests are run against.

The bootstrap utilies presume two things:
1. This package is installed as a dependency of a plugin via Composer and will be in the `vendor/yoast/wp-test-utils/` directory.
2. The `includes` subdirectory of the WordPress Core native `tests/phpunit` directory is available.

The location of the `includes` directory containing the test framework files from WordPress Core will be determined in the following manner:
1. Check if a `WP_TESTS_DIR` environment variable is available pointing to the WordPress Core `./tests/phpunit` directory; or a directory containing a copy of the `includes` subdirectory from the WordPress Core `./tests/phpunit` directory.
2. If not, check to see if a `WP_DEVELOP_DIR` environment variable is available which points to a path containing the WordPress Core root directory from a git/svn check-out.
3. If not, it is checked if the plugin/theme is installed within WordPress itself in a `src/wp-content/plugins/plugin-name` or `src/wp-content/themes/theme-name` directory, with this package in the `src/wp-content/plugins/plugin-name/vendor/yoast/wp-test-utils` directory.
4. As a last resort, a check is done for the typical WP-CLI `scaffold` command setup, where the `includes` subdirectory from the WordPress Core `tests/phpunit` directory has been placed in the system temp directory.

These checks are in line with typical integration test setups in the context of WordPress.

:point_right: The above mentioned environment variables can be set on the OS level or from within a `phpunit.xml[.dist]` file.


##### Using the bootstrap utilities

There are two prevelant patterns for wiring in a plugin/theme to WordPress in an integration test bootstrap file. The bootstrap utilities can be used with both.

To implement use of the bootstrap utilities, use whichever pattern matches your current bootstrap file most closely.

**_Implementation example 1:_**

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
 * Bootstrap WordPress. This will also load the Composer autoload file, the PHPUnit Polyfills
 * and the custom autoloader for the TestCase and the mock object classes.
 */
WPIntegration\bootstrap_it();

if ( ! defined( 'WP_PLUGIN_DIR' ) || file_exists( WP_PLUGIN_DIR . '/plugin-name/main-file.php' ) === false ) {
    echo PHP_EOL, 'ERROR: Please check whether the WP_PLUGIN_DIR environment variable is set and set to the correct value. The integration test suite won\'t be able to run without it.', PHP_EOL;
    exit( 1 );
}
```

**_Implementation example 2:_**
```php
use Yoast\WPTestUtils\WPIntegration;

require_once dirname( __DIR__ ) . '/vendor/yoast/wp-test-utils/src/WPIntegration/bootstrap-functions.php';

$_tests_dir = WPIntegration\get_path_to_wp_test_dir();

// Get access to tests_add_filter() function.
require_once $_tests_dir . 'includes/functions.php';

/**
 * Callback to manually load the plugin
 */
function _manually_load_plugin() {
    require_once __DIR__ . '/relative/path/to/main-file.php';
}

// Add plugin to active mu-plugins to make sure it gets loaded.
tests_add_filter( 'muplugins_loaded', '_manually_load_plugin' );

/*
 * Bootstrap WordPress. This will also load the Composer autoload file, the PHPUnit Polyfills
 * and the custom autoloader for the TestCase and the mock object classes.
 */
WPIntegration\bootstrap_it();
```


### Test Helpers

#### `Yoast\WPTestUtils\Helpers\EscapeOutputHelper` trait

PHPUnit natively contains the `expectOutputString()` (exact string) and the `expectOutputRegex()` (regex match) method, but sometimes you need a little more flexibility.

A typical pattern used, is to check whether the output generated contains certain substrings.
And a typical reason for mismatched output versus expectation, is a mismatch in line endings.

This `EscapeOutputHelper` trait adds the following functions to solve these issues:
* `expectOutputContains( $expected, $ignoreEolDiff = true )` to verify whether generated output contains a certain substring.
    In the same vein as the PHPUnit native methods, only one such expectation can be set in a test.
    If a test needs to check that the generated output contains multiple different substrings, refactor the test to use a data provider feeding the test one substring at a time.
* `normalizeLineEndings( $output )` which is intended to be used in conjunction with the PHPUnit native `setOutputCallback()` method to normalize line endings in the actual output before comparing output expectations with the actual output.

:point_right: This trait is automatically available to test classes based on the BrainMonkey or WPIntegration `TestCase`s as included in WP Test Utils.


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
