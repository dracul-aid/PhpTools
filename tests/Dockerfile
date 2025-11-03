# Используем официальный образ PHP CLI
FROM php:8.1.32-cli

# Установка git и других утилит
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        git \
        ca-certificates \
    && rm -rf /var/lib/apt/lists/*

# Устанавливаем рабочую директорию
WORKDIR /app
