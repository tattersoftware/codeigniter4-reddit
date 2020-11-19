<?php namespace Tatter\Reddit;

use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\HTTP\RedditRequest;
use Tatter\Reddit\HTTP\RedditResponse;

/**
 * Reddit Class
 *
 * Primary library for negotiating the connection
 * and preprocessing responses from the Reddit API.
 */
class Reddit
{
	/**
	 * @var RedditConfig
	 */
	protected $config;

	/**
	 * Reddit CURL client preconfigured for API calls
	 *
	 * @var RedditRequest
	 */
	protected $request;

	/**
	 * Name of the current subreddit
	 *
	 * @var string
	 */
	protected $subreddit;

	/**
	 * Parameters from the last fetch, used for repeating requests
	 *
	 * @var array|null
	 */
	private $archive;

	/**
	 * Initializes the library.
	 *
	 * @param RedditConfig $config
	 */
	public function __construct(RedditConfig $config)
	{
		$this->config  = $config;
		$this->request = new RedditRequest($config);

		$this->subreddit($config->subreddit);
	}

	//--------------------------------------------------------------------
	// CONTENT ENDPOINTS
	//--------------------------------------------------------------------

	/**
	 * Fetches subreddit comments
	 *
	 * @return mixed
	 */
	public function comments()
	{
		$uri = '/r/' . $this->getSubreddit() . '/comments';

		$response = $this->fetch($uri);

		return $response->getResultPath('data/children');
	}

	//--------------------------------------------------------------------
	// SUPPORT METHODS
	//--------------------------------------------------------------------

	/**
	 * Passes a request through to RedditRequest, archiving the parameters
	 * and returning the raw RedditResponse.
	 *
	 * @param string $uri      URI segment
	 * @param array|null $data Additional data for the request
	 * @param array $query     Additional query parameters
	 *
	 * @return RedditResponse
	 *
	 * @throws RedditException
	 */
	public function fetch(string $uri, $data = null, $query = []): RedditResponse
	{
		$this->archive = [
			'uri'   => $uri,
			'data'  => $data,
			'query' => $query,
		];

		try
		{
			$response = $this->request->fetch($uri, $data, $query);
		}
		// Rethrow as a RedditException
		catch (\Throwable $e)
		{
			throw new RedditException($e->getMessage(), $e->getCode(), $e);
		}

		return $response;
	}

	/**
	 * Gets the current subreddit.
	 * Throws if the property is empty to prevent URI failures.
	 *
	 * @return string
	 * @throws RedditException
	 */
	public function getSubreddit(): string
	{
		if (empty($this->subreddit))
		{
			throw new RedditException(lang('Reddit.missingSubreddit'));
		}

		return $this->subreddit;
	}

	/**
	 * Sets the current subreddit
	 *
	 * @param string $subreddit
	 *
	 * @return $this
	 */
	public function subreddit(string $subreddit): self
	{
		$this->subreddit = $subreddit;

		return $this;
	}

	//--------------------------------------------------------------------
	// QUERY PARAMETERS
	//--------------------------------------------------------------------

	/**
	 * Returns the RedditRequest's current query parameters.
	 * Mostly for testing.
	 *
	 * @return array
	 */
	public function getQuery(): array
	{
		return $this->request->getQuery();
	}

	/**
	 * Sets the "after" query parameter.
	 *
	 * @returns $this
	 */
	public function after(string $after = null): self
	{
		$this->request->setQuery('after', $after);
		if (! is_null($after))
		{
			$this->request->setQuery('before', null);
		}

		return $this;
	}

	/**
	 * Sets the "before" query parameter.
	 *
	 * @returns $this
	 */
	public function before(string $before = null): self
	{
		$this->request->setQuery('before', $before);
		if (! is_null($before))
		{
			$this->request->setQuery('after', null);
		}

		return $this;
	}

	/**
	 * Sets the "count" query parameter.
	 *
	 * @returns $this
	 */
	public function count(int $count = null): self
	{
		$this->request->setQuery('count', $count);
		return $this;
	}

	/**
	 * Sets the "limit" query parameter.
	 *
	 * @returns $this
	 */
	public function limit(int $limit = null): self
	{
		$this->request->setQuery('limit', $limit);
		return $this;
	}

	/**
	 * Sets the "show" query parameter.
	 *
	 * @returns $this
	 */
	public function show(string $show = null): self
	{
		$this->request->setQuery('show', $show);
		return $this;
	}
}
