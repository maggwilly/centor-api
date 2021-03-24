FROM romeoz/docker-apache-php:7.0
# default document root is /var/app/web folder
COPY ./ /var/www/app

# example how to install app in the container
RUN apt-get update &&  apt-get install -y curl tar file xz-utils build-essential
RUN curl -sSk https://getcomposer.org/installer | php -- --disable-tls && mv composer.phar /usr/local/bin/composer
RUN apt-get install -y libapache2-mod-php7.0 php7.0-common php7.0-pgsql  php7.0-curl php7.0-json php7.0-cgi php7.0-gd php-amqplib php7.0-bcmath
RUN \
curl -sfLO https://download.imagemagick.org/ImageMagick/download/ImageMagick-7.0.11-4.tar.gz && \
echo 'cf51a1c6ebf627c627a8e6ac20aecce5f1425907c2cdb98c5a60f329c5c6caf2  ImageMagick-7.0.11-4.tar.gz' | sha256sum -c - && \
tar -xzf ImageMagick-7.0.11-4.tar.gz && \
cd ImageMagick-7.0.11-4.tar.gz && \
./configure --prefix /usr/local && \
make install && \
cd .. && \
rm -rf  ImageMagick*
VOLUME ["/var/www/app"]

COPY  ./000-default.conf /etc/apache2/sites-available/000-default.conf
COPY  ./000-default.conf /etc/apache2/sites-enabled/app.conf
WORKDIR /var/www/app/
EXPOSE 80
