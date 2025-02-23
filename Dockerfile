FROM php:8.3.17-fpm-alpine3.21 AS base

WORKDIR /app

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

COPY ./docker/php/php.ini $PHP_INI_DIR/conf.d/php.ini
COPY ./docker/php/opcache.ini $PHP_INI_DIR/conf.d/opcache.ini
COPY ./docker/php/xdebug.ini $PHP_INI_DIR/conf.d/xdebug.ini

RUN install-php-extensions apcu intl opcache zip
RUN install-php-extensions xdebug-3.4.1

ENV APP_ENV="dev"
ENV APP_DEBUG=1

