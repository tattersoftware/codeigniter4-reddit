<?php

namespace Tatter\Reddit;

use Tatter\Reddit\HTTP\RedditRequest;
use Tatter\Reddit\HTTP\RedditResponse;
use Tests\Support\RedditTestCase;

/**
 * @internal
 */
final class RequestTest extends RedditTestCase
{
	/**
	 * @var RedditRequest
	 */
	private $request;

	protected function setUp(): void
	{
		parent::setUp();

		$this->request = new RedditRequest($this->config);
	}

	public function testLimitLimits()
	{
		$limit = 2;

		$this->request->setQuery('limit', $limit);
		$response = $this->request->fetch($this->uri);
		$this->assertInstanceOf(RedditResponse::class, $response);

		$result = $response->getResultPath('data/children');
		$this->assertCount($limit, $result);
	}
}
