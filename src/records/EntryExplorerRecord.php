<?php

/**
 * @copyright Copyright (c) Seeds
 */

namespace seeds\craftentryexplorer\records;

use craft\db\ActiveRecord;

/**
 * @property int $id
 * @property int $entryId
 * @property json $usedFields
 */
class EntryExplorerRecord extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName(): string
    {
        return '{{%entryexplorer}}';
    }
}
