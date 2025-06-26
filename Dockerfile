FROM php:8.3-cli

RUN apt-get update && apt-get install -y \
     unzip \
     git \
     zip \
     libzip-dev \
     libxml2-dev \
     libonig-dev \
     && docker-php-ext-install zip mbstring dom


COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

COPY . .

ENTRYPOINT ["sh", "entry.sh"]
