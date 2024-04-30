FROM php:8.0-apache

# Set the working directory
WORKDIR /var/www/html

# Copy the application files to the container
COPY . .

# Install dependencies (if any)
RUN apt-get update && \
    apt-get install -y \ 
    libpng-dev \ 
    default-libmysqlclient-dev \ 
    libzip-dev \
    unzip \
    zip \
    git && \
    apt-get clean && \
    rm -rf /var/lib/apt/lists/* && \
    docker-php-ext-install pdo pdo_mysql gd mysqli bcmath

# Expose the necessary ports
EXPOSE 80

# Install Composer 
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer install

# Define the command to run your application
CMD ["apache2-foreground"]