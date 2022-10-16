# Installing OpenStreetMap Welcome Tool

## Requirements

- Install dependencies:

  ```cmd
  composer install
  ```

- Create database (if needed):

  ```cmd
  php bin/console doctrine:database:create
  ```

- Initialize schema:

  ```cmd
  php bin/console doctrine:schema:create
  ```

- Create `OSMCHA_API_KEY` in your environment with your [OSMCha](https://osmcha.org/) API key

## Run locally

### Symfony Local Web Server

- Install [Symfony CLI](https://symfony.com/download)
- Run `symfony server:start`
- Browse the given URL

Check [Symfony local server documentation](https://symfony.com/doc/current/setup/symfony_server.html) for more information.

### Docker

```cmd
docker build . --tag osm-welcome-tool
docker run --detach --publish 80:80 --env-file .env.local osm-welcome-tool
```

## Deploy

Check [Symfony deployment documentation](https://symfony.com/doc/current/deployment.html).
