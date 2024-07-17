<?php

/**
 * @copyright Copyright (c) Seeds
 */

namespace seeds\craftentryexplorer\variables;

use craft\elements\db\EntryQuery;
use seeds\craftentryexplorer\EntryExplorer;

class EntryExplorerVariable
{
    /**
     * Get all fields
     */
    public function getAllFields(): array
    {
        return EntryExplorer::$plugin->entryExplorer->getAllFields();
    }

    /**
     * Get used fields
     */
    public function getAllUsedFields(): array
    {
        return EntryExplorer::$plugin->entryExplorer->getAllUsedFields();
    }

    /**
     * Returns processed entries
     * @param int $perPage
     * @param int $currentPage
     * @param string|null $searchUsedFieldsTerm
     * @param string|null $searchTerm
     */
    public function getEntries($perPage = 10, $currentPage = 1, $searchUsedFieldsTerm = null, $searchTerm = null): EntryQuery
    {
        return EntryExplorer::$plugin->entryExplorer->getEntries($perPage, $currentPage, $searchUsedFieldsTerm, $searchTerm);
    }

    /**
     * Import Plugin Data
     */
    public function importPluginData(): void
    {
        EntryExplorer::$plugin->entryExplorer->importPluginData();
    }
}
