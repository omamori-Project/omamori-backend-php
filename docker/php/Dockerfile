FROM php:8.3-fpm-alpine

WORKDIR /var/www/html

# 시스템 패키지 설치
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libwebp-dev \
    libzip-dev \
    postgresql-dev \
    oniguruma-dev \
    icu-dev \
    imagemagick-dev \
    imagemagick \
    bash \
    vim \
    unzip

# 빌드 도구 설치
RUN apk add --no-cache --virtual .build-deps \
    $PHPIZE_DEPS \
    autoconf \
    g++ \
    make \
    linux-headers

# PHP 확장 설치
RUN docker-php-ext-configure gd \
    --with-freetype \
    --with-jpeg \
    --with-webp \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_pgsql \
    pgsql \
    gd \
    zip \
    bcmath \
    opcache \
    intl \
    exif \
    mbstring

# ImageMagick 확장
RUN pecl install imagick \
    && docker-php-ext-enable imagick

# Redis 확장
RUN pecl install redis \
    && docker-php-ext-enable redis

# 빌드 도구 제거
RUN apk del .build-deps

# Composer 설치
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# PHP 설정
RUN echo "upload_max_filesize = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size = 20M" >> /usr/local/etc/php/conf.d/uploads.ini \
    && echo "memory_limit = 512M" >> /usr/local/etc/php/conf.d/memory.ini \
    && echo "max_execution_time = 300" >> /usr/local/etc/php/conf.d/execution.ini \
    && echo "display_errors = On" >> /usr/local/etc/php/conf.d/development.ini \
    && echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/development.ini

# 권한 설정
RUN addgroup -g 1000 www && \
    adduser -D -u 1000 -G www www

USER www

EXPOSE 9000

CMD ["php-fpm"]
