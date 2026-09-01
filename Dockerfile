FROM dunglas/frankenphp:php8.4

RUN install-php-extensions mysqli pdo_mysql

WORKDIR /app

COPY . /app

CMD ["sh", "-c", "frankenphp php-server --listen :$PORT --root /app"]
