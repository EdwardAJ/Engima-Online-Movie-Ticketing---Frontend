FROM php:7.2-apache
SHELL ["/bin/bash", "-c"]
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli 
RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data
RUN apt update
RUN apt install -y iproute2
RUN apt install -y nano
RUN apt install -y libxml2-dev
RUN docker-php-ext-install soap
RUN echo "export HOST_IP=`ip route show | awk '/default/ {print $3}'`" >> /etc/apache2/envvars
RUN /etc/init.d/apache2 restart