# Node.js (Build assets)

FROM dhi.io/node:24-alpine-dev AS node

WORKDIR /assets

COPY . .

RUN npm install
RUN npm run build

# Composer

# FROM dhi.io/composer:2.2-alpine-php8.4-dev AS composer

# Application

FROM dunglas/frankenphp:1-php8.4 AS app

# COPY . /app/public
# COPY --from=node "/assets/public/build" "/app/public/build"

# EXPOSE 80

# ## Install PHP dependencies

# RUN apt-get update -y \
#     && apt-get install -y libicu-dev libpq-dev libsodium-dev libzip-dev \
#     && apt-get clean \
#     && rm -rf /var/lib/apt/lists/*

# RUN docker-php-ext-configure zip;
# RUN docker-php-ext-install -j$(nproc) intl pdo_pgsql sodium zip

# ## Configure Apache

# COPY ".docker/apache/app.conf" "/etc/apache2/sites-available/"
# COPY ".docker/apache/ports.conf" "/etc/apache2/"

# RUN a2enmod rewrite alias
# RUN a2dissite 000-default
# RUN a2ensite app

# ## Copy/Clean files

# ENV APP_ENV=prod

# WORKDIR "/var/www/app"

# COPY --chown=www-data . .

# RUN rm -Rf .docker/

# COPY --from=node --chown=www-data "/assets/public/build" "./public/build"

# ## Install Composer & Dependencies

# COPY --from=composer "/usr/local/bin/composer" "/usr/local/bin/composer"

# RUN composer validate
# RUN composer install --no-ansi --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts --no-dev
# RUN composer clear-cache
# # RUN composer dump-env prod
# RUN composer run-script post-install-cmd --no-dev

# ###> recipes ###
# ###< recipes ###

# ## Finalize

# RUN mkdir -p var/cache/${APP_ENV} var/log/
# RUN chown -R www-data:www-data var/

# EXPOSE 8080
