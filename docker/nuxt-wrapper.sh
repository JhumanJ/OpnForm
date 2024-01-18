#!/bin/bash

. /root/.nvm/nvm.sh
nvm install 20
nvm use 20

cd /app/nuxt/server/

. /app/client/.env
[ "x$NUXT_API_SECRET" != "x" ] || generate-api-secret.sh

sed 's/^/export /' < /app/.nuxt.env > env.sh

. env.sh

node index.mjs
