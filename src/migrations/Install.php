<?php

namespace seeds\craftentryexplorer\migrations;

use Craft;
use craft\db\Migration;
use craft\db\Table;
use seeds\craftentryexplorer\records\EntryExplorerRecord;

/**
 * Install migration.
 */
class Install extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp(): bool
    {
        if (!$this->db->tableExists(EntryExplorerRecord::tableName())) {
            $this->createTable(EntryExplorerRecord::tableName(), [
                'id' => $this->primaryKey(),
                'entryId' => $this->integer()->notNull(),
                'usedFields' => $this->json(),
                'dateCreated' => $this->dateTime()->notNull(),
                'dateUpdated' => $this->dateTime()->notNull(),
                'uid' => $this->uid(),
            ]);

            $this->createIndex(null, EntryExplorerRecord::tableName(), 'entryId', true);

            $this->addForeignKey(null, EntryExplorerRecord::tableName(), 'entryId', Table::ELEMENTS, 'id', 'CASCADE');

            // Refresh the db schema caches
            Craft::$app->db->schema->refresh();
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        $this->dropTableIfExists(EntryExplorerRecord::tableName());

        return true;
    }
}
