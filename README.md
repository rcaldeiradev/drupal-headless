Explore headless rendering of Drupal contents using various front-end approaches
with REST in this experimental repository. Note that it belongs to my personal 
lab and is not suitable for production websites without proper adaptation.

## Requirements
- [DDEV](https://ddev.readthedocs.io/en/stable/) v1.2+ with [Docker](https://ddev.readthedocs.io/en/stable/users/install/docker-installation/])
- [PHP 8.2+](https://www.php.net/releases/)
- [Composer 2.5+](https://getcomposer.org/)


---
## Back-end setup

### Drupal 10

#### Setup

1. Go to the Drupal 10 directory `cd drupal10`.
2. Install the Composer dependencies `composer install`.
3. Start the DDEV Docker containers with `ddev start`.

Project can be reached at http://drupal10.ddev.site

#### Executing the automated tests in the Articles module

1. SSH into the web container `ddev ssh`.
2. Execute PHPUnit passing the Articles module directory parameter `./vendor/bin/phpunit ./web/modules/custom/articles`

## Front-end setup

### Next.js

Coming soon.