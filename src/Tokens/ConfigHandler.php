<?php

namespace Tatter\Reddit\Tokens;

use CodeIgniter\Config\Config;
use Tatter\Reddit\Exceptions\TokensException;

class ConfigHandler implements TokensInterface
{
    /**
     * Retrieves the access token from the
     * Config class, usually from getenv.
     *
     * @param bool $refresh Whether to force a new token request (if applicable)
     *
     * @throws TokensException
     *
     * @return string The access token
     */
    public static function retrieve(bool $refresh = false): string
    {
        if ($token = config('Reddit')->accessToken) {
            return $token;
        }

        throw new TokensException('Configuration property not set');
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
