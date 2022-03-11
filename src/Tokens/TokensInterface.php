<?php

namespace Tatter\Reddit\Tokens;

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
     * @param bool $refresh Whether to force a new token request (if applicable)
     *
     * @throws TokensException
     *
     * @return string The access token
     */
    public static function retrieve(bool $refresh = false): string;

    /**
     * @param string $token The access token
     */
    public static function store(string $token): void;
}
