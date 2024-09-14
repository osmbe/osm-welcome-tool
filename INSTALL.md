## Installing OpenStreetMap Welcome Tool

### Requirements

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

### Run locally

#### Symfony Local Web Server

- Install [Symfony CLI](https://symfony.com/download)
- Run `symfony server:start`
- Browse the given URL

Check [Symfony local server documentation](https://symfony.com/doc/current/setup/symfony_server.html) for more information.

#### Docker

```cmd
docker build . --tag osm-welcome-tool
docker run --detach --publish 80:80 --env-file .env.local osm-welcome-tool
```

### Deploy

Check [Symfony deployment documentation](https://symfony.com/doc/current/deployment.html).

#### Using Deployer

```cmd
vendor/bin/dep deploy --branch=2.x welcome.osm.be
```

## Translating OpenStreetMap Welcome Tool

1. Update XLIFF files for English

    ```cmd
    php bin/console translation:extract --force en
    ```

1. Upload XLIFF files for English to Crowdin

    ```cmd
    php bin/console translation:push --force --locales en
    ```

1. Update translations in [Crowdin](https://crowdin.com/project/osm-welcome-tool)

1. Download XLIFF files from Crowdin

    ```cmd
    php bin/console translation:pull --force
    ```
