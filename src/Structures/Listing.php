<?php namespace Tatter\Reddit\Structures;

use Iterator;
use Tatter\Reddit\Exceptions\RedditException;

/**
 * Listing Class
 *
 * Collection of Things with API-aware handling.
 *
 * @see https://github.com/reddit-archive/reddit/wiki/JSON#listing
 */
class Listing extends Kind implements Iterator
{
	/**
	 * Fullname of the listing that follows after this page.
	 * null if there is no next page.
	 *
	 * @var string|null
	 */
	public $after;

	/**
	 * Fullname of the listing that follows before this page.
	 * null if there is no previous page.
	 *
	 * @var string|null
	 */
	public $before;

	/**
	 * Children from API result.
	 *
	 * @var array
	 */
	protected $children;

	//--------------------------------------------------------------------

	/**
	 * Extracts Listing fields and stores its children
	 *
	 * @param object $input Result of kind "Listing" from RedditResponse
	 *
	 * @throws RedditException
	 */
	public function __construct(object $input)
	{
		$this->validate($input);

		$this->after    = $input->after ?? null;
		$this->before   = $input->before ?? null;
		$this->children = $input->data->children;
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
		if (! isset($input->kind)
			|| $input->kind !== 'Listing'
			|| ! isset($input->data)
			|| ! is_object($input->data)
			|| ! isset($input->data->children)
			|| ! is_array($input->data->children)
		)
		{
			throw new RedditException(lang('Reddit.invalidKindInput'));
		}
	}

	//--------------------------------------------------------------------
	// ITERATOR METHODS
	//--------------------------------------------------------------------

	/**
	 * @return Thing|false
	 */
	public function current()
	{
		if ($input = current($this->children))
		{
			return Thing::create($input);
		}

		return false;
	}

	/**
	 * @return int
	 */
	public function key(): int
	{
		return key($this->children);
	}

	public function next(): void
	{
		next($this->children);
	}

	public function rewind(): void
	{
		reset($this->children);
	}

	/**
	 * @return bool
	 */
	public function valid(): bool
	{
		$key = key($this->children);

		return ($key !== null && $key !== null);
	}

}
