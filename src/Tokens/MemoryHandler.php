<?php namespace Tatter\Reddit\Tokens;

use Tatter\Reddit\Exceptions\TokensException;

class MemoryHandler implements TokensInterface
{
	/**
	 * @var string The access token
	 */
	protected static $token = '';

	/**
	 * Retrieves the access token from this class.
	 *
	 * @return string The access token
	 *
	 * @throws TokensException
	 */
	public static function retrieve(): string
	{
		if (empty(self::$token))
		{
			throw new TokensException('Token not in memory.');
		}

		return self::$token;
	}

	/**
	 * Stores the access token in this class.
	 *
	 * @param string $token The access token
	 *
	 * @throws TokensException
	 */
	public static function store(string $token): void
	{
		self::$token = $token;
	}
}
