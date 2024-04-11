FROM serversideup/php:8.2-fpm-nginx

# ENV AUTORUN_LARAVEL_MIGRATION=true
ENV SSL_MODE=off
ENV APP_ENV=production

# Install postgress extensions
RUN apt-get update && apt-get install -y php8.2-pgsql

# Copy composer.json and composer.lock files
COPY --chown=$PUID:$PGID ./composer.* ./

# Install composer dependencies
RUN composer install --no-scripts --no-interaction --no-plugins --no-dev

# Copy project files
COPY --chown=$PUID:$PGID . .

# http
EXPOSE 80
# https
EXPOSE 443
# php-fpm
EXPOSE 9000
