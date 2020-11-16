WP Test Utils
=====================================================

[![Version](https://poser.pugx.org/yoast/wp-test-utils/version)](https://packagist.org/packages/yoast/wp-test-utils)
[![Travis Build Status](https://travis-ci.com/Yoast/wp-test-utils.svg?branch=main)](https://travis-ci.com/Yoast/wp-test-utils/branches)
[![Minimum PHP Version](https://img.shields.io/packagist/php-v/yoast/wp-test-utils.svg?maxAge=3600)](https://packagist.org/packages/yoast/wp-test-utils)
[![License: BSD3](https://poser.pugx.org/yoast/wp-test-utils/license)](https://github.com/Yoast/wp-test-utils/blob/master/LICENSE)

This library contains a set of utilities for running automated tests for WordPress plugins and themes.

* [Requirements](#requirements)
* [Installation](#installation)
* [Features](#features)
    - [Utilities for running tests using BrainMonkey](#utilities-for-running-tests-using-brainmonkey)
* [Contributing](#contributing)
* [License](#license)


Requirements
-------------------------------------------

* PHP 5.6 or higher.

The following packages will be automatically required via Composer:
* [PHPUnit Polyfills] 0.1.0 or higher.
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
    | `user_trailingslashit( $string )`                      | `trailingslashit( $string )`                                                          | This function will be native stubbed by BrainMonkey as of version 2.6.0. |
    | `wp_json_encode( $data, $options = 0, $depth = 512 )`  | `json_encode( $data, $options, $depth )`                                              | This function will be native stubbed by BrainMonkey as of version 2.6.0. |
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
