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
	 * @var RedditConfig
	 */
	protected $config;

	/**
	 * Reddit instance preconfigured for testing
	 *
	 * @var Reddit
	 */
	protected $reddit;

	/**
	 * As close as possible to a "generic" URI to test
	 *
	 * @var string
	 */
	protected $uri = '/r/pythonforengineers/new';

	protected function setUp(): void
	{
		parent::setUp();

		$this->config = new RedditConfig();
		$this->reddit = new Reddit($this->config);
		$this->reddit->subreddit('pythonforengineers');
	}
}
