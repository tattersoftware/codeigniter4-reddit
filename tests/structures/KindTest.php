<?php

namespace Tatter\Reddit\Structures;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class KindTest extends CIUnitTestCase
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
        $this->assertSame('Link', (string) $this->kind);
    }

    public function testName()
    {
        $this->assertSame('t3_jw6u2r', $this->kind->name());
    }

    public function testKind()
    {
        $this->assertSame('t3', $this->kind->kind());
    }

    public function testId()
    {
        $this->assertSame('jw6u2r', $this->kind->id());
    }

    public function testInt()
    {
        $this->assertSame(1202923971, $this->kind->int());
    }
}
