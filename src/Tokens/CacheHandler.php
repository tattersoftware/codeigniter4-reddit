<?php namespace Tatter\Reddit\Tokens;

use Tatter\Reddit\Exceptions\TokensException;

class CacheHandler implements TokensInterface
{
	/**
	 * Retrieves the access token from cache.
	 *
	 * @return string The access token
	 *
	 * @throws TokensException
	 */
	public static function retrieve(): string
	{
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
		cache()->save('reddit_access_token', $token, 0);
	}
}
