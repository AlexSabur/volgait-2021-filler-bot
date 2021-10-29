# From official php image.
FROM php:8.0-cli-alpine
# Create a user group and account under id 1000.
RUN addgroup -g 1000 -S user && adduser -u 1000 -D user -G user
# Install quality-of-life packages.
RUN apk add --no-cache bash curl git vim openssh
# Install composer for php deps.
RUN apk add --no-cache composer
# Install postgres pdo driver.
RUN apk add --no-cache postgresql-dev && docker-php-ext-install pdo_pgsql
RUN docker-php-ext-install exif bcmath

# Prevent container from exiting early.
CMD ["sleep", "infinity"]
