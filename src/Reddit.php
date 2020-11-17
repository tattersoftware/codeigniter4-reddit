<?php namespace Tatter\Reddit;

use CodeIgniter\Cache\CacheInterface;
use CodeIgniter\HTTP\CURLRequest;
use Config\Services;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Handlers\Interfaces\HandlerInterface;

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
	public function __construct(RedditConfig $config, CacheInterface $cache)
	{
		$this->config = $config;
		$this->cache  = $cache;
		$this->curl   = Services::curlrequest([
			'baseURI' => $this->config->baseURL,
			'timeout' => 3,
		]);
	}
}
