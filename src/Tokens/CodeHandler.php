<?php namespace Tatter\Reddit\Tokens;

use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;
use Tatter\Reddit\Exceptions\TokensException;

class CodeHandler implements TokensInterface
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
		$config = config('Reddit');
		$curl   = Services::curlrequest()
			->setHeader('Expect', '')
			->setAuth($config->clientId, $config->clientSecret)
			->setBody(http_build_query([
				'grant_type' => 'password',
				'username'   => $config->username,
				'password'   => $config->password,
			]));

		try
		{
			$response = $curl->post($config->tokenURL, [
				'http_errors' => false,
			]);
		}
		catch (HTTPException $e)
		{
			throw new TokensException($e->getMessage(), $e->getCode(), $e);
		}

		$body = json_decode($response->getBody(), true, 512, JSON_THROW_ON_ERROR);

		dd($body);

		throw new TokensException('Authentication failed: ');
	}

	/**
	 * Not relevent.
	 *
	 * @param string $token The access token
	 */
	public static function store(string $token): void
	{
		return;
	}
}
