#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

PORT=22225

# You need:
# ssh-copy-id agarb@briop.ru

screen -d -m ssh -L$PORT:192.168.1.6:22 agarb@briop.ru
