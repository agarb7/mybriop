#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

APP_ROOT=/vagrant/
BOOTSTRAP_DIR=vagrant/

cd $APP_ROOT

# Dump DB to mybriop.dump in bootstrap directory
sudo -upostgres pg_dump -Fc mybriop > $BOOTSTRAP_DIR/mybriop.dump

cd $OLDPWD
