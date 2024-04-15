#!/bin/bash -e

echo + . ~nuxt/.nvm/nvm.sh
. ~nuxt/.nvm/nvm.sh

echo + nvm install --no-progress 20
nvm install --no-progress 20
echo + nvm use 20
nvm use 20

cd /app/nuxt/server/

export NUXT_PRIVATE_API_BASE=http://localhost/api

echo + . /app/client/.env
[ -f /app/client/.env ] && . /app/client/.env || echo "Environment file missing!"

[ "x$NUXT_API_SECRET" != "x" ] || (
  echo + generate-api-secret.sh
    generate-api-secret.sh
)

echo + eval \$\(sed 's/^/export /' \< /app/client/.env\)
eval $(sed 's/^/export /' < /app/client/.env)

echo + node index.mjs
node index.mjs
