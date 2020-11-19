<?php namespace Tatter\Reddit\Structures;

use ArrayObject;
use Tatter\Reddit\Exceptions\RedditException;

/**
 * Listing Class
 *
 * Collection of Things with API-aware handling.
 *
 * @see https://github.com/reddit-archive/reddit/wiki/JSON#listing
 */
class Listing extends ArrayObject
{
	/**
	 * Fullname of the listing that follows after this page.
	 * null if there is no next page.
	 *
	 * @var string|null
	 */
	protected $after;

	/**
	 * Fullname of the listing that follows before this page.
	 * null if there is no previous page.
	 *
	 * @var string|null
	 */
	protected $before;

	//--------------------------------------------------------------------

	/**
	 * Extracts Listing fields and applies its
	 * children to the ArrayObject
	 *
	 * @param object $input Result of kind "Listing" from RedditResponse
	 *
	 * @throws RedditException
	 */
	public function __construct(object $input)
	{
		// Validate
		if (! isset($input->kind)
			|| $input->kind !== 'Listing'
			|| ! isset($input->data)
			|| ! is_object($input->data)
			|| ! isset($input->data->children)
			|| ! is_array($input->data->children)
		)
		{
			throw new RedditException(lang('Reddit.invalidListing'));
		}

		$this->after  = $input->after ?? null;
		$this->before = $input->before ?? null;

		parent::construct($input->data->children);
	}

	/**
	 * Returns the name of this kind
	 *
	 * @return string
	 */
    public function kindName(): string
    {
    	return 'Listing';
    }
}
