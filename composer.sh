#!/bin/bash
set -ex

docker-compose run --rm api_composer composer "$@" \
  --no-plugins \
  --no-scripts \
  --no-interaction
