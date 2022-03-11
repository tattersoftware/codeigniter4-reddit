<?php

namespace Tatter\Reddit\Tokens;

use CodeIgniter\HTTP\Exceptions\HTTPException;
use Tatter\Reddit\Exceptions\TokensException;
use Tatter\Reddit\HTTP\RedditRequest;
use Tatter\Reddit\HTTP\RedditResponse;
use Tatter\Reddit\Reddit;
use Throwable;

class PasswordHandler implements TokensInterface
{
	/**
	 * Retrieves a new access token from the
	 * Reddit OAuth endpoint.
	 *
	 * @param bool $refresh Whether to force a new token request (if applicable)
	 *
	 * @throws TokensException
	 *
	 * @return string The access token
	 */
	public static function retrieve(bool $refresh = false): string
	{
		$config = config('Reddit');
		$curl   = (new RedditRequest($config))
		    ->setHeader('Expect', '')
		    ->setAuth($config->clientId, $config->clientSecret);

		// Execute the cURL request
		try {
			/** @var RedditResponse $response */
			$response = $curl->post($config->tokenURL, [
				'form_params' => [
					'grant_type' => 'password',
					'username'   => $config->username,
					'password'   => $config->password,
				],
			]);
		} catch (HTTPException $e)
		{
			throw new TokensException($e->getMessage(), $e->getCode(), $e);
		}

		// Decode the response
		try {
			$result = $response->getResult();
		} catch (Throwable $e)
		{
			throw new TokensException($e->getMessage(), $e->getCode(), $e);
		}

		if (empty($result->access_token))
		{
			throw new TokensException('Indecipherable response: ' . $response->getBody());
		}

		return $result->access_token;
	}

	/**
	 * Not relevent.
	 *
	 * @param string $token The access token
	 */
	public static function store(string $token): void
	{

	}
}
