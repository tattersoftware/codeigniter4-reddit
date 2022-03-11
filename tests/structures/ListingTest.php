<?php

namespace Tatter\Reddit\Structures;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;

/**
 * @internal
 */
final class ListingTest extends CIUnitTestCase
{
    /**
     * @var string
     */
    private $input = '{"kind":"Listing", "data":{"children":[{"kind":"t3", "data":{"subreddit":"pythonforengineers","name":"t3_jw6u2r"}}], "after":"t3_abcdefg"}}';

    /**
     * @dataProvider queryParameterProvider
     *
     * @param mixed $input
     * @param mixed $error
     */
    public function testConstructValidates($input, $error)
    {
        $object = json_decode($input, false, 512, JSON_THROW_ON_ERROR);

        if ($error) {
            $this->expectException(RedditException::class);
            $this->expectExceptionMessage($error);
        }

        $listing = new Listing($object);

        // Invalid listings fail before this
        $this->assertInstanceOf(Listing::class, $listing);
    }

    public function queryParameterProvider()
    {
        return [
            [$this->input, ''], // valid
            ['{"data":{"children":[{"bar":"bam"},{"foo":"baz"}]}}', lang('Reddit.thingMissingKind')], // missing kind
            ['{"kind":"listing", "data":{"children":[{"bar":"bam"},{"foo":"baz"}]}}', lang('Reddit.kindMismatchedPrefix', ['listing', 'Listing'])], // lowercase kind
            ['{"kind":"Comment", "data":{"children":[{"bar":"bam"},{"foo":"baz"}]}}', lang('Reddit.kindMismatchedPrefix', ['Comment', 'Listing'])], // different kind
            ['{"kind":"Listing", "data":"bam"}', lang('Reddit.thingInvalidData')], // data not object
            ['{"kind":"Listing", "data":{"boo":[{"bar":"bam"},{"foo":"baz"}]}}', lang('Reddit.listingMissingChildren')], // data missing children
            ['{"kind":"Listing", "data":{"children":{"bar":"bam"}}}', lang('Reddit.listingInvalidChildren')], //  data children not array
        ];
    }

    public function testConstructSetsAfter()
    {
        $listing = new Listing(json_decode($this->input));

        $this->assertSame('t3_abcdefg', $listing->after);
    }

    public function testIterationCreatesThings()
    {
        $listing = new Listing(json_decode($this->input));

        foreach ($listing as $thing) {
            $this->assertInstanceOf(Thing::class, $thing);
        }
    }
}
