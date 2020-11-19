<?php namespace Tatter\Reddit;

use Tests\Support\RedditTestCase;

/**
 * Comments Test Case
 *
 * Tests functionality specific to the 
 * comments endpoint but also general
 * to all Listing protocols.
 *
 * @see https://www.reddit.com/dev/api#listings
 */
class CommentsTest extends RedditTestCase
{
	public function testCommentsFetches()
	{
		$result = $this->reddit->limit(1)->comments();

		d($result);
	}
}
