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

/**
 * @RestResource(
 *   id = "articles_resource",
 *   label = @Translation("Articles Resource"),
 *   uri_paths = {
 *     "canonical"= "/api/v1/articles/{nid}",
 *   }
 * )
 */
class ArticlesResource extends ResourceBase {

  /**
   * @var EntityStorageInterface
   */
  private EntityStorageInterface $nodeStorage;

  /**
   * @var int
   */
  private int $nid;

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
   * @param $nid
   * @return ResourceResponse
   */
  public function get($nid): ResourceResponse {
    if (!is_numeric($nid)) {
      $message = $this->t('The content ID must be an integer.');
      return new ResourceResponse(['message' => $message], 400);
    }

    $this->nid = $nid;

    $article_node = $this->nodeStorage->load($nid);
    if (!$article_node) {
      $message = $this->t('Content not found');
      return new ResourceResponse(['message' => $message], 404);
    }

    $data = [
      'article' => $this->buildArticleFields($article_node),
      'links' => $this->buildGetRequestLinks(),
    ];

    $cache_metadata = new CacheableMetadata();
    $cache_metadata->setCacheTags(["node_list:$this->nid"]);

    $response = new ResourceResponse($data);
    $response->addCacheableDependency($cache_metadata);

    return $response;
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
    return [
      [
        'rel' => 'self',
        'href' => "/api/v1/articles/$this->nid",
      ],
      [
        'rel' => 'collection',
        'href' => "/api/v1/articles",
      ],
    ];
  }

}
