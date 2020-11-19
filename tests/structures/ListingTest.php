<?php namespace Tatter\Reddit\Structures;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;

class ListingTest extends CIUnitTestCase
{
	/**
	 * @var string
	 */
	private $input = '{"kind":"Listing", "data":{"children":[{"kind":"t3", "data":{"subreddit":"pythonforengineers","name":"t3_jw6u2r"}}]}, "after":"t3_abcdefg"}';

	/**
	 * @dataProvider queryParameterProvider
	 */
	public function testConstructValidates($input, $isValid)
	{
		$object = json_decode($input, false, 512, JSON_THROW_ON_ERROR);

		if (! $isValid)
		{
			$this->expectException(RedditException::class);
			$this->expectExceptionMessage(lang('Reddit.invalidKindInput'));
		}

		$listing = new Listing($object);

		// Invalid listings fail before this
		$this->assertInstanceOf(Listing::class, $listing);
	}

	public function queryParameterProvider()
	{
		return [
			[$this->input, true], // valid
			['{"data":{"children":[{"bar":"bam"},{"foo":"baz"}]}}', false], // missing kind
			['{"kind":"listing", "data":{"children":[{"bar":"bam"},{"foo":"baz"}]}}', false], // lowercase kind
			['{"kind":"Comment", "data":{"children":[{"bar":"bam"},{"foo":"baz"}]}}', false], // different kind
			['{"kind":"Listing", "data":{"bar":"bam"}}', false], // data not object
			['{"kind":"Listing", "data":{"boo":[{"bar":"bam"},{"foo":"baz"}]}}', false], // data missing children
			['{"kind":"Listing", "data":{"children":{"bar":"bam"}}}', false], //  data children not array
		];
	}
	public function testConstructSetsAfter()
	{
		$listing = new Listing(json_decode($this->input));

		$this->assertEquals('t3_abcdefg', $listing->after);
	}

	public function testIterationCreatesThings()
	{
		$listing = new Listing(json_decode($this->input));

		foreach ($listing as $thing)
		{
			$this->assertInstanceOf(Thing::class, $thing);
		}
	}
}
