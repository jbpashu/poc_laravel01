ARG PHP_VERSION=7.4.6
ARG NGINX_VERSION=1.16

#
# Composer
#
FROM composer:1.7 as composer

WORKDIR /app

COPY database/ database/

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist

#
# NPM
#
FROM node:8.11 as frontend

RUN mkdir /app
WORKDIR /app

RUN mkdir -p /app/vendor
COPY --from=composer /app/vendor/ /app/vendor/

COPY package.json package-lock.json webpack.mix.js  /app/

COPY resources/ resources/
RUN npm install

RUN npm run production

#
# PHP Application
#
FROM php:${PHP_VERSION}-fpm-alpine as php

RUN set -ex \
  && apk update --no-cache \
  && apk --no-cache add \
  autoconf \
  curl \
  g++ \
  libpng-dev \
  libxml2-dev \
  make \
  oniguruma-dev \
  oniguruma \
  shadow \
  unzip \
  zip \
  && pecl install -f xdebug \
  && apk del --purge autoconf g++ make \
  && docker-php-ext-enable xdebug

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd opcache

# Opcache
ENV PHP_OPCACHE_VALIDATE_TIMESTAMPS="0" \
    PHP_OPCACHE_MAX_ACCELERATED_FILES="5000" \
    PHP_OPCACHE_MEMORY_CONSUMPTION="192" \
    PHP_OPCACHE_MAX_WASTED_PERCENTAGE="10"

COPY docker/php/conf.d/opcache.ini /tmp/opcache.ini
RUN  cp /tmp/opcache.ini $(php-config --ini-dir)/opcache.ini

COPY docker/php/conf.d/xdebug.ini /tmp/xdebug.ini
RUN  cp /tmp/xdebug.ini $(php-config --ini-dir)/xdebug.ini

COPY --from=composer /app/vendor/ /var/www/html/vendor/
COPY --from=frontend /app/public/js/ /var/www/html/public/js/
COPY --from=frontend /app/public/css/ /var/www/html/public/css/
COPY --from=frontend /app/mix-manifest.json /var/www/html/public/mix-manifest.json
COPY --from=frontend /app/mix-manifest.json /var/www/html/mix-manifest.json

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

COPY --chown=www-data:www-data . /var/www/html

RUN chmod -R 755 /var/www/html/storage

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

#
# Nginx Proxy
#
FROM nginx:${NGINX_VERSION}-alpine AS nginx

# Remove any existing config files
RUN  rm /etc/nginx/conf.d/* /etc/nginx/nginx.conf
COPY docker/nginx/conf.d/api.conf /etc/nginx/conf.d/api.tmpl
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf

RUN mkdir -p /var/www/html
WORKDIR /var/www/html

COPY --from=php /var/www/html/public public/
COPY --from=frontend /app/public public/

ARG UPSTREAM=api_php
ENV UPSTREAM $UPSTREAM
CMD /bin/sh -c "envsubst '\$UPSTREAM' < /etc/nginx/conf.d/api.tmpl > /etc/nginx/conf.d/api.conf && nginx -g 'daemon off;' || cat /etc/nginx/conf.d/api.conf"
