FROM php:8.2-cli-alpine

RUN apk add --no-cache $PHPIZE_DEPS && \
    pecl -q install mongodb && \
    docker-php-ext-enable mongodb && \
    curl -s https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /app
