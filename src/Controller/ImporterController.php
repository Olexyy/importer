<?php

namespace Drupal\importer\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Url;
use Drupal\importer\Plugin\ImporterManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ImporterController
 *
 * @package Drupal\importer\Controller
 */
class ImporterController extends ControllerBase {

  protected $importerManager;

  public function __construct(ImporterManager $importerManager) {
    $this->importerManager = $importerManager;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static(
      $container->get('importer.manager')
    );
  }

  /**
   * Controller callback.
   *
   * @param string $source
   * @return array|RedirectResponse
   */
  public function import($source = NULL) {
    if ($source) {
      try {
        $plugin = $this->importerManager->createInstance($source);
      }
      catch (\Exception $exception) {
        drupal_set_message($this->t('Cannot create @source import source.', [
          '@source' => $source,
        ]), 'error');
      }
      $result = $plugin->import();
      if ($result) {
        drupal_set_message($this->t('Import with @source was successfully queued.', [
          '@source' => $source,
        ]), 'status');
      }
      else {
        drupal_set_message($this->t('Import with @source failed.', [
          '@source' => $source,
        ]), 'error');
      }
      return new RedirectResponse(Url::fromRoute('importer.import', [
        'source' => '',
      ])->toString());
    }
    else {
      $message = $this->t("Available importers:");
      foreach($this->importerManager->getDefinitions() as $importer) {
        $message .= ($this->t("'@id' => @label", [
          '@id' => $importer['id'],
          '@label' => $importer['label'],
        ]));
      }
      $message .= $this->t("Append it as second parameter after '/import/'.");
    }
    return [
      '#markup' => $message,
    ];
  }

}