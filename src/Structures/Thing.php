<?php

namespace Tatter\Reddit\Structures;

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
	 * @var object|null
	 */
	protected $data;

	/**
	 * Creates a new child class from API data.
	 *
	 * @throws RedditException
	 */
	public static function create(object $input): self
	{
		// If there is no Kind then wrap it into a generic Thing
		if (! isset($input->kind))
		{
			return new self((object) [
				'kind' => 'Thing',
				'data' => $input,
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
	 */
	public function __construct(object $input)
	{
		$this->validate($input);

		// Store $data and any additional properties
		foreach ($input as $key => $value)
		{
			$this->{$key} = $value;
		}
	}

	/**
	 * Validates API input.
	 * Usually extended by a child class.
	 *
	 * @throws RedditException
	 */
	protected function validate(object $input)
	{
		$error = '';
		if (! isset($input->kind))
		{
			$error = lang('Reddit.thingMissingKind');
		} elseif (! isset($input->data))
		{
			$error = lang('Reddit.thingMissingData');
		} elseif (! is_object($input->data))
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
	 */
	public function kind(): string
	{
		return $this->kind;
	}

	//--------------------------------------------------------------------

	/**
	 * Magic getter for $data values.
	 *
	 * @throws RedditException
	 *
	 * @return mixed
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

		return $this->data->{$key};
	}

	/**
	 * Returns true if $data has a property named $key
	 */
	public function __isset(string $key): bool
	{
		return property_exists($this->data, $key);
	}

	/**
	 * Returns the basename of the class.
	 */
	public function __toString(): string
	{
		$class = static::class;

		return substr($class, strrpos($class, '\\') + 1);
	}
}
