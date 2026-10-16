FROM php:8.2-cli

# Instala dependências do sistema e extensões do PHP necessárias pro Laravel + SQLite
RUN apt-get update && apt-get install -y \
    unzip \
    libsqlite3-dev \
    git \
    && docker-php-ext-install pdo pdo_sqlite

# Instala o Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Define o diretório de trabalho
WORKDIR /var/www/html

# Copia os arquivos do projeto
COPY . .

# Instala dependências do Laravel sem pacotes de dev
RUN composer install --no-dev --optimize-autoloader

# Gera caches do Laravel
RUN php artisan config:cache \
 && php artisan route:cache \
 && php artisan view:cache

# Porta exposta pelo Render
EXPOSE 10000

# Script de inicialização (cria o banco, roda as migrations, coletores e sobe o servidor)
CMD touch database/database.sqlite \
 && php artisan config:clear \
 && php artisan config:cache \
 && php artisan migrate --force \
 && php artisan jogos:coletar \
 && php artisan classificacao:coletar \
 && php artisan confrontos:coletar \
 && php artisan serve --host=0.0.0.0 --port=${PORT:-10000}
