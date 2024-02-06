#!/bin/bash

. /root/.nvm/nvm.sh
nvm install 20
nvm use 20

cd /app/nuxt/server/

. /app/client/.env

[ "x$NUXT_API_SECRET" != "x" ] || generate-api-secret.sh

eval $(sed 's/^/export /' < /app/client/.env)

node index.mjs
