#!/usr/bin/env bash

set -o pipefail
set -o errexit
set -o nounset

vagrant plugin install vagrant-vbguest
vagrant plugin install vagrant-vbox-snapshot
