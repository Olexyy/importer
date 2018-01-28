<?php

namespace Drupal\importer\Plugin;

  use Drupal\Component\Plugin\PluginBase;
  use Drupal\Core\Entity\EntityTypeManager;
  use Drupal\Core\Logger\LoggerChannelFactory;
  use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
  use GuzzleHttp\Client;
  use Symfony\Component\DependencyInjection\ContainerInterface;
  use Symfony\Component\HttpFoundation\RequestStack;

  /**
   * Base class for Importer plugins.
   */
abstract class ImporterBase extends PluginBase implements
  ImporterInterface, ContainerFactoryPluginInterface {

  const LOGGER_CHANNEL = 'importer';
  const QUEUE = 'importer_queue';

  /**
   * @var \Drupal\Core\Entity\EntityTypeManager
   */
  protected $entityTypeManager;

  /**
   * @var \GuzzleHttp\Client
   */
  protected $httpClient;

  /**
   * @var RequestStack
   */
  protected $requestStack;

  /**
   * @var LoggerChannelFactory
   */
  protected $loggerFactory;

  /**
   * {@inheritdoc}
   */
  public function __construct(array $configuration, $plugin_id,
    $plugin_definition, EntityTypeManager $entityTypeManager,
    Client $httpClient, RequestStack $requestStack,
    LoggerChannelFactory $loggerFactory) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->httpClient = $httpClient;
    $this->requestStack = $requestStack;
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
      $container->get('entity_type.manager'),
      $container->get('http_client'),
      $container->get('request_stack'),
      $container->get('logger.factory')
    );
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