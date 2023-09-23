# Change Log for Yoast WP Test Utils

All notable changes to this project will be documented in this file.

This projects adheres to [Keep a CHANGELOG](http://keepachangelog.com/) and uses [Semantic Versioning](http://semver.org/).


## [Unreleased]

_Nothing yet._

## [1.2.0] - 2023-09-27

### Added

* Support for the new PHPUnit `assertObjectHasProperty()` and `assertObjectNotHasProperty()` assertions, as polyfilled via the PHPUnit Polyfills in all test cases. PR [#64]
    This means that the `assertObjectHasProperty()` and `assertObjectNotHasProperty()` assertions can now safely be used in all tests in classes which extend one of the WP Test Utils TestCases.

### Changed
* `Yoast\WPTestUtils\BrainMonkey\YoastTestCase`: the parameter names used in a few of the stubs for WP Core functions have been updated to stay in line with the names used in WP Core. PR [#53]
* The [PHPUnit Polyfills] dependency has been updated to require [version `^1.1.0`](https://github.com/Yoast/PHPUnit-Polyfills/releases/tag/1.1.0) (was `^1.0.5`). PRs [#52], [#64]
* Verified PHP 8.3 compatibility.
* General housekeeping.

[#52]: https://github.com/Yoast/wp-test-utils/pull/52
[#53]: https://github.com/Yoast/wp-test-utils/pull/53
[#64]: https://github.com/Yoast/wp-test-utils/pull/64


## [1.1.1] - 2022-11-17

### Fixed
* The "on the fly" created test doubles would identify as the same class when comparing objects using `instanceof`. [#45]
    The underlying logic has been changed to prevent this.
    This includes removing the (non-public API) `Yoast\WPTestUtils\BrainMonkey\Doubles\DummyTestDouble` class which was introduced in 1.1.0.

[#45]: https://github.com/Yoast/wp-test-utils/pull/45


## [1.1.0] - 2022-11-17

### Added
* Helper functions for on-the-fly creation of opaque test doubles for unavailable classes for use with Mockery. PR [#40]
    The default Mockery mocks for unavailable classes do not allow for the dynamic property deprecation in PHP 8.2, which can cause tests to error out.
    These helper functions can be used to create test doubles which do allow for setting properties.
    The helper functions can be called from within a test bootstrap or from within a test class.
    For full details about these new functions, why they exist and how to use them, please see [the documentation in the README](https://github.com/Yoast/wp-test-utils#helpers-to-create-test-doubles-for-unavailable-classes).

### Changed
* The [BrainMonkey] dependency has been updated to require [version `^2.6.1`](https://github.com/Brain-WP/BrainMonkey/releases/tag/2.6.1) (was `^2.6.0`). PR  [#28]
* The [PHPUnit Polyfills] dependency has been updated to require [version `^1.0.4`](https://github.com/Yoast/PHPUnit-Polyfills/releases/tag/1.0.4) (was `^1.0.1`). PRs [#28], [#41]
* Composer: The package will now identify itself as a testing tool. PR [#36]
* Verified PHP 8.2 compatibility.
* General housekeeping.

[#28]: https://github.com/Yoast/wp-test-utils/pull/28
[#36]: https://github.com/Yoast/wp-test-utils/pull/36
[#40]: https://github.com/Yoast/wp-test-utils/pull/40
[#41]: https://github.com/Yoast/wp-test-utils/pull/41


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
[1.2.0]: https://github.com/Yoast/wp-test-utils/compare/1.1.1...1.2.0
[1.1.1]: https://github.com/Yoast/wp-test-utils/compare/1.1.0...1.1.1
[1.1.0]: https://github.com/Yoast/wp-test-utils/compare/1.0.0...1.1.0
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
