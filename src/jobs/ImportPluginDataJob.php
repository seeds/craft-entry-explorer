<?php

/**
 * @copyright Copyright (c) Seeds
 */

namespace seeds\craftentryexplorer\jobs;

use Craft;
use craft\elements\Entry;
use craft\queue\BaseJob;
use seeds\craftentryexplorer\EntryExplorer;

class ImportPluginDataJob extends BaseJob
{
    /**
     * Executes the job
     *
     * @param \yii\queue\Queue $queue The queue the job belongs to
     * @return void
     */
    public function execute($queue): void
    {
        $offset = 0;
        $limit = 100;

        $totalEntries = $entries = Entry::find()->status(null)->count();

        do {

            $this->setProgress(
                $queue,
                $offset / $totalEntries,
                Craft::t('app', '{step, number} of {total, number}', [
                    'step' => $offset + 1,
                    'total' => $totalEntries,
                ])
            );

            Craft::$app->cache->set('importPluginDataProgress', $offset / $totalEntries);

            // Get all entries
            $entries = Entry::find()
                ->status(null)
                ->limit($limit)
                ->offset($offset)
                ->all();

            EntryExplorer::$plugin->entryExplorer->importEntries($entries);

            $offset += $limit;
        } while (count($entries) == $limit);
    }

    /**
     * Returns a default description for the job
     *
     * @return string|null
     */
    protected function defaultDescription(): ?string
    {
        return Craft::t('app', 'Importing entry explorer plugin data');
    }
}
