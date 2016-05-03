#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

APP_ROOT=/vagrant/
BOOTSTRAP_DIR=vagrant/

vagrant ssh -- sudo ${APP_ROOT}${BOOTSTRAP_DIR}/resetdb.sh
