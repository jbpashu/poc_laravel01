#!/bin/bash
set -ex

docker-compose run --rm api_php php ./vendor/bin/phpcs
