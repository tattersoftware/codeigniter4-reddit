<?php namespace Tatter\Reddit;

use Tatter\Reddit\HTTP\RedditResponse;
use Tests\Support\RedditTestCase;

class HTTPTest extends RedditTestCase
{
	public function testRequestReturnsRedditResponse()
	{
		$response = $this->reddit->request($this->uri);

		$this->assertInstanceOf(RedditResponse::class, $response);
	}

	public function testFetchPrependsSubreddit()
	{
		$this->reddit->subreddit('php');

		$this->reddit->fetch('new');
		$result = $this->reddit->getArchive();

		$this->assertEquals('/r/php/new', $result['uri']);
	}
}
