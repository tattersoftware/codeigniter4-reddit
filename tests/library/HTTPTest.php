<?php namespace Tatter\Reddit;

use Tatter\Reddit\HTTP\RedditResponse;
use Tatter\Reddit\Structures\Listing;
use Tatter\Reddit\Structures\Thing;
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

	public function testFetchReturnsListing()
	{
		$result = $this->reddit->fetch($this->uri);

		$this->assertInstanceOf(Listing::class, $result);
	}

	public function testFetchReturnsResult()
	{
		$result = $this->reddit->subreddit()->request('api/info', null, ['id' => 't3_jw6u2r'])->getResult();

		$this->assertInstanceOf('stdClass', $result);
	}
}
