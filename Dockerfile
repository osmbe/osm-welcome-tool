# Step 1 : Build JavaScript & CSS assets

FROM node:16 as node

WORKDIR /usr/app

COPY . .

RUN npm install
RUN npm run build

# Step 2

FROM php:8.1-apache

WORKDIR /var/www/app

# Install dependencies
RUN apt-get update -y && apt-get upgrade -y
RUN apt-get install -y git unzip libicu-dev libpq-dev libsodium-dev libzip-dev

# Configure Apache
COPY .docker/apache.conf /etc/apache2/sites-available/app.conf
RUN a2enmod rewrite alias
RUN a2dissite 000-default
RUN a2ensite app
RUN apache2ctl restart

# Install required PHP extensions
RUN docker-php-ext-install -j$(nproc) iconv intl pdo_pgsql sodium zip

# Copy files
COPY --chown=www-data . .

# Install Composer
COPY .docker/composer.sh .
RUN chmod +x ./composer.sh
RUN ./composer.sh
RUN rm ./composer.sh

# Install dependencies
RUN php composer.phar install --no-progress --no-interaction

# Install assets
COPY --chown=www-data --from=node /usr/app/public/build ./public/build

EXPOSE 80