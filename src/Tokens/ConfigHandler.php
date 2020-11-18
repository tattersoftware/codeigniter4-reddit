<?php namespace Tatter\Reddit\Tokens;

use CodeIgniter\Config\Config;
use Tatter\Reddit\Exceptions\TokensException;

class ConfigHandler implements TokensInterface
{
	/**
	 * Retrieves the access token from the
	 * Config class, usually from getenv.
	 *
	 * @return string The access token
	 *
	 * @throws TokensException
	 */
	public static function retrieve(): string
	{
		if ($token = config('Reddit')->accessToken)
		{
			return $token;
		}

		throw new TokensException('Configuration property not set');
	}

	/**
	 * Inject the token into the config instance.
	 *
	 * @param string $token The access token
	 */
	public static function store(string $token): void
	{
		$config = config('Reddit');
		$config->accessToken = $token;
		Config::injectMock('Reddit', $config);
	}
}
