# Node.js (Build assets)

FROM node:16-alpine as node

WORKDIR /assets

COPY . .

RUN npm install
RUN npm run build

# Composer

FROM composer:2 as composer

# Application

FROM php:8.1-apache as app

## Install PHP dependencies

RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y libicu-dev libpq-dev libsodium-dev libzip-dev

RUN docker-php-ext-configure zip;
RUN docker-php-ext-install -j$(nproc) intl pdo_pgsql sodium zip

## Install Symfony CLI

# RUN curl -1sLf 'https://dl.cloudsmith.io/public/symfony/stable/setup.deb.sh' | bash
# RUN apt-get install symfony-cli

## Configure Apache

COPY ".docker/apache/app.conf" "/etc/apache2/sites-available/"

RUN a2enmod rewrite alias
RUN a2dissite 000-default
RUN a2ensite app
RUN apache2ctl restart

## Copy/Clean files

ENV APP_ENV=prod
# ENV APP_ENV=dev

WORKDIR "/var/www/app"

COPY --chown=www-data . .
COPY .docker/cron.daily/welcome-update.sh /etc/cron.daily/welcome-update

RUN rm -Rf .docker/

COPY --from=node --chown=www-data "/assets/public/build" "./public/build"

## Install Composer & Dependencies

COPY --from=composer "/usr/bin/composer" "/usr/bin/composer"

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer validate
RUN composer install --no-ansi --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts --no-dev
# RUN composer install --no-ansi --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts
RUN composer clear-cache
RUN composer dump-env prod
RUN composer run-script post-install-cmd --no-dev
# RUN composer run-script post-install-cmd
RUN chmod +x bin/console; sync;

###> recipes ###
###< recipes ###

## Check Symfony requirements

# RUN symfony check:requirements

## Finalize

RUN mkdir -p var/cache/${APP_ENV} var/log/
RUN chown -R www-data:www-data var/

EXPOSE 80
