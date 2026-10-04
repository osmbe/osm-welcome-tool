# Node.js (Build assets)

FROM dhi.io/node:24-alpine-dev AS node

WORKDIR /assets

COPY . .

RUN npm install
RUN npm run build

# Composer

FROM dhi.io/composer:2-alpine-php8.4-dev AS composer

# Application

FROM docker.io/dunglas/frankenphp:1-php8.4-alpine AS app

## Install PHP dependencies

RUN install-php-extensions intl pdo_pgsql pdo_sqlite sodium zip

# Non-root user setup

ARG USER=nonroot

RUN <<-EOF
	# Add non-root user
	adduser -D ${USER}
	# Add additional capability to bind to port 80 and 443
	# setcap CAP_NET_BIND_SERVICE=+eip /usr/local/bin/frankenphp
	# Give write access to /config/caddy and /data/caddy
	chown -R ${USER}:${USER} /config/caddy /data/caddy
EOF

## Copy/Clean files

ENV APP_ENV=prod

COPY ./Caddyfile /etc/frankenphp/Caddyfile

COPY --chown=${USER}:${USER} . /app
COPY --chown=${USER}:${USER} --from=node "/assets/public/build" "/app/public/build"

RUN chown -R ${USER}:${USER} /app

## Install Composer & Dependencies

COPY --from=composer "/usr/local/bin/composer" "/usr/local/bin/composer"

USER ${USER}

RUN composer validate
RUN composer install --no-ansi --no-interaction --no-progress --prefer-dist --optimize-autoloader --no-scripts --no-dev
RUN composer clear-cache
# RUN composer dump-env ${APP_ENV}
RUN composer run-script post-install-cmd --no-dev

###> recipes ###
###< recipes ###

EXPOSE 8080
