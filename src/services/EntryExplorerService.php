<?php

/**
 * @copyright Copyright (c) Seeds
 */

namespace seeds\craftentryexplorer\services;

use Craft;
use craft\base\Component;
use craft\elements\db\EntryQuery;
use craft\elements\Entry;
use craft\records\Field;
use seeds\craftentryexplorer\jobs\ImportPluginDataJob;
use seeds\craftentryexplorer\records\EntryExplorerRecord;
use yii\web\Response;

/**
 * @property EntryQuery $entries
 */
class EntryExplorerService extends Component
{
    static bool $joinAdded = false;

    /**
     * Returns all fields
     */
    public function getAllFields(): array
    {
        return Field::find()
            ->select(['handle', 'name'])
            ->distinct()
            ->orderBy(['name' => SORT_ASC])
            ->all();
    }

    /**
     * Returns all used fields
     */
    public function getAllUsedFields(): array
    {
        $record = EntryExplorerRecord::find()
            ->select(['usedFields'])
            ->distinct()
            ->column();

        $usedFileds = [];

        foreach ($record as $fields) {

            $fields = json_decode($fields, true);
            if ($fields) {
                $usedFileds = array_unique(array_merge($usedFileds, array_keys($fields)));
            }
        }

        sort($usedFileds);

        foreach ($usedFileds as $key => $value) {
            $obj = new \stdClass();
            $obj->handle = $value;
            $obj->name = null;
            $usedFileds[$key] = $obj;
        }

        return $usedFileds;
    }

    /**
     * Returns entries to be explored
     * @param int $perPage
     * @param int $currentPage
     * @param string|null $searchUsedFieldsTerm
     * @param string|null $searchTerm
     */
    public function getEntries($perPage = 10, $currentPage = 1, $searchUsedFieldsTerm = null, $searchTerm = null): EntryQuery
    {
        $entryQuery = Entry::find()
            ->addSelect(['usedFields'])
            ->innerJoin(EntryExplorerRecord::tableName(), 'entryId = elements.id')
            ->status(null)
            ->where("usedFields like '%{$searchUsedFieldsTerm}%'")
            ->orderBy(['ISNULL(sectionId)' => SORT_ASC])
            ->limit($perPage)
            ->offset(($currentPage - 1) * $perPage);


        return $entryQuery->search($searchTerm);
    }
    public function importPluginData(): void
    {
        Craft::$app->queue->push(new ImportPluginDataJob());
    }

    public function importEntries(array $entries): void
    {
        foreach ($entries as $entry) {

            $entryExplorerRecord = EntryExplorerRecord::find()
                ->where(['entryId' => $entry->id])
                ->one();

            if (!$entryExplorerRecord) {
                $entryExplorerRecord = new EntryExplorerRecord();
            }

            $entryExplorerRecord->entryId = $entry->id;
            $entryExplorerRecord->usedFields = null;
            $entryExplorerRecord->save();

            $this->importUsedFields($entry);
        }
    }
    private function importUsedFields(Entry $entry): void
    {
        // Get record from database to save the fields data
        $entryExplorerRecord = EntryExplorerRecord::find()
            ->where(['entryId' => $entry->id])
            ->one();

        if (!$entryExplorerRecord) {
            return;
        }

        try {
            // Filter out empty fields from the entry's serialized values
            $entryFields = array_filter($entry->getSerializedFieldValues() ?? []);
        } catch (\Exception $e) {
            $entryFields = [];
        }

        // Initialize array to store used fields
        $usedFields = [];

        // Add title field if it exists and is not empty
        if (isset($entry->title) && !empty($entry->title)) {
            $usedFields[] = 'title';
        }

        $matrixFields = [];
        foreach (Craft::$app->fields->getAllFields() as $field) {
            if ($field instanceof \craft\fields\Matrix) {
                $matrixFields[] = $field->handle;
            }
        }

        // Loop through entry fields to extract field names and block types
        foreach ($entryFields as $fieldName => $fieldValue) {

            if (in_array($fieldName, $matrixFields)) {
                foreach ($fieldValue as $block) {
                    // Extract block type and add to used fields
                    $blockType = $block['type'] ?? '{{empty}}';
                    $usedField = "{$fieldName}.{$blockType}";
                }
            } else {
                // Add non-matrix field to used fields
                $usedField = $fieldName;
            }

            if (!in_array($usedField, $usedFields)) {
                $usedFields[] = $usedField;
            }
        }

        $usedFields = array_fill_keys($usedFields, true);

        $entryExplorerRecord->usedFields = $usedFields;
        $entryExplorerRecord->save();
    }

    public function exportCsv(EntryQuery $entries): Response
    {
        // Define the headers for the CSV
        $headers = ['Entry ID', 'Entry Type Handle', 'Entry Type Name', 'Entry Title', 'Section', 'Used Fields'];

        // Open output stream
        $output = fopen('php://temp', 'r+');

        // Write the headers to the CSV
        fputcsv($output, $headers);

        // Write the data rows to the CSV
        foreach ($entries as $entry) {
            $fields = json_decode($entry->usedFields, true);
            $fields = array_keys($fields);
            $row = [
                $entry->id,
                $entry->type,
                $entry->type->name,
                $entry->title,
                $entry->section->name ?? '-',
                implode(', ', $fields)
            ];
            fputcsv($output, $row);
        }

        // Rewind the stream
        rewind($output);

        // Read the stream content
        $csvContent = stream_get_contents($output);

        // Close the stream
        fclose($output);

        // Return the CSV as a download response
        return Craft::$app->response->sendContentAsFile($csvContent, 'entries.csv', [
            'mimeType' => 'text/csv',
        ]);
    }
}
