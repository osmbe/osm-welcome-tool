# Installing OpenStreetMap Welcome Tool

## Requirements

- Install dependencies:

  ```cmd
  composer install
  ```

- Create database:

  ```cmd
  php bin/console doctrine:database:create
  php bin/console doctrine:schema:create
  ```

- Create `OSMCHA_API_KEY` in your environment with your [OSMCha](https://osmcha.org/) API key

## Run locally

- Install [Symfony CLI](https://symfony.com/download)
- Run `symfony server:start`
- Browse the given URL

Check [Symfony local server documentation](https://symfony.com/doc/current/setup/symfony_server.html) for more information.

## Deploy

Check [Symfony deployment documentation](https://symfony.com/doc/current/deployment.html).
