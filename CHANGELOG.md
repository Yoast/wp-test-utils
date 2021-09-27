# Change Log for Yoast WP Test Utils

All notable changes to this project will be documented in this file.

This projects adheres to [Keep a CHANGELOG](http://keepachangelog.com/) and uses [Semantic Versioning](http://semver.org/).


## [Unreleased]

_Nothing yet._

## [1.0.0] - 2021-09-27

WordPress 5.9 contains significant changes to the WordPress native test suite, which impacts **integration tests**.<br/>
Please see the [dev-note about these changes on Make WordPress](https://make.wordpress.org/core/2021/09/27/changes-to-the-wordpress-core-php-test-suite/) for full details.

This release makes WP Test Utils compatible with these changes, but can't fully mitigate them, though if you were using WP Test Utils before, you're already half prepared for these changes.

For users of WP Test Utils, a search for declarations of and calls to the `setUpBeforeClass()`, `setUp()`, `tearDown()` and `tearDownAfterClass()` methods and replacing these with their snake_case equivalents `set_up_before_class()`, `set_up()` `tear_down()` and `tear_down_after_class()` is all that is required to make your test suite compatible again with the latest versions of WordPress.

You also may want to do some tweaking to the CI scripts used to run the tests to allow for using the optimal PHPUnit version to run the tests.<br/>
See the [Make Core dev-note](https://make.wordpress.org/core/2021/09/27/changes-to-the-wordpress-core-php-test-suite/#integration-tests-ci-changes) for guidance.

### Added
* Integration tests bootstrap utilities: the `Yoast\WPTestUtils\WPIntegration\get_path_to_wp_test_dir()` function will now also search for the WP Core test framework files in the system temp directory as per the typical setup created by the [WP-CLI `scaffold` command]. PR [#16]<br/>
    This means that if the `install-wp-tests.sh` script is used without adjustments, the path to the WP native test bootstrap should be findable by WP Test Utils without needing to set the `WP_TESTS_DIR` environment variable.<br/>
    If you previously adjusted your test bootstrap to set this environment variable, you should now be able to remove it.

### Changed
* Integration tests: both the `TestCase` as well as the bootstrap utilities have been adjusted to be cross-version compatible with the WP Core test framework as it is per WP 5.9, while still maintaining compatibility with older WP versions as well, includes WP < 5.2. PR [#20]
* `Yoast\WPTestUtils\BrainMonkey\YoastTestCase`: the `is_multisite()` stub will now respect a potentially set `WP_TESTS_MULTISITE` PHP constant. PR [#22]
* The [PHPUnit Polyfills] dependency has been updated to require [version `^1.0.1`](https://github.com/Yoast/PHPUnit-Polyfills/releases/tag/1.0.1) (was `^1.0.0`).
* README: the documentation has been partially rewritten to make it clearer what problems WP Test Utils solves.
* General housekeeping.

### Fixes
* The [PHPUnit Polyfills] dependency introduced three new polyfills in the `1.0.0` version. These are now supported in all test cases. [#17]


Thanks [Pierre Gordon] and [Pascal Birchler] for making feature suggestions for this version.

[#16]: https://github.com/Yoast/wp-test-utils/pull/16
[#17]: https://github.com/Yoast/wp-test-utils/pull/17
[#20]: https://github.com/Yoast/wp-test-utils/pull/20
[#22]: https://github.com/Yoast/wp-test-utils/pull/22

[Pierre Gordon]: https://github.com/pierlon
[Pascal Birchler]: https://github.com/swissspidy


## [0.2.2] - 2021-06-21

### Changed
* The [PHPUnit Polyfills] dependency has been updated to require [version `^1.0.0`](https://github.com/Yoast/PHPUnit-Polyfills/releases/tag/1.0.0) (was `^0.2.0`).
* Improved compatibility with the test setup as created via the [WP-CLI `scaffold` command].
* CI is now run via GitHub Actions.


## [0.2.1] - 2020-12-09

### Changed
* The [BrainMonkey] dependency has been updated to require version `^2.6.0` (was `^2.5.0`).
* `Yoast\WPTestUtils\BrainMonkey\YoastTestCase`: removed stubs for `wp_json_encode()` and `user_trailingslashit()`.<br/>
    These are now stubbed in the BrainMonkey library.


## [0.2.0] - 2020-12-09

### Added
* A `TestCase` for WordPress plugin integration tests.
* Utility functions and a custom autoloader for use within a plugin's integration test `bootstrap.php` file.
* An `EscapeOutputHelper` trait.

Full details on these new features can be found in the [README].


## [0.1.1] - 2020-11-25

### Changed
* The [PHPUnit Polyfills] dependency has been updated to require version `^0.2.0` (was `^0.1.0`).


## [0.1.0] - 2020-11-13

Initial release.


[Unreleased]: https://github.com/Yoast/wp-test-utils/compare/main...HEAD
[1.0.0]: https://github.com/Yoast/wp-test-utils/compare/0.2.2...1.0.0
[0.2.2]: https://github.com/Yoast/wp-test-utils/compare/0.2.1...0.2.2
[0.2.1]: https://github.com/Yoast/wp-test-utils/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/Yoast/wp-test-utils/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/Yoast/wp-test-utils/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/Yoast/wp-test-utils/compare/35bd47e4d59568ee0bf0997b49111c6fd0da7a8e...0.1.0

[BrainMonkey]:       https://github.com/Brain-WP/BrainMonkey/releases
[PHPUnit Polyfills]: https://github.com/Yoast/PHPUnit-Polyfills/releases
[README]:            https://github.com/Yoast/wp-test-utils/blob/develop/README.md
[WP-CLI `scaffold` command]: https://github.com/wp-cli/scaffold-command/
