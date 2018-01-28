<?php

namespace Drupal\importer\Plugin\QueueWorker;

  use Drupal\Core\Database\Connection;
  use Drupal\Core\Logger\LoggerChannelFactory;
  use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
  use Drupal\Core\Queue\QueueWorkerBase;
  use Drupal\importer\Entity\ImportedEntity;
  use Symfony\Component\DependencyInjection\ContainerInterface;

  /**
   * Processes import.
   *
   * @QueueWorker(
   * id = "importer_queue",
   * title = @Translation("Importer queue."),
   * cron = {"time" = 30}
   * )
   */
class ImporterQueue extends QueueWorkerBase implements ContainerFactoryPluginInterface {

  const LOGGER_CHANNEL = 'importer_queue';

  /**
   * @var \Drupal\Core\Database\Connection
   */
  protected $database;

  /**
   * @var LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * Constructs a ImporterQueue worker.
   *
   * @param array $configuration
   * @param string $plugin_id
   * @param mixed $plugin_definition
   * @param \Drupal\Core\Database\Connection $database
   * @param LoggerChannelFactory $loggerFactory
   */
  public function __construct(array $configuration, $plugin_id,
    $plugin_definition, Connection $database,
    LoggerChannelFactory $loggerFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->database = $database;
    $this->loggerFactory = $loggerFactory;
  }
  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array
  $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('database'),
      $container->get('logger.factory')
    );
  }

  /**
   * {@inheritdoc}
   */
  public function processItem($data) {
    $id = isset($data->id) && $data->id ? $data->id : NULL;
    if (!$id) {
      throw new \Exception('Missing ID.');
    }
    $importedEntity = ImportedEntity::loadOrCreate($data->id);
    $importedEntity->setImportedId($data->id);
    $importedEntity->setTitle($data->title);
    $importedEntity->setBody($data->body);
    $importedEntity->setImportedSource($data->source);
    if ($image = $this->manageImage($data->image, $data->id)) {
      $importedEntity->setImportedImage($image);
    }
    $importedEntity->save();
  }

  /**
   * Helper to fetch image.
   *
   * @param $uri
   * @param $name
   *
   * @return \Drupal\file\FileInterface|false|false
   */
  protected function manageImage($uri, $name) {
    $fileContents = file_get_contents($uri);
    if (!$fileContents) {
      $this->getLogger()->error('Failed to upload image.');
      return FALSE;
    }
    $file = file_save_data($fileContents, 'public://import_images/' . $name, FILE_EXISTS_REPLACE);
    if (!$file) {
      $this->getLogger()->error('Failed to upload image.');
      return FALSE;
    }
    return $file;
  }

  /**
   * Getter for logger.
   *
   * @return \Drupal\Core\Logger\LoggerChannelInterface
   */
  protected function getLogger() {
    return $this->loggerFactory->get(self::LOGGER_CHANNEL);
  }
}