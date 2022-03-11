<?php

namespace Tatter\Reddit;

use stdClass;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\HTTP\RedditRequest;
use Tatter\Reddit\HTTP\RedditResponse;
use Tatter\Reddit\Structures\Listing;
use Tatter\Reddit\Structures\Thing;
use Throwable;

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
	 * Name of the subreddit to apply to requests
	 *
	 * @var string|null
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
	 */
	public function __construct(RedditConfig $config)
	{
		$this->config  = $config;
		$this->request = new RedditRequest($config);

		$this->subreddit($config->subreddit);
	}

	/**
	 * Gets the current subreddit.
	 * Throws if the property is empty to prevent URI failures.
	 *
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
	 * Returns the RedditRequest's current query parameters.
	 * Mostly for testing.
	 */
	public function getQuery(): array
	{
		return $this->request->getQuery();
	}

	/**
	 * Returns the archive of the last request.
	 * Mostly for testing.
	 */
	public function getArchive(): ?array
	{
		return $this->archive;
	}

	//--------------------------------------------------------------------
	// SUPPORT METHODS
	//--------------------------------------------------------------------

	/**
	 * Runs the request and processes the result into the appropriate class.
	 *
	 * @param string     $uri   URI segment
	 * @param array|null $data  Additional data for the request
	 * @param array      $query Additional query parameters
	 *
	 * @return Listing|stdClass|Thing
	 */
	public function fetch(string $uri, $data = null, $query = [])
	{
		// See if we need to prepend a subreddit
		$uri = '/' . trim($uri, '/ ');
		if ($this->subreddit && strpos($uri, '/r/') === false)
		{
			$uri = '/r/' . $this->getSubreddit() . $uri;
		}

		$result = $this->request($uri, $data, $query)->getResult();
		if (empty($result->kind))
		{
			return $result;
		}

		return $result->kind === 'Listing'
			? new Listing($result)
			: Thing::create($result);
	}

	/**
	 * Passes a request through to RedditRequest, archiving
	 * the parameters and returning the RedditResponse.
	 * Exposed for advanced options, but usually use `fetch()`.
	 *
	 * @param string     $uri   URI segment
	 * @param array|null $data  Additional data for the request
	 * @param array      $query Additional query parameters
	 *
	 * @throws RedditException
	 */
	public function request(string $uri, $data = null, $query = []): RedditResponse
	{
		$this->archive = [
			'uri'   => $uri,
			'data'  => $data,
			'query' => $query,
		];

		try {
			$response = $this->request->fetch($uri, $data, $query);
		}
		// Rethrow as a RedditException
		catch (Throwable $e)
		{
			throw new RedditException($e->getMessage(), $e->getCode(), $e);
		}

		return $response;
	}

	//--------------------------------------------------------------------
	// QUERY PARAMETERS
	//--------------------------------------------------------------------

	/**
	 * Validates and sets a subreddit
	 *
	 * @throws RedditException
	 *
	 * @return $this
	 *
	 * @see https://github.com/snuze/snuze/blob/master/src/Reddit/Thing/Subreddit.php for regex
	 */
	public function subreddit(?string $subreddit = null): self
	{
		$pattern = '/^((?:[a-z0-9](?:[a-z0-9_]){2,20})|reddit\.com|ca|de|es|eu|fr|it|ja|nl|pl|ru)$/i';
		if (is_string($subreddit) && ! preg_match($pattern, $subreddit))
		{
			throw new RedditException(lang('Reddit.invalidSubreddit', [$subreddit]));
		}
		$this->subreddit = $subreddit;

		return $this;
	}

	/**
	 * Sets the "after" query parameter.
	 *
	 * @returns $this
	 */
	public function after(?string $after = null): self
	{
		$this->request->setQuery('after', $after);
		if (null !== $after)
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
	public function before(?string $before = null): self
	{
		$this->request->setQuery('before', $before);
		if (null !== $before)
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
	public function count(?int $count = null): self
	{
		$this->request->setQuery('count', $count);

		return $this;
	}

	/**
	 * Sets the "limit" query parameter.
	 *
	 * @returns $this
	 */
	public function limit(?int $limit = null): self
	{
		$this->request->setQuery('limit', $limit);

		return $this;
	}

	/**
	 * Sets the "show" query parameter.
	 *
	 * @returns $this
	 */
	public function show(?string $show = null): self
	{
		$this->request->setQuery('show', $show);

		return $this;
	}
}
