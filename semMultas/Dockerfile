# Use PHP 8.2 CLI Alpine para uma imagem menor
# CLI é necessário para php artisan serve
FROM php:8.2-cli-alpine

# Instalar dependências do sistema
RUN apk add --no-cache \
    git \
    curl \
    libpng-dev \
    libzip-dev \
    zip \
    unzip \
    oniguruma-dev \
    postgresql-dev \
    mysql-client \
    nodejs \
    npm \
    && docker-php-ext-install \
    pdo \
    pdo_mysql \
    pdo_pgsql \
    mbstring \
    exif \
    pcntl \
    bcmath \
    gd \
    zip \
    opcache

# Instalar Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Definir diretório de trabalho
WORKDIR /var/www/html

# Copiar arquivos do projeto
COPY . .

# Instalar dependências do Composer (sem dev dependencies para produção)
RUN composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist

# Configurar permissões
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

# Configurar PHP para produção
RUN echo "memory_limit=256M" > /usr/local/etc/php/conf.d/memory.ini \
    && echo "upload_max_filesize=50M" > /usr/local/etc/php/conf.d/uploads.ini \
    && echo "post_max_size=50M" >> /usr/local/etc/php/conf.d/uploads.ini

# Expor porta (Render define via variável de ambiente PORT)
EXPOSE ${PORT:-8000}

# Script de inicialização
COPY docker-entrypoint.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]

# Comando padrão para iniciar o servidor PHP
# Render define a variável PORT automaticamente
# Usamos um script wrapper para lidar com a variável PORT
COPY docker-start.sh /usr/local/bin/
RUN chmod +x /usr/local/bin/docker-start.sh

CMD ["docker-start.sh"]

