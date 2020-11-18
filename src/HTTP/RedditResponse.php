<?php namespace Tatter\Reddit\HTTP;

use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\TokensException;
use Tatter\Handlers\Interfaces\HandlerInterface;
use JsonException;

/**
 * Reddit Response Class
 *
 * A wrapper around Response to handle
 * some of the Reddit API specifics.
 */
class RedditResponse extends Response
{
	/**
	 * Parses a Reddit API or auth response.
	 *
	 * @param bool $assoc Whether to return an associative array instead of object
	 *
	 * @return array|object
	 *
	 * @throws JsonException, HTTPException
	 */
	public function getResult(bool $assoc = false)
	{
		// Decode the response
		$result = json_decode($this->getBody(), $assoc, 512, JSON_THROW_ON_ERROR);

		// Check for errors
		if (isset($result->error))
		{
			throw new HTTPException($result->error_description ?? $result->error);
		}

		return $assoc ? (array) $result : $result;
	}
}
