<?php

namespace Drupal\Tests\articles\Functional;

use Drupal\Core\Cache\CacheableMetadata;
use Drupal\Core\Cache\RefinableCacheableDependencyInterface;
use Drupal\Core\Url;
use Drupal\Tests\rest\Functional\CookieResourceTestTrait;
use Drupal\Tests\rest\Functional\ResourceTestBase;

class ArticlesCollectionResourceTest extends ResourceTestBase {

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
  protected static $resourceConfigId = 'articles_collection_resource';

  /**
   * {@inheritdoc}
   */
  protected static $modules = ['articles'];

  /**
   * @return void
   */
  protected function setUp(): void {
    parent::setUp();

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
        $this->grantPermissionsToAnonymousRole(['restful get articles_collection_resource']);
        break;

      default:
        throw new \UnexpectedValueException();
    }
  }

  /**
   * @return void
   * @throws \Behat\Mink\Exception\ExpectationException
   */
  public function testAnonymousResponse() {
    $this->drupalGet('api/v1/articles');
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
