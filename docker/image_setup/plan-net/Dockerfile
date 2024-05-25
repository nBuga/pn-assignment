FROM ubuntu:22.04

MAINTAINER buga.nelu@gmail.com

# so we won't get errors related to interactive mode
ENV DEBIAN_FRONTEND noninteractive

RUN apt-get update -y && apt-get install -y apt-transport-https wget

# apt update and install required packages
# It should be run in the same RUN command. Otherwise it will not install git
RUN apt-get update && apt-get install -y software-properties-common

RUN add-apt-repository ppa:ondrej/php && apt-get update && apt-get install -y php8.2

RUN apt-get update && apt-get install -y apache2 supervisor vim git-core curl \
    php8.2-dev php8.2-fpm php8.2-ctype php8.2-apcu php8.2-memcached php8.2-curl php8.2-mysql php8.2-mysqli php8.2-ldap \
    php8.2-zip php8.2-opcache php8.2-xml php8.2-bcmath php8.2-soap php8.2-gd php8.2-mbstring php8.2-sqlite3 \
    php8.2-xdebug php8.2-phar php8.2-fileinfo php8.2-gd  php8.2-iconv php8.2-intl php8.2-dom ruby-full htop screen \
    php8.2-xmlreader php8.2-xmlwriter php8.2-simplexml php8.2-tokenizer node-uglify wget mysql-client telnet

# nvm, node
RUN rm /bin/sh && ln -s /bin/bash /bin/sh
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/v0.39.1/install.sh | bash
RUN source ~/.nvm/nvm.sh && nvm install 20.12.2 && nvm use 20.12.2

# Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# setup apache vhost
COPY resources/01-docker.plan-net.apache /etc/apache2/sites-available/01-docker.plan-net.conf
RUN ln -s /etc/apache2/sites-available/01-docker.plan-net.conf /etc/apache2/sites-enabled/01-docker.plan-net.conf
RUN rm /etc/apache2/sites-available/000-default.conf
RUN rm /etc/apache2/sites-available/default-ssl.conf
RUN rm /etc/apache2/sites-enabled/000-default.conf

# setup apache modules
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

# temp
RUN rm /var/www/html/index.html
RUN echo "<?php phpinfo();" > /var/www/html/index.php

# setup supervisor
COPY ./resources/supervisord.conf /etc/supervisor/supervisord.conf

# supervisord watches and keeps processes open
CMD /usr/bin/supervisord -c /etc/supervisor/supervisord.conf

# expose ports
EXPOSE 80 8888