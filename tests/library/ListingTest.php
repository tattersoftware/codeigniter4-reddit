<?php namespace Tatter\Reddit;

use Tatter\Reddit\HTTP\RedditResponse;
use Tests\Support\RedditTestCase;

/**
 * Listing Test Case
 *
 * Tests functionality general
 * to all Listing protocols.
 *
 * @see https://www.reddit.com/dev/api#listings
 */
class ListingTest extends RedditTestCase
{
	public function testCanFetch()
	{
		$uri = '/r/' . $this->reddit->getSubreddit() . '/comments';

		$response = $this->reddit->fetch($uri);
		$this->assertInstanceOf(RedditResponse::class, $response);

		$result = $response->getResult();

		$this->assertEquals('Listing', $result->kind);
	}
}
