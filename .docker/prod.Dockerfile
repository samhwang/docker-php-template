# Composer dependencies
FROM composer:1.9 as vendor

COPY composer.json composer.json
COPY composer.lock composer.lock

RUN composer install \
    --no-interaction \
    --no-plugins \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative

# Production Image
FROM samhwang/php:7.3
LABEL maintainer="Sam Huynh <samhwang2112.dev@gmail.com>"

ENV SSLKey=".docker/ssl/server.key"
ENV SSLCert=".docker/ssl/server.crt"

WORKDIR /var/www/html

COPY ./.docker/ssl ./.docker/ssl
RUN if [ ! -e "$SSLKey" ] && [ ! -e "$SSLCert" ]; then \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/server.key -out /etc/ssl/server.crt -subj "/C=AU/ST=VIC/L=Melbourne/O=Localhost/CN=Localhost"; \
    else \
    cp -r .docker/ssl /etc/ssl; \
    fi; \
    rm -rf .docker;

COPY --from=project_name:development /var/www/html/public ./public
COPY --from=project_name:development /var/www/html/src ./src
COPY --from=vendor /app/vendor/ ./vendor/