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
	 * Sets class data from API input.
	 *
	 * @param object $input
	 */
	public function __construct(object $input);
}
