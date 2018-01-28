<?php

  /**
   * Implements hook_drush_command().
   */
function importer_drush_command() {
  $items = [];
  $items['importer-run'] = [
    'description' => 'Run import',
    'options' => [
      'importer' => 'The Importer configuration entity to run.',
    ],
    'aliases' => ['imp-run'],
    'callback' => 'importer_run'
  ];
  $items['importer-list'] = [
    'description' => 'List importers',
    'aliases' => ['imp-list'],
    'callback' => 'importer_list'
  ];
  return $items;
}

 /**
  * Callback for the import Drush command.
  */
function importer_run() {
  $importer = drush_get_option('importer', NULL);
  $manager = \Drupal::service('importer.manager');
  if (is_null($importer)) {
    drush_print(t('You should provide "--importer" option'));
    return;
  }
  else {
    try {
      $plugin = $manager->createInstance($importer);
      $result = $plugin->import();
      $message_values = ['@importer' => $importer];
      $message = $result ? t('The "@importer" importer has been run.',
        $message_values) : t('There was a problem running the "@importer" importer.',
        $message_values);
      drush_print($message);
    }
    catch (Exception $exception) {
      drush_print($exception->getMessage());
    }
  }
}

 /**
  * Callback for the import Drush command.
  */
function importer_list() {
  $manager = \Drupal::service('importer.manager');
  drush_print(t('Available importers:'));
  foreach($manager->getDefinitions() as $importer) {
    drush_print(t("'@id' => @label", [
      '@id' => $importer['id'],
      '@label' => $importer['label'],
    ]));
  }
}