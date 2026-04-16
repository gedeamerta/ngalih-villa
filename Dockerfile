FROM node:20-alpine AS frontend-build
WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js postcss.config.js tailwind.config.js ./
RUN npm run build

FROM php:8.4-cli-alpine AS vendor-build
WORKDIR /app
RUN apk add --no-cache \
    curl \
    git \
    unzip \
    icu-dev \
    libzip-dev \
    && docker-php-ext-install intl zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --no-progress --optimize-autoloader

FROM php:8.4-cli-alpine
WORKDIR /app

RUN apk add --no-cache \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite-libs \
    && docker-php-ext-install intl zip pdo_mysql

COPY --from=vendor-build /app/vendor /app/vendor
COPY --from=frontend-build /app/public/build /app/public/build

COPY . .

RUN php artisan storage:link || true

ENV APP_ENV=production
ENV APP_DEBUG=false

CMD php artisan migrate --force || true && php artisan storage:link || true && php -S 0.0.0.0:${PORT:-8000} -t public
