<?php

namespace Tatter\Reddit\Structures;

use Iterator;
use ReturnTypeWillChange;
use Tatter\Reddit\Exceptions\RedditException;

/**
 * Listing Class
 *
 * Collection of Things with API-aware handling.
 *
 * @see https://github.com/reddit-archive/reddit/wiki/JSON#listing
 */
class Listing extends Thing implements Iterator
{
    /**
     * Children from API result.
     *
     * @var array
     */
    protected $children;

    //--------------------------------------------------------------------

    /**
     * Validates API input.
     *
     * @throws RedditException
     */
    protected function validate(object $input)
    {
        parent::validate($input);

        // Additional validation
        $error = '';
        if ($input->kind !== 'Listing') {
            $error = lang('Reddit.kindMismatchedPrefix', [$input->kind, 'Listing']);
        } elseif (! isset($input->data->children)) {
            $error = lang('Reddit.listingMissingChildren');
        } elseif (! is_array($input->data->children)) {
            $error = lang('Reddit.listingInvalidChildren');
        }

        if ($error) {
            throw new RedditException($error);
        }
    }

    //--------------------------------------------------------------------
    // ITERATOR METHODS
    //--------------------------------------------------------------------

    /**
     * @return false|Thing
     */
    #[ReturnTypeWillChange]
    public function current()
    {
        if ($input = current($this->data->children)) {
            return Thing::create($input);
        }

        return false;
    }

    #[ReturnTypeWillChange]
    public function key(): int
    {
        return key($this->data->children);
    }

    #[ReturnTypeWillChange]
    public function next(): void
    {
        next($this->data->children);
    }

    #[ReturnTypeWillChange]
    public function rewind(): void
    {
        reset($this->data->children);
    }

    #[ReturnTypeWillChange]
    public function valid(): bool
    {
        $key = key($this->data->children);

        return $key !== null && $key !== null;
    }
}
