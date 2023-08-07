<?php

namespace Drupal\Tests\articles\Functional;

use Behat\Mink\Exception\ExpectationException;
use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Url;
use Drupal\field\Entity\FieldConfig;
use Drupal\field\Entity\FieldStorageConfig;
use Drupal\Tests\rest\Functional\CookieResourceTestTrait;
use Drupal\Tests\rest\Functional\ResourceTestBase;

class ArticlesResourceTest extends ResourceTestBase {

  use CookieResourceTestTrait;

  /**
   * {@inheritdoc}
   */
  protected $defaultTheme = 'stark';

  /**
   * {@inheritdoc}
   */
  protected static $format = 'json';

  /**
   * {@inheritdoc}
   */
  protected static $auth = 'cookie';

  /**
   * {@inheritdoc}
   */
  protected static $resourceConfigId = 'articles_resource';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['node', 'rest', 'articles'];

  /**
   * @var int|null
   */
  protected ?int $nid;

  /**
   * @return void
   */
  protected function setUp(): void {
    parent::setUp();

    $content_type = 'article';

    $this->drupalCreateContentType([
      'type' => $content_type,
      'name' => 'Article',
    ]);

    $node = $this->drupalCreateNode([
      'type' => 'article',
      'title' => 'Test node',
      'body' => [
        'value' => 'Hi there',
        'format' => 'plain_text',
        'summary' => 'This is a summary',
      ],
    ]);
    $this->nid = $node->id();

    $this->provisionResource([static::$format], [static::$auth]);
    $this->setUpAuthorization('GET');
  }

  /**
   * @param $method
   * @return void
   */
  protected function setUpAuthorization($method) {
    switch ($method) {
      case 'GET':
        $this->grantPermissionsToAnonymousRole(['restful get articles_resource']);
        break;

      default:
        throw new \UnexpectedValueException();
    }
  }

  /**
   * @return void
   * @throws ExpectationException
   */
  public function testAnonymousResponse() {
    $this->drupalGet("api/v1/articles/$this->nid");
    $this->assertSession()->statusCodeEquals(200);
  }

  /**
   * @param $method
   * @param Url $url
   * @param array $request_options
   * @return void
   */
  protected function assertNormalizationEdgeCases($method, Url $url, array $request_options): void {}

  /**
   * @return RefinableCacheableDependencyInterface
   */
  protected function getExpectedUnauthorizedAccessCacheability(): RefinableCacheableDependencyInterface {
    return new CacheableMetadata();
  }
}
