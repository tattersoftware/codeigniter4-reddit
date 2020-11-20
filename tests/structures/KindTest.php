<?php namespace Tatter\Reddit\Structures;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;

class KindTest extends CIUnitTestCase
{
	/**
	 * Input for a Link to test on
	 *
	 * @var string
	 */
	private $input = '{"kind":"t3", "data":{"subreddit":"pythonforengineers","name":"t3_jw6u2r"}}';

	/**
	 * @var Kind
	 */
	private $kind;

	protected function setUp(): void
	{
		parent::setUp();

		$this->kind = new Link(json_decode($this->input));
	}

	public function testToStringReturnsKind()
	{
		$this->assertEquals('Link', (string) $this->kind);
	}

	public function testName()
	{
		$this->assertEquals('t3_jw6u2r', $this->kind->name());
	}

	public function testKind()
	{
		$this->assertEquals('t3', $this->kind->kind());
	}

	public function testId()
	{
		$this->assertEquals('jw6u2r', $this->kind->id());
	}

	public function testInt()
	{
		$this->assertEquals(1202923971, $this->kind->int());
	}
}
