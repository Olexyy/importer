<?php

namespace Drupal\importer\Annotation;

use Drupal\Component\Annotation\Plugin;

/**
 * Defines a Importer item annotation object.
 *
 * @see \Drupal\importer\Plugin\ImporterManager
 *
 * @Annotation
 */
class Importer extends Plugin {

  /**
   * The plugin ID.
   *
   * @var string
   */
  public $id;

  /**
   * The label of the plugin.
   *
   * @var \Drupal\Core\Annotation\Translation
   */
  public $label;
}