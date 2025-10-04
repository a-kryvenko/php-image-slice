# Launch with: docker build -t php-image-size . && docker run --rm -v "$(pwd)":/app php-image-size composer u && php my_code.php
FROM php:cli

RUN apt-get update && apt-get install -y \
    libjpeg-dev \
    libpng-dev \
    libwebp-dev \
    libxpm-dev \
    libfreetype-dev \
    libzip-dev \
    git \
    unzip \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install zip

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
#COPY . /app

WORKDIR /app