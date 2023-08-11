Explore headless rendering of Drupal contents using various front-end approaches
with REST in this experimental repository. Note that it belongs to my personal 
lab and is not suitable for production websites without proper adaptation.

---
## Drupal 10 - Back-end local setup

### Requirements

- [DDEV](https://ddev.readthedocs.io/en/stable/) v1.2+ with [Docker](https://ddev.readthedocs.io/en/stable/users/install/docker-installation/])
- [PHP 8.2+](https://www.php.net/releases/)
- [Composer 2.5+](https://getcomposer.org/)

### Setup

1. Go to the Drupal 10 directory: `cd drupal10`.
2. Install the Composer dependencies: `composer install`.
3. Start the DDEV Docker containers: `ddev start`.
4. Install the Drupal website – the "Drupal Headless" distribution profile will be used: `ddev drush si -y`.
5. Generate a few dummy articles: `ddev drush devel-generate:content 10 --bundles='article'`.
6. Clear Drupal caches `ddev drush cr`.

The project can be reached at http://drupal10.ddev.site

### Endpoints

Articles collection:  
**GET** `/api/v1/articles`.

Article by ID:  
**GET** `/api/v1/articles/[id]`.

:bulb: you can import the drupal10/drupal_headless.postman_collection.json collection into Postman to test all the
available endpoints more quickly.

### Executing the automated tests in the Articles module

1. SSH into the web container `ddev ssh`.
2. Execute PHPUnit passing the Articles module directory parameter `./vendor/bin/phpunit ./web/modules/custom/articles`

## Next.js - Front-end local setup

### Requirements

- [Node 18.0+](https://nodejs.org/en/download)

### Setup

1. Go to the Next.js directory: `cd next.js`.
2. Install the Node dependencies: `npm install`.
3. Start the Next.js dev environment: `npm run dev`

The project can be reached at http://localhost:3000