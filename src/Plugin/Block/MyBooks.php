<?php

namespace Drupal\my_books\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Plugin\ContainerFactoryPluginInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Drupal\Core\Routing\RouteMatchInterface;
use Drupal\Core\Entity\EntityFieldManagerInterface;
use Drupal\my_books\Utils\MyBooksQueryBuilder;

/**
 * Provides a 'My Books' Block.
 *
 * @Block(
 *   id = "my_books",
 *   admin_label = @Translation("My Books"),
 *   category = @Translation("My Books"),
 * )
 */
class MyBooks extends BlockBase implements ContainerFactoryPluginInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The route match service.
   *
   * @var \Drupal\Core\Routing\RouteMatchInterface
   */
  protected $routeMatch;

  /**
   * The entity field manager.
   *
   * @var \Drupal\Core\Entity\EntityFieldManagerInterface
   */
  protected $entityFieldManager;

  /**
   * Constructs a new CustomBlockExample instance.
   *
   * @param array $configuration
   *   The block configuration.
   * @param string $plugin_id
   *   The block ID.
   * @param mixed $plugin_definition
   *   The block definition.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entityTypeManager
   *   The entity type manager.
   * @param \Drupal\Core\Routing\RouteMatchInterface $routeMatch
   *   The route match service.
   * @param \Drupal\Core\Entity\EntityFieldManagerInterface $entityFieldManager
   *   The entity field manager. 
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, EntityTypeManagerInterface $entityTypeManager, RouteMatchInterface $routeMatch, EntityFieldManagerInterface $entityFieldManager, MyBooksQueryBuilder $queryBuilder) {
    parent::__construct($configuration, $plugin_id, $plugin_definition);
    $this->entityTypeManager = $entityTypeManager;
    $this->routeMatch = $routeMatch;
    $this->entityFieldManager = $entityFieldManager;
    $this->queryBuilder = $queryBuilder;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition) {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->get('entity_type.manager'),
      $container->get('current_route_match'),
      $container->get('entity_field.manager'),
      $container->get('mybooks.query_builder')
    );
  }
  /**
   * {@inheritdoc}
   */
  public function build() {
    $nids = $this->queryBuilder->getBooks('books');
    foreach ($nids as $nid) {
      $node = $this->entityTypeManager->getStorage('node')->load($nid);
      $title = $node->get('title')->getValue()[0]['value'];
      $body = $node->get('body')->getValue()[0]['value'];
      $field_author = $node->get('field_author')->getValue()[0]['value'];
      $field_book_image = $node->get('field_book_image')->entity->getFileUri();
      // $content_type = $this->entityTypeManager->getStorage('node_type')->load($node->getType());
      // $fields = $this->entityFieldManager->getFieldDefinitions('node', 'books');
      return [
        '#theme' => 'my_books',
        '#data' => [$title, $body, $field_author, $field_book_image],
      ];
    }
  }
}