
FROM php:8.2-apache

# Instalar soporte de PHP para MySQL y PDO
RUN docker-php-ext-install pdo_mysql

# Activar mod_rewrite para las rutas MVC
RUN a2enmod rewrite

# Permitir que Apache lea los archivos secretos de Render.
# Render utiliza el grupo con ID 1000 para estos archivos.
RUN if ! getent group 1000 >/dev/null 2>&1; then \
        groupadd --gid 1000 rendersecrets; \
    fi \
    && SECRET_GROUP="$(getent group 1000 | cut -d: -f1)" \
    && usermod --append --groups "$SECRET_GROUP" www-data

# Configurar Apache para escuchar en el puerto 10000
# y usar public como raíz web
RUN sed -i 's/Listen 80/Listen 10000/' /etc/apache2/ports.conf \
    && sed -i 's/<VirtualHost \*:80>/<VirtualHost *:10000>/' \
    /etc/apache2/sites-available/000-default.conf \
    && sed -i 's#DocumentRoot /var/www/html#DocumentRoot /var/www/html/public#' \
    /etc/apache2/sites-available/000-default.conf

# Permitir las reglas .htaccess dentro de public
RUN printf '\n<Directory /var/www/html/public>\n    AllowOverride All\n    Require all granted\n</Directory>\n' \
    >> /etc/apache2/apache2.conf

# Carpeta del proyecto dentro del contenedor
WORKDIR /var/www/html

# Copiar todo el proyecto: public, app y demás archivos MVC
COPY . /var/www/html/

# Puerto web de Render
EXPOSE 10000

CMD ["apache2-foreground"]
