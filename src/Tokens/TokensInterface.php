<?php namespace Tatter\Reddit\Tokens;

use Tatter\Reddit\Exceptions\TokensException;

/**
 * Tokens Interface
 *
 * Interface to define retrieval of
 * API access tokens.
 */
interface TokensInterface
{
	/**
	 * @return string The access token
	 *
	 * @throws TokensException
	 */
	public static function retrieve(): string;

	/**
	 * @param string $token The access token
	 */
	public static function store(string $token): void;
}
