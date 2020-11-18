<?php namespace Tatter\Reddit\Tokens;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\TokensException;

class MemoryTest extends CIUnitTestCase
{
	public function testStoreSucceeds()
	{
		$token = 'foobar';

		MemoryHandler::store($token);

		$this->assertEquals($token, MemoryHandler::retrieve());
	}

	public function testRetrieveThrows()
	{
		// Make sure it is blank
		MemoryHandler::store('');

		$this->expectException(TokensException::class);

		MemoryHandler::retrieve();
	}
}
