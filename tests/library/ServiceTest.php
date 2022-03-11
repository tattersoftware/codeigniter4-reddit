<?php

namespace Tatter\Reddit;

use CodeIgniter\Test\CIUnitTestCase;

/**
 * @internal
 */
final class ServiceTest extends CIUnitTestCase
{
	public function testServiceReturnsInstance()
	{
		$result = service('reddit');

		$this->assertInstanceOf(Reddit::class, $result);
	}
}
