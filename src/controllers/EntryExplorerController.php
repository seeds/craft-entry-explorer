<?php

/**
 * @copyright Copyright (c) Seeds
 */

namespace seeds\craftentryexplorer\controllers;

use Craft;
use craft\web\Controller;
use seeds\craftentryexplorer\EntryExplorer;
use yii\web\Response;

class EntryExplorerController extends Controller
{
    /**
     * Import Plugin Data
     */
    public function actionImportPluginData(): Response
    {
        $this->requirePostRequest();

        EntryExplorer::$plugin->entryExplorer->importPluginData();

        Craft::$app->session->setNotice(Craft::t('entry-explorer', 'Importing plugin data in background.'));
        return $this->redirect('entry-explorer');
    }

    public function actionExportCsv()
    {
        $this->requirePostRequest();

        $searchUsedFieldsTerm = Craft::$app->request->getBodyParam('searchUsedFields');
        $searchTerm = Craft::$app->request->getBodyParam('search');

        // Get all entries
        $entries = EntryExplorer::$plugin->entryExplorer->getEntries(10000, 1, $searchUsedFieldsTerm, $searchTerm);
        return EntryExplorer::$plugin->entryExplorer->exportCsv($entries);
    }
}
