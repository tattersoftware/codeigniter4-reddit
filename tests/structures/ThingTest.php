<?php

namespace Tatter\Reddit\Structures;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Exceptions\RedditException;

/**
 * @internal
 */
final class ThingTest extends CIUnitTestCase
{
    public function testNoKindReturnsThing()
    {
        $result = Thing::create((object) ['foo' => 'bar']);

        $this->assertInstanceOf(Thing::class, $result);
        $this->assertSame('bar', $result->foo);
    }

    public function testUndefinedThrows()
    {
        $thing = Thing::create((object) ['foo' => 'bar']);

        $this->expectException(RedditException::class);
        $this->expectExceptionMessage(lang('Reddit.missingThingKey', ['banana', 'Thing']));

        $thing->banana; // @phpstan-ignore-line
    }
}
