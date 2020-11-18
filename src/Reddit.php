<?php namespace Tatter\Reddit;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\TokensException;
use Tatter\Handlers\Interfaces\HandlerInterface;
use JsonException;

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
	 * Name of the current subreddit
	 *
	 * @var string
	 */
	protected $subreddit;

	/**
	 * CURL client preconfigured for API calls
	 *
	 * @var CURLRequest
	 */
	protected $curl;

	/**
	 * Initializes the library.
	 *
	 * @param HandlersConfig|null $config
	 * @param CacheInterface|null $cache
	 */
	public function __construct(RedditConfig $config)
	{
		$this->config    = $config;
		$this->subreddit = $config->subreddit;
		$this->curl      = Services::curlrequest([
			'baseURI'     => $this->config->baseURL,
			'user_agent'  => $this->config->userAgent,
			'http_errors' => false,
			'timeout'     => 3,
		]);
	}

	//--------------------------------------------------------------------

	/**
	 * Parses a Reddit API or auth response.
	 *
	 * @param ResponseInterface
	 *
	 * @return array
	 *
	 * @throws JsonException, HTTPException
	 *
	 * @todo Should move somewhere else, maybe a Response superset?
	 */
	public static function parseResponse(ResponseInterface $response): array
	{
		// Decode the response
		$body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

		// Check for errors
		if (isset($body['error']))
		{
			throw new HTTPException($body['error_description'] ?? $body['error']);
		}

		return $body;
	}

	//--------------------------------------------------------------------

	/**
	 * @return string
	 */
	public function getSubreddit(): string
	{
		return $this->subreddit;
	}

	/**
	 * @param string $subreddit
	 *
	 * @return $this
	 */
	public function setSubreddit(string $subreddit): self
	{
		$this->subreddit = $subreddit;

		return $this;
	}

	//--------------------------------------------------------------------

	/**
	 * Fetches the most recent comments
	 *
	 * @param string $subreddit
	 *
	 * @return $this
	 */
	public function fetchComments($sort = 'new', $limit = 50): array
	{
		$uri = '/r/' . $this->getSubreddit() . '/comments';

		return $this->send($uri, [
			'cb'    => time(),
			'sort'  => $sort,
			'limit' => $limit,
		]);
	}

	//--------------------------------------------------------------------

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
		foreach ($this->config->tokenHandlers as $class)
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

	/**
	 * Sends a cURL request and parses the response.
	 *
	 * @param string $uri      URI segment relative to baseURL
	 * @param array $query     Query parameters to append to the URI
	 * @param array|null $data Additional data for the request
	 *
	 * @return ResponseInterface
	 *
	 * @throws HTTPException, TokensException
	 */
	protected function send($uri, $query = [], $data = null): ResponseInterface
	{
		// Apply JSON format override
		$query['raw_json'] = 1;

		// Append any queries to the URI
		$uri .= '?' . http_build_query($query);

		$this->curl
			->setHeader('Expect', '')
			->setHeader('Authorization', 'bearer ' . $this->token());

		$response = is_null($data) ? $this->curl->get($uri) : $this->curl->post($uri, ['form_params' => $data]);

		$result = self::parseResponse($response);

		dd($result);
	}
}
