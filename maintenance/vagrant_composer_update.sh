#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

APP_ROOT=/vagrant/

vagrant ssh -- "cd ${APP_ROOT} && sudo composer update"
