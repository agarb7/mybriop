#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

APP_ROOT=/vagrant/
BOOTSTRAP_DIR=vagrant/guest/

cd $APP_ROOT

# Drop DB if exists
$BOOTSTRAP_DIR/dropdb.sh -f

# Create DB user (drop it if exists)
sudo -upostgres psql -c "drop role if exists mybriop; create role mybriop login encrypted password 'mybriop' nosuperuser inherit nocreatedb nocreaterole noreplication"

# Create or restore DB
if [ ! -f $BOOTSTRAP_DIR/mybriop.dump ]
then # Create DB
    sudo -upostgres createdb -O mybriop -T template0 -E utf8 -l ru_RU.utf-8 mybriop
else # Restore DB
    $BOOTSTRAP_DIR/restoredb.sh
fi

# Install admin pack if not exists
sudo -upostgres psql -dmybriop -c'create extension if not exists adminpack'

# Apply migrations
./yii migrate --interactive=0

cd $OLDPWD
