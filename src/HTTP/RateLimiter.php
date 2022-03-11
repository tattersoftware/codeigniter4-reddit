<?php namespace Tatter\Reddit\HTTP;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\Events\Events;
use CodeIgniter\HTTP\Header;

/**
 * Rate Limiter Class
 *
 * Handles storing, retrieving, and enforcing
 * Reddit API rate limits based on response headers.
 *
 * @see https://github.com/reddit-archive/reddit/wiki/API#rules
 */
class RateLimiter
{
	/**
	 * @var CacheInterface
	 */
	protected $cache;

	/**
	 * Timestamp of the last request made.
	 *
	 * @var int|null
	 */
	protected $last;

	/**
	 * Approximate number of requests used in this period.
	 *
	 * @var float|null
	 */
	protected $used;

	/**
	 * Approximate number of requests left to use.
	 *
	 * @var float|null
	 */
	protected $remaining;

	/**
	 * Approximate number of seconds to end of period.
	 *
	 * @var float|null
	 */
	protected $reset;

	/**
	 * Initializes and accesses the Cache handler
	 * and registers the shutdown storage event
	 *
	 * @param CacheInterface|null $cache
	 */
	public function __construct(CacheInterface $cache = null)
	{
		$this->cache = $cache ?? cache();

		$this->retrieve();

		Events::on('post_system', [$this, 'store']);
	}

	//--------------------------------------------------------------------

	/**
	 * Delays an API request, if necessary.
	 *
	 * @returns $this
	 */
    public function request(): void
    {
    	// Nothing to do without data
    	if (is_null($this->remaining))
    	{
    		return;
    	}

		// Determine the time passed since the last request
		$passed = $this->last ? time() - $this->last : 0;

		// Update the $reset timer
		$this->reset = $this->reset - $passed;
		$this->last  = time();

		// Check for available requests
		if ($this->remaining < 1)
		{
			// If the delay risks timing out then store preemptively
			if ($this->reset > ini_get('max_execution_time') / 2)
			{
				$this->store();
			}

			// Delay the request until the next reset
			$this->wait((int) ceil($this->reset));
		}

		$this->remaining--;
		$this->used++;
    }

	/**
	 * Sleeps for a period of time.
	 * Split out for testing.
	 */
    protected function wait(int $seconds): void
    {
        sleep($seconds);
    }

	//--------------------------------------------------------------------

	/**
	 * Updates limits from response headers.
	 *
	 * @param array<string,Header> $headers
	 */
    public function respond(array $headers): void
    {
		// Make sure all keys are present before updating
		$keys = ['used', 'remaining', 'reset'];

		$values = [];
    	foreach ($keys as $key)
    	{
    		$name = 'x-ratelimit-' . $key;
    		if (! isset($headers[$name]))
    		{
    			return;
			}

			$values[$key] = (float) trim($headers[$name]->getValue());
    	}

		// Update the properties
		foreach ($values as $key => $value)
		{
			$this->$key = $value;
		}

		$this->last = time();
    }

	/**
	 * Attempts to load current values from cache.
	 */
	protected function retrieve(): void
	{
		$keys = ['last', 'used', 'remaining', 'reset'];

		foreach ($keys as $key)
		{
			$value = $this->cache->get("reddit_rate_{$key}");
			if ($value !== null)
			{
				$this->$key = $value;
			}
		}
	}

	/**
	 * Stores values to the cache.
	 */
	public function store(): void
	{
		$keys = ['last', 'used', 'remaining', 'reset'];

		foreach ($keys as $key)
		{
			$this->cache->save("reddit_rate_{$key}", $this->$key, 0);
		}
	}
}
