<?php

namespace Tatter\Reddit\Tokens;

use Tests\Support\RedditTestCase;

/**
 * @internal
 */
final class PasswordTest extends RedditTestCase
{
	public function testRetrieve()
	{
		$result = PasswordHandler::retrieve();

		$this->assertIsString($result);
	}
}
