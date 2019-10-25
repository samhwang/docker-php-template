# Composer dependencies
FROM composer:1.9 as vendor

ARG ENVIRONMENT=development
ENV PARAM="--no-dev -o"
COPY composer.json composer.json
COPY composer.lock composer.lock

RUN if [ "$ENVIRONMENT" = "development" ]; then \
    PARAM=""; \
    fi; \
    composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --ignore-platform-reqs \
    ${PARAM}

# The project image
FROM samhwang/php:7.3
LABEL maintainer="Sam Huynh <samhwang2112.dev@gmail.com>"

ARG ENVIRONMENT=development
ARG XDEBUG_ENABLE=true
ENV SSLKey=".docker/ssl/server.key"
ENV SSLCert=".docker/ssl/server.crt"

# Dev environment config: MailHog SMTP, SSL Keys and XDebug
RUN if [ "$ENVIRONMENT" = 'development' ]; then \
    curl -LkSso /usr/bin/mhsendmail 'https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64' && \
    chmod 0755 /usr/bin/mhsendmail && \
    echo 'sendmail_path = "/usr/bin/mhsendmail --smtp-addr=mailhog:1025"' > /usr/local/etc/php/php.ini; \
    if [ "$XDEBUG_ENABLE" = true ]; then \
    pecl install xdebug && \
    docker-php-ext-enable xdebug && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini; \
    fi; \
    fi;

WORKDIR /var/www/html
COPY . .
RUN if [ ! -e "$SSLKey" ] && [ ! -e "$SSLCert" ]; then \
    openssl req -x509 -nodes -days 365 -newkey rsa:2048 -keyout /etc/ssl/server.key -out /etc/ssl/server.crt -subj "/C=AU/ST=VIC/L=Melbourne/O=Localhost/CN=Localhost"; \
    else \
    cp -r .docker/ssl /etc/ssl; \
    fi; \
    rm -rf .docker;

COPY --from=vendor /app/vendor/ ./vendor/
