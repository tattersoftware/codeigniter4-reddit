<?php

namespace Tatter\Reddit\HTTP;

use CodeIgniter\HTTP\Message;
use CodeIgniter\HTTP\Response;
use JsonException;
use Tatter\Reddit\Exceptions\RedditException;

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
     * @throws JsonException
     * @throws RedditException
     *
     * @return array|object
     */
    public function getResult(bool $assoc = false)
    {
        if (null === $this->result) {
            // Check for failure status
            if ($this->getStatusCode() >= 300) { // @phpstan-ignore-line
                throw new RedditException(
                    lang('Reddit.failedResponse', [static::$statusCodes[$this->statusCode]]),
                    $this->statusCode
                );
            }

            // Decode the response
            try {
                $result = json_decode($this->getBody(), false, 512, JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                throw new RedditException($e->getMessage(), $e->getCode(), $e);
            }

            // Check for errors
            if (isset($result->error)) {
                $message = $result->message ?? $result->error_description ?? $result->error;

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
     * @throws RedditException
     *
     * @return mixed
     */
    public function getResultPath(string $path)
    {
        // If no result then try to get one on-the-fly
        $object = $this->getResult();

        foreach (explode('/', $path) as $segment) {
            if (! isset($object->{$segment})) {
                throw new RedditException(lang('Reddit.unverifiedPath', [$segment, $path]));
            }

            $object = $object->{$segment};
        }

        return $object;
    }

    /**
     * Resets the stored $result anytime $body changes
     *
     * @param mixed $data
     *
     * @return $this
     *
     * @psalm-suppress MethodSignatureMismatch
     */
    public function setBody($data): Message
    {
        $this->result = null;

        return parent::setBody($data);
    }
}
