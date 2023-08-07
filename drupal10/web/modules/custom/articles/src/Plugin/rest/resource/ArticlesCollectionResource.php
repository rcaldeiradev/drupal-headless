<?php

namespace Drupal\articles\Plugin\rest\resource;

use Drupal\Component\Plugin\Exception\InvalidPluginDefinitionException;
use Drupal\Component\Plugin\Exception\PluginNotFoundException;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\rest\Plugin\ResourceBase;
use Drupal\rest\Plugin\ResourceInterface;
use Drupal\rest\ResourceResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @RestResource(
 *   id = "articles_collection_resource",
 *   label = @Translation("Articles Collection Resource"),
 *   uri_paths = {
 *     "canonical"= "/api/v1/articles",
 *   }
 * )
 */
class ArticlesCollectionResource extends ResourceBase {

  const ITEMS_PER_PAGE = 10;

  /**
   * @var EntityStorageInterface
   */
  private EntityStorageInterface $nodeStorage;

  /**
   * @var int
   */
  private int $page = 0;

  /**
   * @var float
   */
  private float $pagesCount = 0;

  /**
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   * @param array $serializer_formats
   * @param LoggerInterface $logger
   * @param EntityTypeManagerInterface $entity_type_manager
   *
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   */
  public function __construct(array $configuration, $plugin_id, $plugin_definition, array $serializer_formats, LoggerInterface $logger, EntityTypeManagerInterface $entity_type_manager) {
    parent::__construct($configuration, $plugin_id, $plugin_definition, $serializer_formats, $logger);
    $this->nodeStorage = $entity_type_manager->getStorage('node');
  }

  /**
   * @param ContainerInterface $container
   * @param array $configuration
   * @param $plugin_id
   * @param $plugin_definition
   *
   * @return ResourceInterface
   *
   * @throws InvalidPluginDefinitionException
   * @throws PluginNotFoundException
   */
  public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): ResourceInterface {
    return new static(
      $configuration,
      $plugin_id,
      $plugin_definition,
      $container->getParameter('serializer.formats'),
      $container->get('logger.factory')->get('rest'),
      $container->get('entity_type.manager')
    );
  }

  /**
   * @param Request $request
   * @return ResourceResponse
   */
  public function get(Request $request): ResourceResponse {
    $this->page = (int) $request->get('page', 0);
    $this->pagesCount = $this->getPagesCount();

    $data = [
      'page' => [
        'number' => $this->page,
        'total' => $this->pagesCount,
      ],
      'articles' => $this->buildArticlesList(),
      'links' => $this->buildGetRequestLinks(),
    ];

    $cache_metadata = new CacheableMetadata();
    $cache_metadata->setCacheTags(['node_list:article']);

    $response = new ResourceResponse($data);
    $response->addCacheableDependency($cache_metadata);

    return $response;
  }

  /**
   * @return float
   */
  private function getPagesCount(): float {
    $total_pages = $this->getTotalItemsCount() / self::ITEMS_PER_PAGE;
    return ceil($total_pages);
  }

  /**
   * @return int
   */
  private function getTotalItemsCount(): int {
    return $this->nodeStorage->getQuery()->count()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->accessCheck()
      ->execute();
  }

  /**
   * @return array
   */
  private function buildArticlesList(): array {
    $articles = [];

    $nids = $this->fetchArticlesNids();
    $nodes = $this->nodeStorage->loadMultiple($nids);

    foreach ($nodes as $node) {
      $articles[] = $this->buildArticleFields($node);
    }

    return $articles;
  }

  /**
   * @return array
   */
  private function fetchArticlesNids(): array {
    return $this->nodeStorage->getQuery()
      ->condition('type', 'article')
      ->condition('status', 1)
      ->accessCheck()
      ->sort('created', 'DESC')
      ->range($this->page * self::ITEMS_PER_PAGE, self::ITEMS_PER_PAGE)
      ->execute();
  }

  /**
   * @param EntityInterface $node
   * @return array
   */
  private function buildArticleFields(EntityInterface $node): array {
    return [
      'id' => $node->id(),
      'created' => $node->get('created')->getString(),
      'title' => $node->label(),
      'summary' => $node->get('body')->summary,
      'body' => $node->get('body')->value,
    ];
  }

  /**
   * @return array
   */
  private function buildGetRequestLinks(): array {
    $links[] = [
      'rel' => 'self',
      'href' => '/api/v1/articles',
    ];

    if ($this->page < ($this->pagesCount - 1)) {
      $next_page = $this->page + 1;
      $links[] = [
        'rel' => 'next',
        'href' => "/api/v1/articles?page=$next_page",
      ];
    }

    if ($this->page > 0) {
      $previous_page = $this->page - 1;
      $links[] = [
        'rel' => 'previous',
        'href' => "/api/v1/articles?page=$previous_page",
      ];
    }

    return $links;
  }

}
