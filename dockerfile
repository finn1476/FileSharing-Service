# Use an official PHP runtime as a parent image
FROM php:8.3.3-apache

# Set the working directory to /var/www/html
WORKDIR /var/www/html

# Copy the current directory contents into the container at /var/www/html
COPY . /var/www/html

# Install additional dependencies and enable required Apache modules
RUN apt-get update \
    && apt-get install -y libpng-dev libjpeg-dev libzip-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install gd mysqli zip \
    && a2enmod rewrite \
    && service apache2 restart

# Set PHP configuration values
RUN { \
        echo 'max_execution_time=86400'; \
        echo 'max_input_time=86400'; \
        echo 'post_max_size=32000M'; \
        echo 'upload_max_filesize=32000M'; \
    } > /usr/local/etc/php/conf.d/custom.ini

# Set proper permissions for the Files directory
RUN chown -R www-data:www-data /var/www/html/Files \
    && chmod -R 755 /var/www/html/Files

# Set proper permissions for the entire /var/www/html directory
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Set proper permissions for the admin directory
RUN chown -R www-data:www-data /var/www/html/admin \
    && chmod -R 755 /var/www/html/admin

# Set proper permissions for the .htpasswd file
RUN chown www-data:www-data /var/www/html/sicherspeicher/.htpasswd \
    && chmod 644 /var/www/html/sicherspeicher/.htpasswd

# Create an .htpasswd file with an encrypted password
RUN htpasswd -nbB admin admin > /var/www/html/sicherspeicher/.htpasswd

# Expose port 80 for Apache
EXPOSE 80

# Start Apache in the foreground
CMD ["apache2-foreground"]
