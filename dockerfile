FROM php:8.1

RUN buildDeps="libpq-dev libzip-dev libicu-dev" && \
    apt-get update && \
    apt-get install -y $buildDeps --no-install-recommends

RUN docker-php-ext-install \
    pdo \
    pdo_mysql

RUN pecl install xdebug-3.1.5 \
	&& docker-php-ext-enable xdebug

RUN mkdir -p /usr/src/app

WORKDIR /usr/src/app
