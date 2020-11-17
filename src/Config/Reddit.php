<?php namespace Tatter\Reddit\Config;

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
	 * Access token from OAuth.
	 *
	 * @var string
	 */
	public $accessToken = '';

	/**
	 * User Agent to use for API requests.
	 *
	 * @var string
	 */
	public $userAgent = '';

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
