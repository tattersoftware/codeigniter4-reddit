<?php namespace Tatter\Reddit\Structures;

use Tatter\Reddit\Exceptions\RedditException;

/**
 * Class Thing
 *
 * Common class for anything returned from a
 * successful API request.
 */
class Thing
{
	/**
	 * Thing prefix (see Kind::CLASSES), "Listing", or "Thing".
	 *
	 * @var string
	 */
	protected $kind;

	/**
	 * Data from the API
	 *
	 * @var array|null
	 */
	protected $data;

	/**
	 * Creates a new child class from API data.
	 *
	 * @param object $input
	 *
	 * @return static
	 *
	 * @throws RedditException
	 */
	public static function create(object $input): self
	{
		// If there is no Kind then wrap it into a generic Thing
		if (! isset($input->kind))
		{
			return self::__construct((object) [
				'kind' => 'Thing',
				'data' => $input
			]);
		}

		if ($input->kind === 'Listing')
		{
			return new Listing($input);
		}

		if (! isset(Kind::CLASSES[$input->kind]))
		{
			throw new RedditException(lang('Reddit.kindUnknownPrefix', [$input->kind]));
		}

		// Create the child class
		$class = Kind::CLASSES[$input->kind];
		return new $class($input);
	}

	/**
	 * Stores class data from API input.
	 *
	 * @param object $input
	 */
	public function __construct(object $input)
	{
		$this->validate($input);

		// Store $data and any additional properties
		foreach ($input as $key => $value)
		{
			$this->$key = $value;
		}
	}

	/**
	 * Validates API input.
	 * Usually extended by a child class.
	 *
	 * @param object $input
	 *
	 * @throws RedditException
	 */
	protected function validate(object $input)
	{
		$error = '';
		if (! isset($input->kind))
		{
			$error = lang('Reddit.thingMissingKind');
		}
		elseif (! isset($input->data))
		{
			$error = lang('Reddit.thingMissingData');
		}
		elseif (! is_object($input->data))
		{
			$error = lang('Reddit.thingInvalidData');
		}

		if ($error)
		{
			throw new RedditException($error);
		}
	}

	/**
	 * Returns the kind (name prefix).
	 *
	 * @return string
	 */
	public function kind(): string
	{
		return $this->kind;
	}

	//--------------------------------------------------------------------

	/**
	 * Magic getter for $data values.
	 *
	 * @param string $key
	 *
	 * @return mixed
	 * @throws RedditException
	 */
	public function __get(string $key)
	{
		if (! $this->__isset($key))
		{
			throw new RedditException(lang('Reddit.missingThingKey', [
				$key,
				$this->__toString(),
			]));
		}

		return $this->data[$key];
	}

	/**
	 * Returns true if $data has a property named $key
	 *
	 * @param string $key
	 *
	 * @return boolean
	 */
	public function __isset(string $key): bool
	{
		return array_key_exists($key, $this->data);
	}

	/**
	 * Returns the basename of the class.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		$class = get_class($this);

		return substr($class, strrpos($class, '\\') + 1);
	}
}
