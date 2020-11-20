<?php namespace Tatter\Reddit\Structures;

use Tatter\Reddit\Exceptions\RedditException;
use stdClass;

/**
 * Kind Abstract Class
 *
 * Base class for all Things that
 * use a specified `kind` prefix.
 *
 * @see https://github.com/reddit-archive/reddit/wiki/JSON
 */
abstract class Kind extends Thing
{
	/**
	 * Regex to validate full names.
	 */
	const NAME_REGEX = '/^t[1-5]_[A-Za-z0-9]{1,13}$/';

	/**
	 * Class handlers for each prefix.
	 */
	const CLASSES = [
		't1' => Comment::class,
		't2' => Account::class,
		't3' => Link::class,
		't4' => Message::class,
		't5' => Subreddit::class,
		't6' => Award::class,
	];

	/**
	 * ID portion of the full name.
	 * E.g. 15bfi0 of t1_15bfi0
	 *
	 * @var string
	 */
	protected $id;

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
		parent::__construct($input);

		// Extract the ID from data->name
		$this->id = explode('_', $input->data->name)[1];
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
		parent::validate($input);

		// Additional validation
		$error = '';
		if (! isset(static::CLASSES[$input->kind]))
		{
			$error = lang('Reddit.kindUnknownPrefix', [$input->kind]);
		}
		elseif ($input->kind !== $this->kind)
		{
			$error = lang('Reddit.kindMismatchedPrefix', [$input->kind, $this->kind]);
		}
		elseif (! isset($input->data->name))
		{
			$error = lang('Reddit.kindMissingName');
		}
		elseif (! is_string($input->data->name) || ! preg_match(self::NAME_REGEX, $input->data->name))
		{
			$error = lang('Reddit.kindInvalidName', [(string) $input->data->name]);
		}

		if ($error)
		{
			throw new RedditException($error);
		}
	}

	//--------------------------------------------------------------------

	/**
	 * Returns the full name.
	 * Prefix + underscore + UID
	 *
	 * @return string
	 */
	public function name(): string
	{
		return $this->kind . '_' . $this->id;
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
		return (int) base_convert($this->id, 36, 10);
	}
}
