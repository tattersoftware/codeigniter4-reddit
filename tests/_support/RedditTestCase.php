<?php namespace Tests\Support;

use CodeIgniter\Test\CIDatabaseTestCase;
use Config\Services;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Reddit;

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

	/**
	 * Reddit instance preconfigured for testing
	 *
	 * @var Reddit
	 */
	protected $reddit;

	public function setUp(): void
	{
		parent::setUp();

		$this->reddit = new Reddit(new RedditConfig());
		$this->reddit->setSubreddit('pythonforengineers');
	}
}
