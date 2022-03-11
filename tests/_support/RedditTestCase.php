<?php

namespace Tests\Support;

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Reddit;

/**
 * @internal
 */
abstract class RedditTestCase extends CIUnitTestCase
{
    use DatabaseTestTrait;

    /**
     * @var bool
     */
    protected $refresh = true;

    /**
     * @var array|string|null
     */
    protected $namespace = 'Tatter\Reddit';

    protected RedditConfig $config;

    /**
     * Reddit instance preconfigured for testing
     */
    protected \Tatter\Reddit\Reddit $reddit;

    /**
     * As close as possible to a "generic" URI to test
     */
    protected string $uri = '/r/pythonforengineers/new';

    protected function setUp(): void
    {
        parent::setUp();

        $this->config = new RedditConfig();
        $this->reddit = new Reddit($this->config);
        $this->reddit->subreddit('pythonforengineers');
    }
}
