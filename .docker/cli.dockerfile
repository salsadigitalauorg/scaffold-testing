# CLI container.
#
# All CLI operations performed in this container.
#
# hadolint global ignore=DL3018
#
# @see https://hub.docker.com/r/uselagoon/php-8.2-cli-drupal/tags
# @see https://github.com/uselagoon/lagoon-images/tree/main/images/php-cli-drupal
FROM uselagoon/php-8.2-cli-drupal:24.8.0

# Add missing variables.
# @todo Remove once https://github.com/uselagoon/lagoon/issues/3121 is resolved.
ARG LAGOON_PR_HEAD_BRANCH=""
ENV LAGOON_PR_HEAD_BRANCH=${LAGOON_PR_HEAD_BRANCH}
ARG LAGOON_PR_HEAD_SHA=""
ENV LAGOON_PR_HEAD_SHA=${LAGOON_PR_HEAD_SHA}

# Webroot is used for Drush aliases.
ARG WEBROOT=web

ARG GITHUB_TOKEN=""
ENV GITHUB_TOKEN=${GITHUB_TOKEN}

# Set default values for environment variables.
# These values will be overridden if set in docker-compose.yml or .env file
# during build stage.
ENV WEBROOT=${WEBROOT} \
    COMPOSER_ALLOW_SUPERUSER=1 \
    COMPOSER_CACHE_DIR=/tmp/.composer/cache \
    SIMPLETEST_DB=mysql://drupal:drupal@mariadb/drupal \
    SIMPLETEST_BASE_URL=http://nginx:8080 \
    SYMFONY_DEPRECATIONS_HELPER=disabled

# Strating from this line, Docker will add result of each command into a
# separate layer. These layers are then cached and re-used when the project is
# rebuilt.
# Note that layers are only rebuilt if files added into the image with `ADD`
# have changed since the last build. So, adding files that are unlikely to
# change earlier in the build process (closer to the start of this file)
# reduce build time.

# Adding more tools.
RUN apk add --no-cache ncurses pv tzdata

# Adding patches and scripts.
COPY patches /app/patches
COPY scripts /app/scripts
COPY .docker/drush /app/.docker/drush

# Copy files required for PHP dependencies resolution.
# Note that composer.lock is not explicitly copied, allowing to run the stack
# without existing lock file (this is not advisable, but allows to build
# using latest versions of packages). composer.lock should be comitted to the
# repository.
# File .env (and other environment files) is copied into image as it may be
# required by composer scripts to get some additions variables.
COPY composer.json composer.* .env* auth* /app/

# Install PHP dependencies without including development dependencies. This is
# crucial as it prevents potential security vulnerabilities from being exposed
# to the production environment.
RUN if [ -n "${GITHUB_TOKEN}" ]; then export COMPOSER_AUTH="{\"github-oauth\": {\"github.com\": \"${GITHUB_TOKEN}\"}}"; fi && \
    COMPOSER_MEMORY_LIMIT=-1 composer install -n --no-dev --ansi --prefer-dist --optimize-autoloader

# Remove Drush launcher installed by the base Lagoon PHP image.
# @see https://github.com/uselagoon/lagoon-images/blob/main/images/php-cli-drupal/8.2.Dockerfile#L19
RUN rm -rf /usr/local/bin/drush
ENV PATH="/app/vendor/bin:${PATH}"

# Copy all files into appllication source directory. Existing files are always
# overridden.
COPY . /app

# Create files directory and set correct permissions.
RUN mkdir -p /app/${WEBROOT}/sites/default/files && chmod 0770 /app/${WEBROOT}/sites/default/files && \
  mkdir -p /app/.logs/test_results && chmod 0770 /app/.logs/test_results

WORKDIR /app

# Install ahoy for wrapped commands
RUN curl -L "https://github.com/ahoy-cli/ahoy/releases/download/2.0.0/ahoy-bin-linux-amd64" -o /usr/local/bin/ahoy &&\
    chmod +x /usr/local/bin/ahoy

RUN chmod -f 0550 /app/web/sites/default \
  && chmod -f 0440 /app/web/sites/default/settings.php \
  && chmod -f 0440 /app/web/sites/default/services.yml
