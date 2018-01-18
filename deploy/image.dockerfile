FROM php:7.1.8-fpm

RUN apt-get update && apt-get install -y nginx libmcrypt-dev curl nano zip libpng-dev redis-server

RUN docker-php-ext-install mcrypt pdo_mysql gd

#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN pecl install -o -f redis &&  rm -rf /tmp/pear &&  echo "extension=redis.so" > /usr/local/etc/php/conf.d/redis.ini

RUN rm -rf /var/lib/apt/lists/*

COPY . /var/www/html
WORKDIR /var/www/html

RUN rm /etc/nginx/sites-enabled/default

COPY ./deploy/nginx/default.conf /etc/nginx/conf.d/default.conf

RUN usermod -a -G www-data root
RUN chgrp -R www-data storage

RUN chown -R www-data:www-data ./storage
RUN chmod -R 0777 ./storage

#RUN ln -s ./secret/.env .env

RUN chmod +x ./deploy/run

ENTRYPOINT ["./deploy/run"]

EXPOSE 80