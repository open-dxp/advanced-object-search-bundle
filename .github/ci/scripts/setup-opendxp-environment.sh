#!/bin/bash

set -eu -o xtrace

#cp -rv .github/ci/files/var .
cp .github/ci/files/.env .

# Setup composer auth
if [ -n "$COMPOSER_AUTH" ]; then
composer config repositories.opendxp '{"type": "composer", "url": "https://open-dxp.repo.repman.io"}' --file composer.json
fi
