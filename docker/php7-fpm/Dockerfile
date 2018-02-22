FROM php:7.0-fpm
 
MAINTAINER Mathieu Thomas <mthomas@blahp.fr>
 
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    zlib1g-dev \
    libicu-dev \
    g++ 
    
# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer --version

 
# Set timezone
RUN rm /etc/localtime
RUN ln -s /usr/share/zoneinfo/Europe/Paris /etc/localtime
RUN "date"
 
# Install APCu v5.1.7
RUN mkdir -p /usr/src/php/ext
RUN curl -L -o /tmp/apcu-5.1.7.tgz https://pecl.php.net/get/apcu-5.1.7.tgz
RUN tar xfz /tmp/apcu-5.1.7.tgz
RUN rm -r /tmp/apcu-5.1.7.tgz
RUN mv apcu-5.1.7  /usr/src/php/ext/apcu
 
# Enable apc
RUN echo "apc.enable_cli = On" >> /usr/local/etc/php/conf.d/docker-php-ext-apcu.ini
 
# install and active xdebug
RUN docker-php-ext-install pdo_mysql curl intl json
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_enable=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_connect_back=1" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.idekey=\"PHPSTORM\"" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
RUN echo "xdebug.remote_port=9001" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
 
# Configure short open tag for Symfony
RUN echo "short_open_tag = Off" >> /usr/local/etc/php/php.ini
 
# for mac
RUN usermod -u 501 www-data
 
WORKDIR /var/www/app