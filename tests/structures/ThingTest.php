<?php namespace Tatter\Reddit\Structures;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;

class ThingTest extends CIUnitTestCase
{
	/**
	 * Input for a Link to test on
	 *
	 * @var string
	 */
	private $input = '{"kind":"t3", "data":{"subreddit":"pythonforengineers","name":"t3_jw6u2r"}}';

	/**
	 * @var Thing
	 */
	private $thing;

	protected function setUp(): void
	{
		parent::setUp();

		$this->thing = new Link(json_decode($this->input));
	}

	public function testToStringReturnsKind()
	{
		$this->assertEquals('Link', (string) $this->thing);
	}

	public function testName()
	{
		$this->assertEquals('t3_jw6u2r', $this->thing->name());
	}

	public function testKind()
	{
		$this->assertEquals('t3', $this->thing->kind());
	}

	public function testId()
	{
		$this->assertEquals('jw6u2r', $this->thing->id());
	}

	public function testInt()
	{
		$this->assertEquals(1202923971, $this->thing->int());
	}
}
