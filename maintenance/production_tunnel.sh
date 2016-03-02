#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

PORT=22225

ssh -L$PORT:192.168.1.6:22 -p5000 agarb@briop.ru
