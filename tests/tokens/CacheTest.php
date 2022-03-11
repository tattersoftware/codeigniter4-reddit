<?php

namespace Tatter\Reddit\Tokens;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\TokensException;

/**
 * @internal
 */
final class CacheTest extends CIUnitTestCase
{
	public function testRetrieveThrows()
	{
		$this->expectException(TokensException::class);

		CacheHandler::retrieve();
	}

	public function testRetrieveSucceeds()
	{
		$token = 'foobar';
		cache()->save('reddit_access_token', $token);

		$result = CacheHandler::retrieve();

		$this->assertIsString($result);
		$this->assertSame($token, $result);
	}

	public function testStoreSucceeds()
	{
		$token = 'foobar';

		CacheHandler::store($token);

		$this->assertSame($token, cache('reddit_access_token'));
	}
}
