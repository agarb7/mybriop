#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

[ ! -z ${1:-} ] && HOST_PORT=$1 || HOST_PORT=54320

screen -d -m vagrant ssh -- -T -L${HOST_PORT}:127.0.0.1:5432
