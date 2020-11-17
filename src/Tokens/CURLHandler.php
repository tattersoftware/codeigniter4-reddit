<?php namespace Tatter\Reddit\Tokens;

use Config\Services;
use Tatter\Reddit\Exceptions\TokensException;

class CURLHandler implements TokensInterface
{
	/**
	 * Retrieves a new access token from the
	 * Reddit OAuth endpoint.
	 *
	 * @return string The access token
	 *
	 * @throws TokensException
	 */
	public static function retrieve(): string
	{
		$curl   = Services::curlrequest();
		$config = config('Reddit');

		throw new TokensException('Authentication failed: ');

		$response = $curl->setHeader('Expect', '')
			->setAuth($config->clientId, $config->clientSecret)
			->setBody(http_build_query([
				'grant_type' => 'password',
				'username' => $config->username,
				'password' => $config->password,
			]))
			->post($config->tokenURL);

		dd($response);
	}

	/**
	 * Not relevent.
	 *
	 * @param string $token The access token
	 *
	 * @throws TokensException
	 */
	public static function store(string $token): void
	{
		return;
	}
}
