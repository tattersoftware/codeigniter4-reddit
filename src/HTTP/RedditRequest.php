<?php namespace Tatter\Reddit\HTTP;

use CodeIgniter\HTTP\CURLRequest;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\TokensException;
use Tatter\Handlers\Interfaces\HandlerInterface;
use JsonException;

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
	 * Fullname of an item in the listing to use
	 * as the anchor point for the beginning of the slice.
	 *
	 * @var string|null
	 */
	protected $after;

	/**
	 * Fullname of an item in the listing to use
	 * as the anchor point for the end of a slice.
	 *
	 * @var string|null
	 */
	protected $before;

	/**
	 * The maximum number of items to return in
	 * this slice of the listing.
	 *
	 * @var int|null
	 */
	protected $limit;

	/**
	 * The number of items already seen in this listing.
	 *
	 * @var int|null
	 */
	protected $count;

	/**
	 * Optional parameter; if "all" is passed, filters such as
	 * "hide links that I have voted on" will be disabled.
	 *
	 * @var string|null
	 */
	protected $show;

	/**
	 * @param RedditConfig $config
	 */
	public function __construct(RedditConfig $config)
	{
		parent::__construct(config('App'), new URI($config->baseURL), new RedditResponse(), [
			'baseURI'     => $this->config->baseURL,
			'user_agent'  => $this->config->userAgent,
			'http_errors' => false,
			'timeout'     => 3,
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
	 * @param string $uri      URI segment (relative to baseURL)
	 * @param array $query     Query parameters to append to the URI
	 * @param array|null $data Additional data for the request
	 *
	 * @return RedditResponse
	 *
	 * @throws HTTPException, TokensException
	 */
	public function submit(string $uri, $query = [], $data = null): RedditResponse
	{
		// Apply JSON format override
		$query['raw_json'] = 1;

		// Append any queries to the URI
		$uri .= '?' . http_build_query($query);

		$this->setHeader('Expect', '')->setHeader('Authorization', 'bearer ' . $this->token());

		return is_null($data) ? $this->get($uri) : $this->post($uri, ['form_params' => $data]);
	}
}
