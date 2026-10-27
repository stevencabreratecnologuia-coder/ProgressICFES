
FROM php:8.2-apache
RUN usermod -a -G 1000 www-data

# Instalar soporte para MySQL
RUN docker-php-ext-install pdo_mysql

# Habilitar las rutas de MVC
RUN a2enmod rewrite

# Configurar Apache para el puerto de Render y la carpeta public
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' /etc/apache2/sites-available/000-default.conf \
    && sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' /etc/apache2/sites-available/000-default.conf

# Permitir .htaccess dentro de public
RUN printf '\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
    >> /etc/apache2/apache2.conf

WORKDIR /var/www/html

# Copiar el proyecto completo, conservando app y public
COPY . /var/www/html/

EXPOSE 10000

CMD ["apache2-foreground"]
