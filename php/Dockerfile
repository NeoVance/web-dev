FROM phpdockerio/php7-fpm

RUN apt-get install -y curl
RUN curl -s https://packagecloud.io/install/repositories/phalcon/stable/script.deb.sh | bash
RUN apt-get install -y php7.0-phalcon php7.0-xml php7.0-mbstring php7.0-xdebug php7.0-mysql
