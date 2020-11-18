<?php namespace Tatter\Reddit;

use Tests\Support\RedditTestCase;

class CommentsTest extends RedditTestCase
{
	public function testFetchComments()
	{
		$result = $this->reddit->fetchComments();

		dd($result);
	}
}
