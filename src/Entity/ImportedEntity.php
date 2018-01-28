<?php

namespace Drupal\importer\Entity;


use Drupal\node\Entity\Node;

/**
 * Class ImportedEntity.
 *
 * @package Drupal\importer\Entity\
 */
class ImportedEntity {

  const FIELD_IMPORT_ID = 'field_import_id';
  const FIELD_IMPORT_IMAGE = 'field_import_image';
  const FIELD_IMPORT_SOURCE = 'field_import_source';
  const BODY = 'body';
  const BUNDLE = 'imported';
  const LOGGER_CHANNEL = 'imported_entity';

  /**
   * @var Node
   */
  protected $entity;

  /**
   * ImportedEntity constructor.
   *
   * @param Node $entity
   */
  protected function __construct(Node $entity) {
    $this->entity = $entity;
  }

  /**
   * @param Node $entity
   *
   * @return static
   */
  public static function createFromNode(Node $entity) {
    return new static($entity);
  }

  /**
   * @param $id
   *
   * @return null|static
   */
  public static function load($id) {
    $entity = Node::load($id);
    if ($entity) {
      return new static($entity);
    }
    return NULL;
  }

  /**
   * @param array $params
   *
   * @return static
   */
  public static function createNew(array $params = []) {
    $params['type'] = self::BUNDLE;
    $entity = Node::create($params);
    return new static($entity);
  }

  /**
   * @param $importId
   *
   * @return ImportedEntity
   */
  public static function loadOrCreate($importId) {
    try {
      $entities = \Drupal::entityTypeManager()
        ->getStorage('node')
        ->loadByProperties([self::FIELD_IMPORT_ID => $importId]);
      if ($entities) {
        return self::createFromNode(reset($entities));
      } else {
        return self::createNew();
      }
    } catch (\Exception $exception) {
      \Drupal::logger(self::LOGGER_CHANNEL)
        ->error($exception->getMessage());
    }
  }

  /**
   * @return Node
   */
  public function getEntity() {
    return $this->entity;
  }

  /**
   * @return string
   */
  public function getTitle() {
    return $this->entity->getTitle();
  }

  /**
   * @param $value
   *
   * @return $this
   */
  public function setTitle($value) {
    $this->entity->setTitle($value);
    return $this;
  }

  /**
   * @return mixed
   */
  public function getBody() {
    return $this->entity->body->value;
  }

  /**
   * @param $value
   *
   * @return $this
   */
  public function setBody($value) {
    $this->entity->body = $value;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getImportedSource() {
    return $this->entity->{self::FIELD_IMPORT_SOURCE}->value;
  }

  /**
   * @param $value
   *
   * @return $this
   */
  public function setImportedSource($value) {
    $this->entity->{self::FIELD_IMPORT_SOURCE} = $value;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getImportedId() {
    return $this->entity->{self::FIELD_IMPORT_ID}->value;
  }

  /**
   * @param $value
   *
   * @return $this
   */
  public function setImportedId($value) {
    $this->entity->{self::FIELD_IMPORT_ID} = $value;
    return $this;
  }

  /**
   * @return mixed
   */
  public function getImportedImage() {
    return $this->entity->{self::FIELD_IMPORT_IMAGE}->value;
  }

  /**
   * @param $value
   *
   * @return $this
   */
  public function setImportedImage($value) {
    $this->entity->{self::FIELD_IMPORT_IMAGE} = $value;
    return $this;
  }

  /**
   * Save helper.
   */
  public function save() {
    $this->entity->save();
  }

  /**
   * Delete helper.
   */
  public function delete() {
    $this->entity->delete();
  }

}