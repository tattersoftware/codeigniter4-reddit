<?php namespace Tatter\Reddit;

use CodeIgniter\Test\CIUnitTestCase;
use Tatter\Reddit\Reddit;

class ServiceTest extends CIUnitTestCase
{
	public function testServiceReturnsInstance()
	{
		$result = service('reddit');

		$this->assertInstanceOf(Reddit::class, $result);
	}
}
