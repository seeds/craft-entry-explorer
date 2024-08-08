[![Total Downloads](https://poser.pugx.org/99x/craft-entry-explorer/downloads)](https://packagist.org/packages/99x/craft-entry-explorer)
[![Latest Stable Version](https://poser.pugx.org/99x/craft-entry-explorer/v/stable)](https://packagist.org/packages/99x/craft-entry-explorer)
[![License](https://poser.pugx.org/99x/craft-entry-explorer/license)](https://packagist.org/packages/99x/craft-entry-explorer)

# Craft Entry Explorer

The Entry Explorer plugin helps to understand which fields that are in on a per entry basis.

This plugin was developed out of the need to easier perform code due diligence on large and complex projects that has evolved over time.

Initially the plugin will analyse all the content in a Craft CMS installation. When the analysis job is completed it’s possible to filter the report per field to see exactly where it’s in use.

The main goal is to identify abandoned fields that are no longer in use and that can be removed.

## Requirements

This plugin requires Craft CMS 4.4.0 or later, and PHP 8.0.2 or later.

## Installation

You can install this plugin from the Plugin Store or with Composer.

#### From the Plugin Store

Go to the Plugin Store in your project’s Control Panel and search for “Entry Explorer”. Then press “Install”.

#### With Composer

Open your terminal and run the following commands:

```bash
# go to the project directory
cd /path/to/my-project.test

# tell Composer to load the plugin
composer require seeds/craft-entry-explorer

# tell Craft to install the plugin
./craft plugin/install entry-explorer
```


## Usage

#### Refresh button
When you click the refresh button, the EntryExplorerService will push the ImportPluginDataJob to the queue.

This job will get all the entries from your database and filter out empty fields from the entry's serialized values.

Only the used fields will be shown in the table.

It will also show you the pagination of the entries in the table that you can use to navigate through the entries.

#### Export to CSV button
Export the entries to a CSV file.
