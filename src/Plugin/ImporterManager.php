<?php

namespace Drupal\importer\Plugin;

  use Drupal\Core\Plugin\DefaultPluginManager;
  use Drupal\Core\Cache\CacheBackendInterface;
  use Drupal\Core\Extension\ModuleHandlerInterface;

  /**
   * Class ImporterManager.
   *
   * @package Drupal\importer\Plugin
   */
class ImporterManager extends DefaultPluginManager {

  /**
   * ImporterManager constructor.
   *
   * @param \Traversable $namespaces
   * @param CacheBackendInterface $cache_backend
   * @param ModuleHandlerInterface $module_handler
   */
  public function __construct(\Traversable $namespaces,
    CacheBackendInterface $cache_backend,
    ModuleHandlerInterface $module_handler) {
    parent::__construct('Plugin/Importer', $namespaces, $module_handler,
      'Drupal\importer\Plugin\ImporterInterface',
      'Drupal\importer\Annotation\Importer');
    $this->alterInfo('importer_plugin_info');
    $this->setCacheBackend($cache_backend, 'importer_plugins');
  }
}