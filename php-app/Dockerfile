# Use the official PHP image
FROM php:8.2-apache

RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Install PDO MySQL extension
#RUN docker-php-ext-install pdo pdo_mysql

# Set the working directory
WORKDIR /var/www/html
RUN echo "DirectoryIndex index.html index.php" > /etc/apache2/conf-enabled/directoryindex.conf
# Copy the application code
#COPY index.php .

# Expose port 80
EXPOSE 80