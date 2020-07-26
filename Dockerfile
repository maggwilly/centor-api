FROM romeoz/docker-apache-php:7.0
# default document root is /var/app/web folder
COPY ./ /var/www/app

# example how to install app in the container
RUN apt-get update &&  apt-get install -y curl
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && mv composer.phar /usr/local/bin/composer
RUN apt-get install -y libapache2-mod-php7.0 php7.0-common php7.0-pgsql  php7.0-curl php7.0-json php7.0-cgi php7.0-gd php-amqplib php7.0-bcmath
VOLUME ["/var/www/app"]

COPY  ./000-default.conf /etc/apache2/sites-available/000-default.conf
COPY  ./000-default.conf /etc/apache2/sites-enabled/app.conf
WORKDIR /var/www/app/
RUN composer install --no-interaction --prefer-source
EXPOSE 80
