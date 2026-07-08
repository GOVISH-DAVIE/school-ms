FROM php:8.1-apache

# Set working directory
WORKDIR /var/www/html

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    netcat-traditional \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    zip \
    unzip \
    nodejs \
    npm \
    libfreetype6-dev \
    libjpeg62-turbo-dev \
    libwebp-dev \
    libgd-dev \
    libgmp-dev \
    libc-client-dev \
    libkrb5-dev \
    libssl-dev \
    libcurl4-openssl-dev \
    libbz2-dev \
    libreadline-dev \
    libsqlite3-dev \
    libicu-dev \
    libxslt-dev \
    libtidy-dev \
    libffi-dev \
    libsodium-dev \
    libargon2-dev \
    libldap2-dev \
    libsasl2-dev \
    && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-configure imap --with-kerberos --with-imap-ssl \
    && docker-php-ext-configure ldap \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    imap \
    ldap \
    zip \
    bz2 \
    calendar \
    ctype \
    curl \
    dom \
    ffi \
    fileinfo \
    filter \
    ftp \
    gettext \
    gmp \
    hash \
    iconv \
    intl \
    json \
    libxml \
    mysqli \
    opcache \
    openssl \
    pdo \
    pdo_sqlite \
    phar \
    posix \
    readline \
    reflection \
    session \
    shmop \
    simplexml \
    snmp \
    soap \
    sockets \
    spl \
    sqlite3 \
    standard \
    sysvmsg \
    sysvsem \
    sysvshm \
    tidy \
    tokenizer \
    xml \
    xmlreader \
    xmlwriter \
    xsl \
    zip

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Enable Apache modules
RUN a2enmod rewrite

# Set permissions
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Copy application files
COPY . /var/www/html/

# Copy entrypoint script
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

# Configure Apache
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Expose port 80
EXPOSE 80

# Use entrypoint script
ENTRYPOINT ["docker-entrypoint.sh"]
