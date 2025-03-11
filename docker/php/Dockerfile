FROM php:8.3-fpm

MAINTAINER Grzegorz Gabryel <greg@grzegab.eu>

RUN apt-get update && apt-get install -y libpq-dev libzip-dev libicu-dev \
    && rm -rf /var/lib/apt/lists/*

RUN pecl install pcov
RUN pecl install redis
RUN pecl install xdebug

RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql intl opcache
RUN docker-php-ext-enable opcache pcov redis xdebug

# Override with custom opcache settings
COPY php-additional.ini $PHP_INI_DIR/conf.d/

#Install composer form docker
COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /data/www
