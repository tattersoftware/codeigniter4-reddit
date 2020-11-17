<?php namespace Tests\Support;

use CodeIgniter\Test\CIDatabaseTestCase;
use Config\Services;

class RedditTestCase extends CIDatabaseTestCase
{
	/**
	 * @var boolean
	 */
	protected $refresh = true;

	/**
	 * @var string|array|null
	 */
	protected $namespace = 'Tatter\Reddit';

	public function setUp(): void
	{
		parent::setUp();
	}
}
