<?php

namespace Drupal\importer\Plugin\Importer;

use Drupal\importer\Entity\ImportedEntity;
use Drupal\importer\Plugin\ImporterBase;

/**
 * Importer from a XML format.
 *
 * @Importer(
 *  id = "xml",
 *  label = @Translation("XML Importer")
 * )
 */
class XmlImporter extends ImporterBase {

  /**
   * {@inheritdoc}
   */
  public function import() {
    $data = $this->getData();
    if (!$data || !$data->status) {
      return FALSE;
    }
    if (!isset($data->objects)) {
      return FALSE;
    }
    foreach ($data->objects as $item) {
      $this->addQueueItem($item);
    }
    return TRUE;
  }

  /**
   * Loads the items data from the remote URL.
   *
   * @return \stdClass
   */
  private function getData() {
    $uri = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/sites/default/files/import/responce.xml';
    $request = $this->httpClient->get($uri);
    $string = $request->getBody()->getContents();
    return simplexml_load_string($string);
  }

  /**
   * Saves an entity from the remote data.
   *
   * @param \SimpleXMLElement
   */
  private function addQueueItem($item) {
    $queue = \Drupal::queue(self::QUEUE);
    $queueItem = (object) [
      'id' => (string)$item->key,
      'title' => (string)$item->name,
      'body' => (string)$item->text,
      'source' => $this->getPluginId(),
      'image' => (string)$item->picture,
    ];
    $queue->createItem($queueItem);
  }


}