<?php namespace Tatter\Reddit;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;
use Tatter\Reddit\Reddit;

class ParametersTest extends CIUnitTestCase
{
	/**
	 * Reddit instance
	 *
	 * @var Reddit
	 */
	protected $reddit;

	public function setUp(): void
	{
		parent::setUp();

		$this->reddit = new Reddit(config('Reddit'));
	}

	public function testGetSubredditThrowsOnEmpty()
	{
		$this->expectException(RedditException::class);
		$this->expectExceptionMessage(lang('Reddit.missingSubreddit'));

		$query = $this->reddit->getSubreddit();
	}

	public function testSubredditSetsSubreddit()
	{
		$subreddit = 'NotLikeThis';
		$this->reddit->subreddit($subreddit);

		$result = $this->reddit->getSubreddit();

		$this->assertEquals($subreddit, $result);
	}

	public function testAfterNullsBefore()
	{
		$this->reddit->before('t1_gfedcba');
		$this->reddit->after('t1_abcdefg');

		$query = $this->reddit->getQuery();

		$this->assertNull($query['before']);
	}

	public function testNullAfterLeavesBefore()
	{
		$this->reddit->before('t1_gfedcba');
		$this->reddit->after(null);

		$query = $this->reddit->getQuery();

		$this->assertEquals('t1_gfedcba', $query['before']);
	}

	public function testBeforeNullsAfter()
	{
		$this->reddit->after('t1_abcdefg');
		$this->reddit->before('t1_gfedcba');

		$query = $this->reddit->getQuery();

		$this->assertNull($query['after']);
	}

	public function testNullBeforeLeavesAfter()
	{
		$this->reddit->after('t1_abcdefg');
		$this->reddit->before(null);

		$query = $this->reddit->getQuery();

		$this->assertEquals('t1_abcdefg', $query['after']);
	}

	/**
	 * @dataProvider queryParameterProvider
	 */
	public function testQueryMethodsSetValue($name, $input)
	{
		$this->reddit->$name($input);

		$query = $this->reddit->getQuery();

		$this->assertEquals($input, $query[$name]);
	}

	public function queryParameterProvider()
	{
		return [
			['after', 't1_abcdefg'],
			['before', 't1_gfedcba'],
			['count', 50],
			['limit', 10],
			['show', 'all'],
		];
	}
}
