#!/bin/bash -e

echo + . /root/.nvm/nvm.sh
. /root/.nvm/nvm.sh

echo + nvm install --no-progress 20
nvm install --no-progress 20
echo + nvm use 20
nvm use 20

cd /app/nuxt/server/

echo + . /app/client/.env
. /app/client/.env

[ "x$NUXT_API_SECRET" != "x" ] || (
  echo + generate-api-secret.sh
    generate-api-secret.sh
)

echo + eval \$\(sed 's/^/export /' \< /app/client/.env\)
eval $(sed 's/^/export /' < /app/client/.env)

echo + node index.mjs
node index.mjs
