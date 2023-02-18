FROM php:8.2-fpm-alpine3.17 AS builder

# composer installer
ARG COMPOSER_INSTALLER=/usr/src/installer
# composer installer src
ARG COMPOSER_SRC=https://install.phpcomposer.com/installer

RUN set -ex \
    && export https_proxy=http://192.168.0.101:11475 && http_proxy=http://192.168.0.101:11475 && all_proxy=socks5://192.168.0.101:11475 \
    && apk update \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS \
        curl-dev \
        icu-dev \
        libzip-dev \
        openssl-dev \
        pcre-dev \
        pcre2-dev \
        zlib-dev \
    && apk add bash unzip libzip shadow libstdc++ icu icu-libs icu-data-full curl openssl \
    && pear config-set http_proxy http://192.168.0.101:11475 \
    && docker-php-source extract \
    && docker-php-ext-configure pdo_mysql --with-pdo-mysql=mysqlnd \
    && docker-php-ext-configure mysqli --with-mysqli=mysqlnd \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-configure exif && docker-php-ext-install -j$(nproc) exif \
    && pecl install zip && docker-php-ext-enable zip \
    && docker-php-ext-configure intl && docker-php-ext-install -j$(nproc) intl \
    && echo "extension=intl" > /usr/local/etc/php/conf.d/docker-php-ext-intl.ini \
# Composer::Install \
    && curl -o $COMPOSER_INSTALLER -sS $COMPOSER_SRC \
    && php $COMPOSER_INSTALLER --install-dir=/usr/local/bin/ --filename=composer2 \
    && ln -sf /usr/local/bin/composer2 /usr/local/bin/composer \
    && chmod +x /usr/local/bin/composer \
# Deps::Clear \
    && docker-php-source delete && apk del .build-deps \
    && ln -svf /usr/share/zoneinfo/UTC /etc/localtime

COPY deploy/php/php-production.ini /usr/local/etc/php/php.ini

EXPOSE 9000


FROM builder AS app

RUN usermod -u 1000 www-data && chown -R www-data:www-data /var/www
USER www-data

WORKDIR /var/www/app

COPY composer.* symfony.* ./

VOLUME /var/www/app/var/

RUN set -eux \
    && composer install --prefer-dist --no-dev --optimize-autoloader --no-scripts --no-progress \
    && composer clear-cache;

COPY . .


FROM nginx:1.23.3-alpine AS ingress

ENV NGINX_HOST=backend.ingress.mihuatuanzi.io
ENV APP_ENV=prod

COPY public /var/www/app/public
COPY deploy/proxy/templates /etc/nginx/templates
COPY deploy/proxy/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/app
