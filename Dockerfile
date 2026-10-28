FROM php:8.4-fpm-alpine

# Instala pacotes do sistema necessários e dependências do SQLite
RUN apk update && apk add --no-cache \
    shadow \
    sqlite-dev \
    sqlite \
    tzdata \
    zip \
    unzip \
    libzip-dev \
    bash \
    curl \
    freetype-dev \
    libjpeg-turbo-dev \
    libpng-dev

# Configura o fuso horário
ENV TZ=America/Sao_Paulo
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone

# Configura e instala extensões do PHP (incluindo GD para phpspreadsheet)
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install pdo pdo_sqlite zip gd

# Copia o binário do Composer para dentro da imagem
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Ajusta as permissões padrão do www-data para evitar problemas de leitura/escrita no SQLite
RUN usermod -u 1000 www-data && groupmod -g 1000 www-data

WORKDIR /var/www/html

# Ajusta permissões do diretório de trabalho
RUN chown -R www-data:www-data /var/www/html

USER www-data
