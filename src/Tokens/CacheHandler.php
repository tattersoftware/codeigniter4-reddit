<?php

namespace Tatter\Reddit\Tokens;

use Tatter\Reddit\Exceptions\TokensException;

class CacheHandler implements TokensInterface
{
	/**
	 * Retrieves the access token from cache.
	 *
	 * @param bool $refresh Whether to force a new token request (if applicable)
	 *
	 * @throws TokensException
	 *
	 * @return string The access token
	 */
	public static function retrieve(bool $refresh = false): string
	{
		if ($refresh)
		{
			cache()->delete('reddit_access_token');
		}

		if ($token = cache('reddit_access_token'))
		{
			return $token;
		}

		throw new TokensException('Cached item not available');
	}

	/**
	 * Caches the access token.
	 *
	 * @param string $token The access token
	 */
	public static function store(string $token): void
	{
		cache()->save('reddit_access_token', $token, 3600);
	}
}
