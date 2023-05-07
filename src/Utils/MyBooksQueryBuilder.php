<?php

namespace Drupal\my_books\Utils;

use Drupal\Core\Entity\EntityTypeManagerInterface;

class MyBooksQueryBuilder {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  public function __construct(EntityTypeManagerInterface $entityTypeManager) {
    $this->entityTypeManager = $entityTypeManager;
  }

  public function getBooks($contentType) {
    $query = $this->entityTypeManager->getStorage('node')
    ->getQuery()
    ->condition('type', $contentType)
    ->accessCheck(FALSE);

    return $query->execute();
  }
}
