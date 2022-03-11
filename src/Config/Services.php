<?php

namespace Tatter\Reddit\Config;

use CodeIgniter\Config\BaseService;
use Tatter\Reddit\Config\Reddit as RedditConfig;
use Tatter\Reddit\Reddit;

class Services extends BaseService
{
    /**
     * Returns an initialized Reddit API client
     *
     * @param RedditConfig $config
     */
    public static function reddit(?RedditConfig $config = null, bool $getShared = true): Reddit
    {
        if ($getShared) {
            return static::getSharedInstance('reddit', $config);
        }

        return new Reddit($config ?? config('Reddit'));
    }
}
