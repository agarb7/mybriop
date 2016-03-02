#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

if [ ! -z ${1:-} ] && [ ${1:-} = '-f' ]; then
    IF_EXISTS='if exists'
else
    IF_EXISTS=''
fi

# Drop vagrant database
sudo -upostgres psql -c"drop database ${IF_EXISTS} mybriop"
