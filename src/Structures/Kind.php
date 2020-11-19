<?php namespace Tatter\Reddit\Structures;

use Tatter\Reddit\Exceptions\RedditException;

/**
 * Abstract Class Kind
 *
 * Common class for identifying anything
 * returned from a successful API request
 * and returning its appropriate child class.
 */
abstract class Kind
{
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
		if (! isset($input->kind))
		{
			throw new RedditException(lang('Reddit.invalidListing'));
		}

		if ($input->kind === 'Listing')
		{
			return new Listing($input);
		}

		if (! isset(Thing::KINDS[$input->kind]))
		{
			throw new RedditException(lang('Reddit.invalidListing'));
		}

		// Create the child class
		$class = Thing::KINDS[$input->kind];
		return new $class($input);
	}

	/**
	 * Sets class data from API input.
	 *
	 * @param object $input
	 */
	abstract public function __construct(object $input);

	/**
	 * Validates API input for the particular class.
	 *
	 * @param object $input
	 *
	 * @throws RedditException
	 */
	abstract protected function validate(object $input);
}
