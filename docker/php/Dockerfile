FROM php:7.2-apache
RUN docker-php-ext-install mysqli
RUN docker-php-ext-enable mysqli 
RUN usermod -u 1000 www-data
RUN groupmod -g 1000 www-data