<?php namespace Tatter\Reddit\Things;

use Tatter\Reddit\Exceptions\RedditException;

/**
 * Thing Abstract Class
 *
 * Base class for all API return casts.
 *
 * @see https://www.reddit.com/dev/api#fullnames
 */
abstract class Thing
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
	 * All the data from the API
	 *
	 * @var array|null
	 */
	protected $data;

	//--------------------------------------------------------------------

	/**
	 * Creates a new child class a name.
	 *
	 * @param string $name
	 *
	 * @return static
	 *
	 * @throws RedditException
	 */
    public static function fromName(string $name): self
    {
    	// Verify the name
    	if (! preg_match(self::REGEX, $name))
    	{
    		throw new RedditException(lang('Reddit.invalidThingName', [$name]));
    	}

		// WIP
    }

	/**
	 * Creates a new child class from API data.
	 *
	 * @param array|object $data
	 *
	 * @return static
	 */
    public static function fromData($data): self
    {
		// WIP
    }

	//--------------------------------------------------------------------

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
	 * Returns the name of this kind, e.g. "Comment".
	 *
	 * @return string
	 */
    public function kindName(): string
    {
    	$class = self::KINDS[$this->kind];

		return substr($class, strrpos($class, '\\') + 1)
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
	 * Returns the full name of this Thing.
	 * Prefix + underscore + UID
	 *
	 * @return string
	 */
    public function __toString(): string
    {
		return $this->kind . '_' . $this->id;
    }

	//--------------------------------------------------------------------

	/**
	 * Sets the data. Usually only used during creation.
	 *
	 * @param array|object|null $data
	 *
	 * @return $this
	 */
    public function setData($data = null): self
    {
    	$this->data = is_object($data) ? (array) $data : $data;

		return $this;
    }

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
		if (! )
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
