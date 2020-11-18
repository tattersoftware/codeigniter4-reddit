<?php namespace Tatter\Reddit\Tokens;

use Config\Services;
use Tatter\Reddit\Exceptions\TokensException;
use Tests\Support\RedditTestCase;

class PasswordTest extends RedditTestCase
{
	public function testRetrieve()
	{
		$result = PasswordHandler::retrieve();

		$this->assertIsString($result);
	}
}
