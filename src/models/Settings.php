<?php

namespace seeds\craftentryexplorer\models;

use Craft;
use craft\base\Model;

/**
 * Entry Explorer settings
 */
class Settings extends Model
{
    /**
     * @var bool
     */
    public bool $ignoreLoggedInUsers = false;

    /**
     * @var string
     */
    public string $ignoreIpAddresses = '';
}
