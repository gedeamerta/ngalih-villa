FROM php:8.4-cli-alpine AS vendor-build
WORKDIR /app
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    $PHPIZE_DEPS \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip mbstring pdo_mysql gd
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader --no-scripts

FROM node:20-alpine AS frontend-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
COPY --from=vendor-build /app/vendor/tightenco/ziggy /app/vendor/tightenco/ziggy
RUN npm run build

FROM php:8.4-cli-alpine
WORKDIR /app

RUN apk add --no-cache \
    $PHPIZE_DEPS \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    sqlite-libs \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install intl zip mbstring pdo_mysql gd

COPY --from=vendor-build /app/vendor /app/vendor
COPY --from=frontend-build /app/public/build /app/public/build

COPY . .

RUN mkdir -p \
    storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache

RUN php artisan storage:link || true

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV LOG_CHANNEL=stderr
ENV LOG_LEVEL=debug

CMD php artisan package:discover --ansi && php artisan migrate --force && (php artisan storage:link || true) && php -S 0.0.0.0:${PORT:-8000} -t public
