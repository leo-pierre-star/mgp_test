FROM php:8.4-cli

# Install PostgreSQL driver (pdo_pgsql) with minimal packages
RUN apt-get update \
    && apt-get install -y --no-install-recommends libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /app

# (Dependencies and source code are mounted via volume in docker compose)

CMD ["php", "-S", "0.0.0.0:8000", "-t", "public"]
