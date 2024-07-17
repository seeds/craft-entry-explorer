<?php

namespace seeds\craftentryexplorer;

use Craft;
use craft\base\Element;
use craft\base\Model;
use craft\base\Plugin;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\web\twig\variables\CraftVariable;
use seeds\craftentryexplorer\behaviors\EntryExplorerBehavior;
use seeds\craftentryexplorer\models\Settings;
use seeds\craftentryexplorer\services\EntryExplorerService;
use seeds\craftentryexplorer\variables\EntryExplorerVariable;
use yii\base\Event;

/**
 * Entry Explorer plugin
 *
 * @method static EntryExplorer getInstance()
 * @method Settings getSettings()
 * @author Alexandre Monteiro <alexandre.monteiro@seeds.no>
 * @copyright Alexandre Monteiro
 * @license MIT
 * @property-read EntryExplorerService $entryExplorer
 * @property-read SettingsModel $settings
 */
class EntryExplorer extends Plugin
{
    /**
     * @var EntryExplorer
     */
    public static EntryExplorer $plugin;

    /**
     * @inheritdoc
     */
    public string $schemaVersion = '1.0.0';

    /**
     * @inheritdoc
     */
    public bool $hasCpSettings = false;

    /**
     * @inheritdoc
     */
    public bool $hasCpSection = true;

    public static function config(): array
    {
        return [
            'components' => [
                'entryExplorer' => ['class' => EntryExplorerService::class],
            ],
        ];
    }

    public function init(): void
    {
        parent::init();
        self::$plugin = $this;

        // Register variable
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function (Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;
            $variable->set('entryExplorer', EntryExplorerVariable::class);
        });

        Event::on(Element::class, Element::EVENT_DEFINE_BEHAVIORS, function (DefineBehaviorsEvent $event) {
            /** @var Element $element */
            $element = $event->sender;
            if ($element instanceof Entry) {
                $event->behaviors['entryExplorer'] = EntryExplorerBehavior::class;
            }
        });

        // Defer most setup tasks until Craft is fully initialized
        Craft::$app->onInit(function () {
            $this->attachEventHandlers();
            // ...
        });
    }

    /**
     * @inheritdoc
     */
    protected function createSettingsModel(): ?Model
    {
        return Craft::createObject(Settings::class);
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): ?string
    {
        return Craft::$app->view->renderTemplate('entry-explorer/settings.twig', [
            'plugin' => $this,
            'settings' => $this->getSettings(),
        ]);
    }

    private function attachEventHandlers(): void
    {
        // Register event handlers here ...
        // (see https://craftcms.com/docs/4.x/extend/events.html to get started)
    }
}
