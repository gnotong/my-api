# ---- PHP code ----
FROM php:7.4-fpm
RUN docker-php-ext-install pdo_mysql
RUN pecl install apcu
RUN apt-get update && \
    apt-get install -y \
    zlib1g-dev \
    libxml2-dev \
    libzip-dev \
    g++ \
    # Rabbit MQ
    librabbitmq-dev \
    libssh-dev \
    # imagick helps to use liip_imagine bundle
    libmagickwand-dev --no-install-recommends

RUN docker-php-ext-configure intl && \
    docker-php-ext-install zip intl && \
    docker-php-ext-enable apcu && \
    # imagick helps to use liip_imagine bundle
    pecl install imagick && \
    docker-php-ext-enable imagick && \
    # Rabbit MQ
    docker-php-ext-install bcmath sockets \
    && pecl install amqp \
    && docker-php-ext-enable amqp

COPY ./project /app/my-api

WORKDIR /app/my-api
