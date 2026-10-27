
FROM php:8.2-apache

# Instalar soporte de PHP para MySQL
RUN docker-php-ext-install pdo_mysql

# Habilitar las reglas MVC de Apache
RUN a2enmod rewrite

# Configurar Apache para el puerto predeterminado de Render
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' \
    /etc/apache2/sites-available/000-default.conf

# Permitir las reglas definidas en .htaccess
RUN printf '\n<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
    >> /etc/apache2/apache2.conf

# Carpeta pública de Apache
WORKDIR /var/www/html

# Copiar el contenido de app, no la carpeta app completa
COPY app/ /var/www/html/

EXPOSE 10000

CMD ["apache2-foreground"]
