#!/bin/bash
set -ex

TAG=${1:-latest}
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
REPO_DIR="${DIR}/../.."

PHP_REPO="spherewms/v3-api-php"
NGINX_REPO="spherewms/v3-api-nginx"

COMPOSER_TAG="${PHP_REPO}:composer"
FRONTEND_TAG="${PHP_REPO}:frontend"
PHP_TAG="${PHP_REPO}:${TAG}"
NGINX_TAG="${NGINX_REPO}:${TAG}"

# pull images for caches
docker pull ${COMPOSER_TAG} || true
docker pull ${FRONTEND_TAG} || true
docker pull "${PHP_REPO}:latest" || true
docker pull "${NGINX_REPO}:latest" || true

# build images per stage
docker build -t ${COMPOSER_TAG} \
   --cache-from ${COMPOSER_TAG} \
   --target composer $REPO_DIR

docker push ${COMPOSER_TAG}

docker build -t ${FRONTEND_TAG} \
   --cache-from ${COMPOSER_TAG} \
   --cache-from ${FRONTEND_TAG} \
   --target frontend $REPO_DIR

docker push ${FRONTEND_TAG}

docker build -t ${PHP_TAG} \
   -t "${PHP_REPO}:latest" \
   --cache-from ${COMPOSER_TAG} \
   --cache-from ${FRONTEND_TAG} \
   --cache-from "${PHP_REPO}:latest" \
   --target php $REPO_DIR

docker push ${PHP_TAG}
docker push "${PHP_REPO}:latest"

docker build -t ${NGINX_TAG} \
   -t  "${NGINX_REPO}:latest" \
   --cache-from ${COMPOSER_TAG} \
   --cache-from ${FRONTEND_TAG} \
   --cache-from "${PHP_REPO}:latest" \
   --cache-from "${NGINX_REPO}:latest" \
   --target nginx $REPO_DIR

docker push ${NGINX_TAG}
docker push "${NGINX_REPO}:latest"
