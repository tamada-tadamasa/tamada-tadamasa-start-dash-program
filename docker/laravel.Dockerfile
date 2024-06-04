FROM php:8.2-fpm

# Debian packages
RUN apt-get update && apt-get install -y \
    autoconf \
    bash \
    build-essential \
    curl \
    g++ \
    git \
    imagemagick libmagickwand-dev \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libpng-dev \
    libxml2-dev \
    make \
    openssl \
    unzip \
    zip \
    zlib1g-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions (Non-standard)
RUN pecl install imagick && docker-php-ext-enable imagick \
    && pecl install xdebug && docker-php-ext-enable xdebug


# PHP extensions (Standard)
RUN docker-php-ext-install \
    gd \
    opcache \
    pdo_mysql \
    && docker-php-ext-configure gd --with-freetype --with-jpeg

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer \
    && chmod +x /usr/local/bin/composer

WORKDIR /usr/share/nginx/html
