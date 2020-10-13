# Use the official image as a parent image.
FROM php:7.4-cli

# Copy composer
COPY --from=composer:2.0 /usr/bin/composer /usr/bin/composer

RUN apt-get update
RUN apt-get install zip unzip -y

RUN groupadd -g 1000 appuser && \
    useradd -r -u 1000 -g appuser appuser
RUN mkdir /home/appuser
RUN chown appuser:appuser /home/appuser
RUN mkdir /usr/src/myapp
RUN chown -R appuser:appuser /usr/src/myapp
USER appuser

# Set working directory
WORKDIR /usr/src/myapp
