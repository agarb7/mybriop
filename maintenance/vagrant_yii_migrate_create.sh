#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

APP_ROOT=/vagrant/

vagrant ssh -- sudo ${APP_ROOT}/yii migrate/create --interactive=0 $1
