FROM serversideup/php:8.2-fpm-nginx


### ---------- Lunix packages ------------------------------------------- ###
# # Install Linux packages
# RUN apt-get update && apt-get install -y --no-install-recommends \
#     git \
#     mariadb-client \
#     vim \
#     libicu-dev \
#     wget \
#     libzip-dev \
#     zip \
#     htop \
#     procps \
#     libatk1.0-0 \
#     libxdamage1 \
#     libatk-bridge2.0-0 \
#     libcups2 \
#     libxcomposite1 \
#     libdrm2 \
#     libxkbcommon0 \
#     libxfixes3 \
#     libxrandr2 \
#     libgbm1 \
#     libasound2 \
#     libcairo2 \
#     libpango-1.0-0 \
#     libnss3 \
#     imagemagick \
#     php7.4-imagick
# ### --------------------------------------------------------------------- ###



# Copy composer.json and composer.lock files
COPY ./app/composer.* ./app/

# Install composer dependencies
RUN composer install --no-scripts --no-interaction --no-plugins --no-dev

# Copy project files
COPY . .

# http
EXPOSE 80
# https
EXPOSE 443
# php-fpm
EXPOSE 9000
