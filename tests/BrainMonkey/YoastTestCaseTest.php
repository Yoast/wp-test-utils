<?php

namespace Yoast\WPTestUtils\Tests\BrainMonkey;

use Yoast\WPTestUtils\BrainMonkey\YoastTestCase;

/**
 * Basic test for the Yoast BrainMonkey TestCase setup.
 *
 * @covers \Yoast\WPTestUtils\BrainMonkey\YoastTestCase
 */
class YoastTestCaseTest extends YoastTestCase {

	/**
	 * Verify that the additional stubs added in the setUp() are available.
	 *
	 * @return void
	 */
	public function testSetUp() {
		$this->assertSame( 'https://www.example.org', \site_url() );
		$this->assertFalse( \is_multisite() );
	}
}
