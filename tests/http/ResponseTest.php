<?php

namespace Tatter\Reddit;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\HTTP\RedditResponse;

/**
 * @internal
 */
final class ResponseTest extends CIUnitTestCase
{
	/**
	 * @var RedditResponse
	 */
	protected $response;

	protected function setUp(): void
	{
		parent::setUp();

		$this->response = new RedditResponse(config('App'));
	}

	public function testGetResultThrowsException()
	{
		$this->response->setBody('foobar bam baz');

		$this->expectException(RedditException::class);
		$this->expectExceptionMessage('Syntax error');

		$this->response->getResult();
	}

	public function testGetResultThrowsRedditException()
	{
		$this->response->setBody('{"error":"foo"}');

		$this->expectException(RedditException::class);
		$this->expectExceptionMessage('foo');

		$this->response->getResult();
	}

	public function testGetResultReturnsObject()
	{
		$this->response->setBody('{"foo":"bar"}');

		$result = $this->response->getResult();

		$this->assertIsObject($result);
		$this->assertSame('bar', $result->foo);
	}

	public function testGetResultReturnsArray()
	{
		$this->response->setBody('{"foo":"bar"}');

		$result = $this->response->getResult(true);

		$this->assertIsArray($result);
		$this->assertSame('bar', $result['foo']);
	}

	public function testGetResultPathThrows()
	{
		$this->response->setBody('{"foo":"bar"}');

		$this->expectException(RedditException::class);
		$this->expectExceptionMessage(lang('Reddit.unverifiedPath', ['bam', 'bam/baz']));

		$this->response->getResultPath('bam/baz');
	}

	public function testGetResultPathReturnsContent()
	{
		$this->response->setBody('{"foo":{"bar":"bam"}}');

		$result = $this->response->getResultPath('foo/bar');

		$this->assertSame('bam', $result);
	}
}
