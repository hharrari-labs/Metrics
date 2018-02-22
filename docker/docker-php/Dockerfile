FROM php:7.1-fpm-alpine

ENV APCU_VERSION 5.1.8
ENV COMPOSER_ALLOW_SUPERUSER 1

RUN apk add --no-cache --virtual .persistent-deps \
		git \
		icu-libs \
		zlib \
		freetype \
		libpng \
		libjpeg-turbo \
		libpq

RUN set -xe \
	&& apk add --no-cache --virtual .build-deps \
		$PHPIZE_DEPS \
		icu-dev \
		zlib-dev \
		freetype-dev \
		libpng-dev \
		libjpeg-turbo-dev \
		postgresql-dev \
    && docker-php-ext-configure gd \
        --with-gd \
        --with-freetype-dir=/usr/include/ \
        --with-png-dir=/usr/include/ \
        --with-jpeg-dir=/usr/include/ \
	&& docker-php-ext-install \
		intl \
		pdo_mysql \
		zip \
		gd \
		exif \
		bcmath \
	&& pecl install \
		apcu-${APCU_VERSION} \
		redis \
	&& docker-php-ext-enable --ini-name 20-apcu.ini apcu \
	&& docker-php-ext-enable --ini-name 05-opcache.ini opcache \
	&& docker-php-ext-enable --ini-name 05-redis.ini redis \
	&& apk del .build-deps

COPY docker/php/conf.d/php.ini /usr/local/etc/php/conf.d/00-conf.ini
COPY docker/php/install-composer.sh /usr/local/bin/docker-app-install-composer

RUN chmod +x /usr/local/bin/docker-app-install-composer

RUN set -xe \
    && apk add --no-cache \
        openssh-client \
	&& apk add --no-cache --virtual .fetch-deps \
		$PHPIZE_DEPS \
		openssl \
	&& docker-app-install-composer \
	&& mv composer.phar /usr/local/bin/composer \
	&& pecl install xdebug \
	&& docker-php-ext-enable --ini-name 20-xdebug.ini xdebug \
	&& apk del .fetch-deps

RUN composer global require \
        "hirak/prestissimo:^0.3" \
        "squizlabs/php_codesniffer:^3.0.2" \
        --prefer-dist --no-progress --no-suggest --optimize-autoloader --classmap-authoritative \
	&& composer clear-cache \
	&& ln -s /root/.composer/vendor/squizlabs/php_codesniffer/bin/phpcs /usr/local/bin/phpcs \
	&& ln -s /root/.composer/vendor/squizlabs/php_codesniffer/bin/phpcbf /usr/local/bin/phpcbf

WORKDIR /app

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-app-entrypoint
RUN chmod +x /usr/local/bin/docker-app-entrypoint

ENTRYPOINT ["docker-app-entrypoint"]
CMD ["php-fpm"]
