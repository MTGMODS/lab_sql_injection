FROM php:8.2-cli

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www
COPY start.sh /var/www/start.sh
COPY vulnerable /var/www/vulnerable
COPY secure /var/www/secure

RUN mkdir -p /var/www/vulnerable/data /var/www/secure/data \
    && chmod +x /var/www/start.sh \
    && chmod -R 777 /var/www/vulnerable/data /var/www/secure/data \
    && sed -i 's/\r$//' /var/www/start.sh

EXPOSE 8080 8081
CMD ["/var/www/start.sh"]
