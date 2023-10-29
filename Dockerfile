FROM dunglas/frankenphp

# Set the working directory to /app
WORKDIR /app

# Copy the current directory contents into the container at /app
COPY . /app

# Run Composer install
RUN composer install --no-interaction --no-scripts --no-progress
