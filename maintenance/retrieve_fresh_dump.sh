#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

HOST=127.0.0.1
PORT=22225

SERVER_DUMPS_DIR=/home/mybriop/dumps
PROJECT_DUMPS_DIR=vagrant/

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && git rev-parse --show-toplevel )"

# create dump on server
ssh -p$PORT mybriop@$HOST "mkdir -p \"$SERVER_DUMPS_DIR\"; sudo -upostgres pg_dump -Fc mybriop > \"$SERVER_DUMPS_DIR/mybriop.dump\""

# retrieve dump
scp -P$PORT mybriop@$HOST:"$SERVER_DUMPS_DIR/mybriop.dump" "$PROJECT_ROOT/$PROJECT_DUMPS_DIR"
