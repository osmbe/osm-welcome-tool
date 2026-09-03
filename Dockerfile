# Node.js (Build assets)

FROM dhi.io/node:24-alpine-dev AS node

WORKDIR /assets

COPY . .

RUN npm install
RUN npm run build

# Composer

FROM dhi.io/composer:2.2-alpine-php8.4-dev AS composer

# Application

FROM docker.io/dunglas/frankenphp:1-php8.4 AS app

## Install PHP dependencies

RUN install-php-extensions intl pdo_pgsql sodium zip

## Copy/Clean files

ENV APP_ENV=prod

COPY ./Caddyfile /etc/frankenphp/Caddyfile
COPY . /app
COPY --from=node "/assets/public/build" "/app/public/build"

# ## Install Composer & Dependencies

COPY --from=composer "/usr/local/bin/composer" "/usr/local/bin/composer"

RUN composer validate
RUN composer install --no-ansi --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts --no-dev
RUN composer clear-cache
# RUN composer dump-env ${APP_ENV}
RUN composer run-script post-install-cmd --no-dev

###> recipes ###
###< recipes ###
