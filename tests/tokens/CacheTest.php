<?php namespace Tatter\Reddit\Tokens;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\TokensException;

class CacheTest extends CIUnitTestCase
{
	public function testRetrieveThrows()
	{
		$this->expectException(TokensException::class);

		$result = CacheHandler::retrieve();
	}

	public function testRetrieveSucceeds()
	{
		$token = 'foobar';
		cache()->save('reddit_access_token', $token);

		$result = CacheHandler::retrieve();

		$this->assertIsString($result);
		$this->assertEquals($token, $result);
	}
}
