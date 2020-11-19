<?php namespace Tatter\Reddit;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\HTTP\RedditResponse;

class ResponseTest extends CIUnitTestCase
{
	/**
	 * @var RedditResponse
	 */
	protected $response;

	public function setUp(): void
	{
		parent::setUp();

		$this->response = new RedditResponse(config('App'));
	}

	public function testGetResultThrowsJsonException()
	{
		$this->response->setBody('foobar bam baz');

		$this->expectException('JsonException');
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
		$this->assertEquals('bar', $result->foo);
	}

	public function testGetResultReturnsArray()
	{
		$this->response->setBody('{"foo":"bar"}');
		
		$result = $this->response->getResult(true);

		$this->assertIsArray($result);
		$this->assertEquals('bar', $result['foo']);
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

		$this->assertEquals('bam', $result);
	}
}
