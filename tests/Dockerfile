# Используем официальный образ PHP CLI
FROM php:8.1.32-cli

# Установка зависимостей
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git \
        ca-certificates \
        zip \
        unzip \
        libzip-dev \
    && docker-php-ext-install zip \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем рабочую директорию
WORKDIR /app
