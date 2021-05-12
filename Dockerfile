FROM php:8.0-fpm

RUN apt-get update \
    && apt-get install -y --no-install-recommends libssl-dev \
        zlib1g-dev \
        curl \
        git \
        unzip \
        netcat \
        libxml2-dev \
        libpq-dev \
        libzip-dev \
        wget \
    && pecl install apcu \
    && docker-php-ext-install -j$(nproc) zip \
        opcache \
        intl \
        pdo_mysql \
        mysqli \
    && docker-php-ext-enable apcu \
        pdo_mysql \
        sodium \
    && wget https://get.symfony.com/cli/installer -O - | bash \
    && mv /root/.symfony/bin/symfony /usr/local/bin/symfony \
    && ls -l \
    && apt-get clean && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

COPY --from=composer /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/

EXPOSE 9000

CMD composer i -o; symfony serve --port=8000