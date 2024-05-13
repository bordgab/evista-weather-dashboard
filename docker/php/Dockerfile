FROM php:8.2-fpm

# Install minimum configuration
RUN apt update -y && \
    apt-get autoremove -y && \
    apt-get install --no-install-recommends -y \
    apt-utils \
    libpq-dev \
    autoconf \
    wget \
    git \
    zip unzip && \
    apt-get update && \
    rm -rf /var/lib/apt/lists/* && \
    # Install PHP extensions \
    pecl install xdebug && \
    docker-php-ext-install pdo pdo_pgsql && \
    docker-php-ext-enable xdebug && \
    docker-php-source delete && \
    # Install Composer
    curl -sS https://getcomposer.org/installer | php && \
    mv composer.phar /usr/local/bin/composer

WORKDIR /var/www/http

COPY entrypoint.sh /entrypoint
COPY xdebug.ini /usr/local/etc/php/conf.d/xdebug.ini
RUN chmod +x /entrypoint
