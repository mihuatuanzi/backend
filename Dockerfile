FROM php:8.2.0-fpm-alpine3.17 AS builder

# composer installer
ARG COMPOSER_INSTALLER=/usr/src/installer
# composer installer src
ARG COMPOSER_SRC=https://install.phpcomposer.com/installer

RUN set -ex \
    && export https_proxy=http://192.168.0.101:2806 && http_proxy=http://192.168.0.101:2806 && all_proxy=socks5://192.168.0.101:2806 \
    && apk update \
    && apk add --no-cache --virtual .build-deps \
        $PHPIZE_DEPS curl-dev icu-dev libzip-dev openssl-dev pcre-dev pcre2-dev zlib-dev \
    && apk add bash unzip libzip shadow libstdc++ icu icu-libs icu-data-full curl openssl \
    && pear config-set http_proxy http://192.168.0.101:2806 \
    && docker-php-source extract \
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

EXPOSE 9000


FROM builder AS app
#
#RUN #set -ex && addgroup -g 1000 -S app_group \
##    && addgroup --system docker \
##    && adduser -G app_group -u 1000 -h /home/app_user --disabled-password --ingroup "docker" -S app_user

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
COPY ingress/templates /etc/nginx/templates
COPY ingress/nginx.conf /etc/nginx/nginx.conf

WORKDIR /var/www/app
