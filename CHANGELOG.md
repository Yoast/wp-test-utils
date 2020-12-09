# Change Log for Yoast WP Test Utils

All notable changes to this project will be documented in this file.

This projects adheres to [Keep a CHANGELOG](http://keepachangelog.com/) and uses [Semantic Versioning](http://semver.org/).


## [Unreleased]

_Nothing yet._


## [0.2.1] - 2020-12-09

### Changed
* The [BrainMonkey] dependency has been updated to require version `^2.6.0` (was `^2.5.0`).
* `Yoast\WPTestUtils\BrainMonkey\YoastTestCase`: removed stubs for `wp_json_encode()` and `user_trailingslashit()`.
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
[0.2.1]: https://github.com/Yoast/wp-test-utils/compare/0.2.0...0.2.1
[0.2.0]: https://github.com/Yoast/wp-test-utils/compare/0.1.1...0.2.0
[0.1.1]: https://github.com/Yoast/wp-test-utils/compare/0.1.0...0.1.1
[0.1.0]: https://github.com/Yoast/wp-test-utils/compare/35bd47e4d59568ee0bf0997b49111c6fd0da7a8e...0.1.0

[BrainMonkey]:       https://github.com/Brain-WP/BrainMonkey/releases
[PHPUnit Polyfills]: https://github.com/Yoast/PHPUnit-Polyfills/releases
[README]:            https://github.com/Yoast/wp-test-utils/blob/develop/README.md
