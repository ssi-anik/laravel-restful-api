FROM php:7.1.1-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev curl nano zip libpng-dev

RUN docker-php-ext-install mcrypt pdo_mysql gd

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis \
    &&  rm -rf /tmp/pear \
    &&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini