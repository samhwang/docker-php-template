# Development image
FROM samhwang/php:7.4-alpine as development
LABEL maintainer="Sam Huynh <samhwang2112.dev@gmail.com>"

WORKDIR /var/www/html

# Install composer dependencies
COPY --from=composer:1.10.6 /usr/bin/composer /usr/bin/composer
COPY composer.json composer.lock ./

RUN composer install \
    --no-interaction \
    --no-plugins \
    --prefer-dist; \
    composer clear-cache

ENV SSLKey=".docker/ssl/server.key"
ENV SSLCert=".docker/ssl/server.crt"

# Dev environment config: MailHog SMTP, SSL Keys and PCOV
RUN curl -LkSso /usr/bin/mhsendmail 'https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64' && \
    chmod 0755 /usr/bin/mhsendmail && \
    echo 'sendmail_path = "/usr/bin/mhsendmail --smtp-addr=mailhog:1025"' >> /usr/local/etc/php/php.ini && \
    apk update && apk add --no-cache --virtual build-deps ${PHPIZE_DEPS} && \
    pecl install pcov && \
    pecl clear-cache && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/php.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "display_errors = On" >> /usr/local/etc/php/php.ini && \
    echo "pcov.directory = /var/www/html/src" >> /usr/local/etc/php/php.ini && \
    echo "pcov.exclude = /var/www/html/vendor" >> /usr/local/etc/php/php.ini; \
    apk del build-deps;

COPY . .
RUN if [ ! -e "$SSLKey" ] && [ ! -e "$SSLCert" ]; then \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/server.key -out /etc/ssl/server.crt -subj "/C=AU/ST=VIC/L=Melbourne/O=Localhost/CN=Localhost"; \
    else \
    cp -r .docker/ssl /etc/ssl; \
    fi; \
    rm -rf .docker;

# Build for production
FROM development as build

RUN composer install \
    --no-interaction \
    --no-plugins \
    --prefer-dist \
    --no-dev \
    --optimize-autoloader \
    --classmap-authoritative; \
    composer clear-cache;

# Production image
FROM samhwang/php:7.4-alpine as production
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

COPY ./public ./public
COPY ./src ./src
COPY --from=build /var/www/html/vendor/ ./vendor/