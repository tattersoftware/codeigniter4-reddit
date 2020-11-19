<?php namespace Tatter\Reddit\Structures;

use Tatter\Reddit\Exceptions\RedditException;
use stdClass;

/**
 * Thing Abstract Class
 *
 * Base class for all API return casts.
 *
 * @see https://github.com/reddit-archive/reddit/wiki/JSON
 */
abstract class Thing extends Kind
{
	/**
	 * Class handlers for each prefixed kind.
	 */
	const KINDS = [
		't1' => Comment::class,
		't2' => Account::class,
		't3' => Link::class,
		't4' => Message::class,
		't5' => Subreddit::class,
		't6' => Award::class,
	];

	/**
	 * Regex for full valid names.
	 */
	const REGEX = '/^t[1-5]_[A-Za-z0-9]{1,13}$/';

	/**
	 * Thing prefix kind (see KINDS).
	 * Set by child classes.
	 *
	 * @var string
	 */
	protected $kind;

	/**
	 * ID portion of the fullname.
	 * E.g. 15bfi0 of t1_15bfi0
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Data from the API
	 *
	 * @var array|null
	 */
	protected $data;

	//--------------------------------------------------------------------

	/**
	 * Sets the data from API input.
	 *
	 * @param object $input
	 *
	 * @return $this
	 */
	public function __construct(object $input = null)
	{
		$this->validate($input);

		$this->data = $input->data;
		$this->id   = explode('_', $input->data->name)[1];
	}

	/**
	 * Validates API input.
	 *
	 * @param object $input
	 *
	 * @throws RedditException
	 */
	protected function validate(object $input)
	{
		// Validate
		if (! isset($input->kind)
			|| ! isset(static::KINDS[$input->kind])
			|| $input->kind !== $this->kind
			|| ! isset($input->data)
			|| ! is_object($input->data)
			|| ! isset($input->data->name)
			|| ! is_string($input->data->name)
			|| ! preg_match(self::REGEX, $input->data->name)
		)
		{
dd($input);
			throw new RedditException(lang('Reddit.invalidKindInput'));
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the full name of this Thing.
	 * Prefix + underscore + UID
	 *
	 * @return string
	 */
	public function name(): string
	{
		return $this->kind . '_' . $this->id;
	}

	/**
	 * Returns the prefix kind.
	 *
	 * @return string
	 */
	public function kind(): string
	{
		return $this->kind;
	}

	/**
	 * Returns the prefix-less ID.
	 *
	 * @return string
	 */
	public function id(): string
	{
		return $this->id;
	}

	/**
	 * Returns the base 36 ID in its integer form
	 *
	 * @return int
	 */
	public function int(): int
	{
		return base_convert($this->id, 36, 10);
	}

	/**
	 * Returns the name of this Thing's kind.
	 *
	 * @return string
	 */
	public function __toString(): string
	{
		$class = self::KINDS[$this->kind];

		return substr($class, strrpos($class, '\\') + 1);
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
				$this->kindName(),
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
}
