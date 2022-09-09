FROM php:8.1-cli-alpine

RUN apk add build-base php8-dev && \
    pecl -q install mongodb && \
    docker-php-ext-enable mongodb && \
    curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
