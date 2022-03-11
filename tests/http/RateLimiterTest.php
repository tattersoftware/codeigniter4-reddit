<?php namespace Tatter\Reddit\HTTP;

use CodeIgniter\Events\Events;
use CodeIgniter\HTTP\Header;
use Tests\Support\RedditTestCase;

class RateLimiterTest extends RedditTestCase
{
	/**
	 * Array of example Headers to test with
	 *
	 * @var Header[]
	 */
	private $testHeaders;

	/**
	 * @var RateLimiter
	 */
	private $limiter;

	protected function setUp(): void
	{
		parent::setUp();

		$this->limiter = new RateLimiter();

		$this->testHeaders = [
			'x-ratelimit-used'      => new Header('x-ratelimit-used', '8'),
			'x-ratelimit-remaining' => new Header('x-ratelimit-remaining', '2'),
			'x-ratelimit-reset'     => new Header('x-ratelimit-reset', '2'),
		];
	}

	/**
	 * Returns the RateLimiter's current properties
	 *
	 * @return array<string,mixed>
	 */
	protected function getProperties(): array
	{
		$data = [];
		foreach (['last', 'used', 'remaining', 'reset'] as $key)
		{
			$data[$key] = $this->getPrivateProperty($this->limiter, $key);
		}

		return $data;
	}

	//--------------------------------------------------------------------

	public function testRespondStoresValues()
	{
		$result = $this->getProperties();
		$this->assertNull($result['last']);
		$this->assertNull($result['remaining']);

		$this->limiter->respond($this->testHeaders);

		$result = $this->getProperties();
		$this->assertEquals(8, $result['used']);
		$this->assertEquals(2, $result['reset']);
	}

	public function testEventTriggersStore()
	{
		$this->limiter->respond($this->testHeaders);

		Events::trigger('post_system');

		$this->assertEquals(8, cache('reddit_rate_used'));
	}

	public function testLimiterDelays()
	{
		// Stage an exhausted quota
		$this->testHeaders['x-ratelimit-remaining'] = new Header('x-ratelimit-remaining', '0');
		$this->limiter->respond($this->testHeaders);

		$now = time();
		$this->limiter->request();
		$passed = time() - $now;

		$this->assertGreaterThanOrEqual(2, $passed);
	}
}
