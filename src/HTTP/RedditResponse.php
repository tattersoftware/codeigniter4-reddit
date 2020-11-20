<?php namespace Tatter\Reddit\HTTP;

use CodeIgniter\HTTP\Response;
use CodeIgniter\HTTP\Exceptions\HTTPException;
use Config\Services;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Exceptions\RedditException;
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
	 * Stored version of the result.
	 *
	 * @var array|object|null
	 */
	protected $result;

	/**
	 * Parses a Reddit API or auth response.
	 *
	 * @param bool $assoc Whether to return an associative array instead of object
	 *
	 * @return array|object
	 *
	 * @throws RedditException, JsonException
	 */
	public function getResult(bool $assoc = false)
	{
		if (is_null($this->result))
		{
			// Decode the response
			$result = json_decode($this->getBody(), false, 512, JSON_THROW_ON_ERROR);

			// Check for errors
			if (isset($result->error))
			{
				$message = $result->message ?? $result->error_description ?? $result->error; // @phpstan-ignore-line
				throw new RedditException(lang('Reddit.errorResponse', [$message]));
			}

			$this->result = $result;
		}

		return $assoc ? (array) $this->result : $this->result;
	}

	/**
	 * Verifies the object path exists and returns the content.
	 *
	 * @param string $path Result properites in URI format, e.g. 'data/segment/part'
	 *
	 * @return mixed
	 *
	 * @throws RedditException
	 */
	public function getResultPath(string $path)
	{
		// If no result then try to get one on-the-fly
		$object = $this->getResult();
		foreach (explode('/', $path) as $segment)
		{
			if (! isset($object->$segment))
			{
				throw new RedditException(lang('Reddit.unverifiedPath', [$segment, $path]));
			}

			$object = $object->$segment;
		}

		return $object;
	}

	/**
	 * Resets the stored $result anytime $body changes
	 *
	 * @param mixed $data
	 *
	 * @return $this
	 */
	public function setBody($data): self
	{
		$this->result = null;

		return parent::setBody($data);
	}
}
