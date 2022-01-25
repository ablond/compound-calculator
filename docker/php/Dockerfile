FROM php:cli-alpine
COPY --from=composer /usr/bin/composer /usr/bin/composer
RUN alias composer='php /usr/bin/composer'
