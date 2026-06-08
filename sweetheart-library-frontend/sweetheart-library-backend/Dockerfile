FROM php:8.2-cli

# Install PostgreSQL extension (needed for Render database)
RUN apt-get update && apt-get install -y libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql

# Copy all your backend files
COPY . /app

WORKDIR /app

# Start PHP built-in server
CMD php -S 0.0.0.0:$PORT -t .