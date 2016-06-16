#!/usr/bin/env bash

#set -o pipefail
#set -o errexit
#set -o nounset

APP_ROOT=/vagrant/
BOOTSTRAP_DIR=vagrant/

cd $APP_ROOT

# Restore DB from mybriop.dump in bootstrap directory
sudo -upostgres pg_restore -dpostgres -C $BOOTSTRAP_DIR/mybriop.dump

cd $OLDPWD
