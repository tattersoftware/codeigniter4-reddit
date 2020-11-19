<?php namespace Tatter\Reddit\Structures;

/**
 * Kind Interface
 *
 * Common interface for identifying anything
 * returned from a successful API request.
 */
interface Kind
{
	/**
	 * Returns the name of this kind, e.g. "Comment".
	 *
	 * @return string
	 */
    public function kindName(): string;

	/**
	 * Sets class data from API input.
	 *
	 * @param object $input
	 */
	public function __construct(object $input);
}
