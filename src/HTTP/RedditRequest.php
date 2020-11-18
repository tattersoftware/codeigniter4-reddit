<?php namespace Tatter\Reddit\HTTP;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use CodeIgniter\HTTP\URI;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\TokensException;

/**
 * Reddit Request Class
 *
 * A wrapper around CURLRequest to handle
 * some of the Reddit API specifics.
 *
 * @see https://www.reddit.com/dev/api
 */
class RedditRequest extends CURLRequest
{
	/**
	 * Token handlers in priority order.
	 *
	 * @var string[]
	 */
	public $tokenHandlers;

	/**
	 * Default query parameters to append to the URI.
	 *
	 * - after    Fullname of an item in the listing to use as the anchor point for the beginning of the slice
	 * - before   Fullname of an item in the listing to use as the anchor point for the end of a slice
	 * - count    The number of items already seen in this listing
	 * - limit    The maximum number of items to return in this slice of the listing
	 * - show     Optional parameter; if "all" is passed, filters such as "hide links that I have voted on" will be disabled
	 * - raw_json Opts out of legacy JSON special character escaping
	 *
	 * @var array
	 */
	protected $query = [
		'after'    => null,
		'before'   => null,
		'count'    => null,
		'limit'    => null,
		'show'     => null,
		'raw_json' => 1,
	];

	/**
	 * Query parameters for the next request.
	 *
	 * @var array
	 */
	protected $tempQuery;

	/**
	 * @param RedditConfig $config
	 */
	public function __construct(RedditConfig $config)
	{
		parent::__construct(config('App'), new URI($config->baseURL), new RedditResponse(config('App')), [
			'baseURI'     => $config->baseURL,
			'http_errors' => false,
			'timeout'     => 3,
			'user_agent'  => $config->userAgent,
		]);

		$this->tokenHandlers = $config->tokenHandlers;
		$this->reset();
	}

	//--------------------------------------------------------------------

	/**
	 * Resets query parameters between calls.
	 *
	 * @returns $this
	 */
	public function reset(): self
	{
		$this->tempQuery = $this->query;
		return $this;
	}

	/**
	 * Sets a query parameter value
	 *
	 * @param string $name
	 * @param mixed|null $value
	 *
	 * @returns $this
	 */
	public function setQuery(string $name, $value): self
	{
		$this->tempQuery[$name] = $value;

		return $this;
	}

	/**
	 * Returns the query parameter(s) for the next request.
	 * Mostly for testing.
	 *
	 * @param string $name Optional parameter name filter
	 *
	 * @returns array|mixed|null
	 */
	public function getQuery(string $name = null)
	{
		if (is_null($name))
		{
			return $this->tempQuery;
		}

		return $this->tempQuery[$name] ?? null;
	}

	//--------------------------------------------------------------------

	/**
	 * Sends a cURL request and parses the response.
	 *
	 * @param string $uri      URI segment (relative to baseURL)
	 * @param array|null $data Additional data for the request
	 * @param array $query     Additional query parameters
	 *
	 * @return RedditResponse
	 *
	 * @throws HTTPException, TokensException
	 */
	public function submit(string $uri, $data = null, $query = []): RedditResponse
	{
		// Determine the query parameters, ignoring null values
		$query = array_merge($this->tempQuery, $query);
		$query = array_filter($query, static function($var) {
			return $var !== null;
		});

		// Append the query to the URI
		if (! empty($query))
		{
			$uri .= '?' . http_build_query($query);
		}

		$this->setHeader('Expect', '')->setHeader('Authorization', 'bearer ' . $this->token());

		return is_null($data) ? $this->get($uri) : $this->post($uri, ['form_params' => $data]);
	}

	/**
	 * Retrieves an access token.
	 *
	 * @return string
	 *
	 * @throws TokensException
	 */
	protected function token(): string
	{
		// Try each handler, tracking failures
		$failed = [];
		foreach ($this->tokenHandlers as $class)
		{
			try
			{
				$token = $class::retrieve();
				break;
			}
			catch (TokensException $e)
			{
				$failed[$class] = $e->getMessage();
			}
		}

		// If no token was found then compile the error messages into one
		if (empty($token))
		{
			$messages = [];
			foreach ($failed as $class => $message)
			{
				$messages[] = "$class: $message";
			}

			throw new TokensException(implode(' ', $messages));
		}

		// Try to store the token back to failed handlers
		foreach ($failed as $class => $message)
		{
			$class::store($token);
		}

		return $token;
	}
}
