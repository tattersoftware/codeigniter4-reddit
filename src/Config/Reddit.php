<?php

namespace Tatter\Reddit\Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Reddit Config
 *
 * Provides properties for the credentials and
 * API endpoints used to make the connection.
 *
 * NOTE: Sensitive information should be placed
 * in .env instead of this file.
 */
class Reddit extends BaseConfig
{
	/**
	 * A default subreddit to apply to requests.
	 *
	 * @var string|null
	 */
	public $subreddit;

	/**
	 * Token handlers in priority order.
	 *
	 * @var string[]
	 */
	public $tokenHandlers = [
		'Tatter\Reddit\Tokens\MemoryHandler',
		'Tatter\Reddit\Tokens\ConfigHandler',
		'Tatter\Reddit\Tokens\CacheHandler',
		'Tatter\Reddit\Tokens\PasswordHandler',
	];

	/**
	 * API client ID.
	 *
	 * @var string
	 */
	public $clientId = '';

	/**
	 * API secret key.
	 *
	 * @var string
	 */
	public $clientSecret = '';

	/**
	 * Username for authenticating the API.
	 *
	 * @var string
	 */
	public $username = '';

	/**
	 * Password for authenticating the API.
	 *
	 * @var string
	 */
	public $password = '';

	/**
	 * Access token from OAuth.
	 *
	 * @var string
	 */
	public $accessToken = '';

	/**
	 * User Agent to use for API requests. Format:
	 * <platform>:<app ID>:<version string> (by /u/<reddit username>)
	 *
	 * @var string
	 */
	public $userAgent = 'web:com.tattersoftware.reddit:v1.0.0 (by /u/mgatner)';

	/**
	 * Return URL after authentication.
	 *
	 * @var string
	 */
	public $redirectURL = '';

	/**
	 * URL for acquiring an access token.
	 *
	 * @var string
	 */
	public $tokenURL = 'https://www.reddit.com/api/v1/access_token';

	/**
	 * Base URL for API calls.
	 *
	 * @var string
	 */
	public $baseURL = 'https://oauth.reddit.com';
}
