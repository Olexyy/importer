<?php

namespace Drupal\importer\Plugin\Importer;

use Drupal\importer\Plugin\ImporterBase;

/**
 * Importer from a JSON format.
 *
 * @Importer(
 *  id = "json",
 *  label = @Translation("JSON Importer")
 * )
 */
class JsonImporter extends ImporterBase {

  /**
   * {@inheritdoc}
   */
  public function import() {
    $data = $this->getData();
    if (!$data || !$data->success) {
      return FALSE;
    }
    if (!isset($data->entities)) {
      return FALSE;
    }
    $items = $data->entities;
    foreach ($items as $item) {
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
    $uri = $this->requestStack->getCurrentRequest()->getSchemeAndHttpHost() . '/sites/default/files/import/responce.json';
    $request = $this->httpClient->get($uri);
    $string = $request->getBody()->getContents();
    return json_decode($string);
  }

  /**
   * Adds data to queue.
   *
   * @param \stdClass $item
   */
  private function addQueueItem($item) {
    // Get queue.
    $queue = \Drupal::queue(self::QUEUE);
    $queueItem = (object) [
      'id' => $item->id,
      'title' => $item->title,
      'body' => $item->body,
      'source' => $this->getPluginId(),
      'image' => $item->image,
    ];
    $queue->createItem($queueItem);
  }


}