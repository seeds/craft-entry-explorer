<?php

/**
 * @copyright Copyright (c) Seeds
 */

namespace seeds\craftentryexplorer\assets;

use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

class EntryExplorerAsset extends AssetBundle
{
    /**
     * @inheritdoc
     */
    public function init(): void
    {
        $this->sourcePath = '@seeds/craftentryexplorer/resources';

        $this->depends = [
            CpAsset::class,
        ];

        $this->css = [
            'css/cp.css',
        ];

        $this->js = [
            'js/cp.js',
        ];

        parent::init();
    }
}
