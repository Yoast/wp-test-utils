<?php

namespace Yoast\WPTestUtils\BrainMonkey\Doubles;

use stdClass;

/**
 * This is a "dummy" test double class for use with Mockery.
 *
 * This class allows to mock classes which are unavailable during the test run
 * and on which properties need to be set, either from within the test or
 * from within the code under test, by aliasing this class ahead of creating the mock.
 *
 * Mocking unavailable classes using an anonymous mock - `Mockery::mock()` or
 * a mock for a specific named, but unavailable class - `Mockery::mock( 'Unavailable' )` -
 * worked fine prior to PHP 8.2.
 * However, PHP 8.2 deprecates the use of dynamic properties, which means that if
 * either of the above code patterns is used and either the test or the code under
 * test sets properties on the Mock, tests will throw deprecation notices and,
 * depending on the value for the PHPUnit `convertDeprecationsToExceptions` configuration
 * option, tests may show as errored.
 *
 * The "go to" pattern to solve this is to let the mock extend `stdClass`, but
 * as `stdClass` always exists, the class will then identify as an instance of `stdClass`
 * and no longer as an instance of the "Unavailable" class, which causes problems
 * with code using type declarations of calls to `instanceof`.
 *
 * The other alternative would be to used `Mockery::namedMock()` or an alias mock, but
 * both of these require each test using these to run in a separate process, which
 * makes debugging of failing tests more complicated as well as making the test suite
 * slower.
 *
 * Note: aliasing `stdClass` directly is not allowed in PHP, which is why this
 * dummy test double class is put in place.
 *
 * @link https://github.com/mockery/mockery/issues/1197
 */
class DummyTestDouble extends stdClass {}
